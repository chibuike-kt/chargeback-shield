import { WebSocketServer, WebSocket } from "ws";
import { subscriberClient } from "./config/redis.js";
import "dotenv/config";

const CHANNEL = process.env.PUBSUB_CHANNEL || "transactions.live";

const clients = new Map();

function addClient(merchantId, ws) {
    if (!clients.has(merchantId)) {
        clients.set(merchantId, new Set());
    }
    clients.get(merchantId).add(ws);
}

function removeClient(merchantId, ws) {
    if (clients.has(merchantId)) {
        clients.get(merchantId).delete(ws);
    }
}

function broadcast(merchantId, data) {
    const targets = [
        ...(clients.get(merchantId) ?? []),
        ...(clients.get("all") ?? []),
    ];

    const message = JSON.stringify(data);

    for (const ws of targets) {
        if (ws.readyState === WebSocket.OPEN) {
            try {
                ws.send(message);
            } catch (err) {
                console.error("[WS] Send error:", err.message);
            }
        }
    }
}

function startRedisSubscriber() {
    subscriberClient.on('message', (channel, message) => {
        if (channel !== CHANNEL) return;

        try {
            const event = JSON.parse(message);
            console.log('[Redis Subscriber] Event received:', event.decision, event.card_bin);

            const enriched = {
                type:             'transaction',
                transaction_id:   event.transaction_id,
                merchant_id:      event.merchant_id,
                card_bin:         event.card_bin,
                card_last4:       event.card_last4,
                amount:           event.amount,
                currency:         event.currency || 'NGN',
                decision:         event.decision,
                risk_score:       event.risk_score,
                risk_level:       event.risk_level,
                ip_country:       event.ip_country,
                card_country:     event.card_country,
                duration_ms:      event.duration_ms,
                scored_at:        event.scored_at,
                formatted_amount: formatAmount(event.amount, event.currency),
                decision_label:   decisionLabel(event.decision),
                risk_color:       riskColor(event.risk_score),
            };

            broadcast(String(event.merchant_id ?? 'all'), enriched);
            broadcast('all', enriched);

        } catch (err) {
            console.error('[Redis Subscriber] Parse error:', err.message);
        }
    });

    subscriberClient.on('error', (err) => {
        console.error('[Redis Subscriber] Error:', err.message);
    });

    // Subscribe directly — don't wait for connect event
    subscriberClient.subscribe(CHANNEL, (err, count) => {
        if (err) {
            console.error('[Redis Subscriber] Subscribe error:', err.message);
            return;
        }
        console.log(`[Redis Subscriber] Subscribed to: ${CHANNEL} (${count} channels)`);
    });
}

export function createWebSocketServer(httpServer) {
    const wss = new WebSocketServer({ server: httpServer, path: "/ws" });

    console.log("[WS] WebSocket server attached to /ws");

    wss.on("connection", (ws) => {
        let merchantId = "all";

        console.log(`[WS] Client connected (${wss.clients.size} total)`);

        addClient(merchantId, ws);

        ws.send(
            JSON.stringify({
                type: "connected",
                message: "Chargeback Shield live feed connected",
                timestamp: new Date().toISOString(),
            }),
        );

        ws.on("message", (raw) => {
            try {
                const msg = JSON.parse(raw.toString());
                if (msg.type === "identify" && msg.merchant_id) {
                    removeClient(merchantId, ws);
                    merchantId = msg.merchant_id;
                    addClient(merchantId, ws);
                    console.log(
                        `[WS] Client identified as merchant: ${merchantId}`,
                    );
                    ws.send(
                        JSON.stringify({
                            type: "identified",
                            merchant_id: merchantId,
                            timestamp: new Date().toISOString(),
                        }),
                    );
                }
            } catch {}
        });

        ws.on("close", () => {
            removeClient(merchantId, ws);
            console.log(
                `[WS] Client disconnected (${wss.clients.size} remaining)`,
            );
        });

        ws.on("error", (err) => {
            console.error("[WS] Client error:", err.message);
            removeClient(merchantId, ws);
        });
    });

    startRedisSubscriber();

    return wss;
}

function formatAmount(amount, currency = "NGN") {
    return `${currency} ${(amount / 100).toLocaleString("en-NG", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })}`;
}

function decisionLabel(decision) {
    return (
        { allow: "Approved", step_up: "Step-Up", decline: "Declined" }[
            decision
        ] ?? decision
    );
}

function riskColor(score) {
    if (score < 0.4) return "green";
    if (score < 0.7) return "amber";
    return "red";
}
