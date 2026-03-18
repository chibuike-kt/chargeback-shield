import "dotenv/config";
import http from "http";
import express from "express";
import { scoreTransaction } from "./scorer.js";
import { publishTransactionEvent } from "./publisher.js";
import { redisClient } from "./config/redis.js";
import { createWebSocketServer } from "./websocket.js";

const app = express();
const PORT = process.env.PORT || 3001;

app.use(express.json());

// ── Request logging ───────────────────────────────────────────────────────────
app.use((req, res, next) => {
    const start = Date.now();
    res.on("finish", () => {
        console.log(
            `[${new Date().toISOString()}] ${req.method} ${req.path} ` +
                `${res.statusCode} ${Date.now() - start}ms`,
        );
    });
    next();
});

// ── Health check ──────────────────────────────────────────────────────────────
app.get("/health", async (req, res) => {
    try {
        await redisClient.ping();
        res.json({
            status: "ok",
            service: "chargeback-shield-scoring",
            redis: "connected",
            timestamp: new Date().toISOString(),
        });
    } catch (err) {
        res.status(503).json({
            status: "degraded",
            redis: "disconnected",
            error: err.message,
        });
    }
});

// ── Score endpoint ────────────────────────────────────────────────────────────
app.post("/score", async (req, res) => {
    const data = req.body;

    if (!data.card_bin || !data.amount) {
        return res.status(400).json({
            error: "card_bin and amount are required",
        });
    }

    try {
        const result = await scoreTransaction(data);
        publishTransactionEvent(data, result).catch(console.error);
        return res.json(result);
    } catch (err) {
        console.error("[Score] Error:", err);
        return res
            .status(500)
            .json({ error: "Scoring failed", message: err.message });
    }
});

// ── Velocity reset ────────────────────────────────────────────────────────────
app.delete("/velocity/:cardBin/:cardLast4", async (req, res) => {
    const { cardBin, cardLast4 } = req.params;
    const pattern = `vel:card:${cardBin}${cardLast4}:*`;
    try {
        const keys = await redisClient.keys(pattern);
        if (keys.length > 0) await redisClient.del(...keys);
        res.json({ deleted: keys.length, pattern });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// ── Create HTTP server and attach WebSocket ───────────────────────────────────
const server = http.createServer(app);
createWebSocketServer(server);

server.listen(PORT, () => {
    console.log(`
╔════════════════════════════════════════════╗
║   Chargeback Shield — Scoring Service      ║
║   HTTP  : http://localhost:${PORT}             ║
║   WS    : ws://localhost:${PORT}/ws            ║
║   Redis : ${process.env.REDIS_HOST}:${process.env.REDIS_PORT}               ║
╚════════════════════════════════════════════╝
    `);
});
