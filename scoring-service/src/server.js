import "dotenv/config";
import express from "express";
import { scoreTransaction } from "./scorer.js";
import { publishTransactionEvent } from "./publisher.js";
import { redisClient } from "./config/redis.js";

const app = express();
const PORT = process.env.PORT || 3001;

app.use(express.json());

// ── Request logging middleware ────────────────────────────────────────────────
app.use((req, res, next) => {
    const start = Date.now();
    res.on("finish", () => {
        const duration = Date.now() - start;
        console.log(
            `[${new Date().toISOString()}] ${req.method} ${req.path} ${res.statusCode} ${duration}ms`,
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
            service: "chargeback-shield-scoring",
            redis: "disconnected",
            error: err.message,
        });
    }
});

// ── Score endpoint ────────────────────────────────────────────────────────────
app.post("/score", async (req, res) => {
    const data = req.body;

    // Basic validation
    if (!data.card_bin || !data.amount) {
        return res.status(400).json({
            error: "card_bin and amount are required",
        });
    }

    try {
        const result = await scoreTransaction(data);

        // Publish to Redis pub/sub for live dashboard feed
        // Fire and forget — don't await
        publishTransactionEvent(data, result).catch(console.error);

        return res.json(result);
    } catch (err) {
        console.error("[Score] Unhandled error:", err);
        return res.status(500).json({
            error: "Scoring failed",
            message: err.message,
        });
    }
});

// ── Velocity reset (useful for demo/testing) ──────────────────────────────────
app.delete("/velocity/:cardBin/:cardLast4", async (req, res) => {
    const { cardBin, cardLast4 } = req.params;
    const pattern = `vel:card:${cardBin}${cardLast4}:*`;

    try {
        const keys = await redisClient.keys(pattern);
        if (keys.length > 0) {
            await redisClient.del(...keys);
        }
        res.json({ deleted: keys.length, pattern });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

// ── Start ─────────────────────────────────────────────────────────────────────
app.listen(PORT, () => {
    console.log(`
╔════════════════════════════════════════════╗
║   Chargeback Shield — Scoring Service      ║
║   Running on port ${PORT}                     ║
║   Redis: ${process.env.REDIS_HOST}:${process.env.REDIS_PORT}               ║
╚════════════════════════════════════════════╝
  `);
});
