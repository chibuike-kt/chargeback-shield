import { publisherClient } from "./config/redis.js";

const CHANNEL = process.env.PUBSUB_CHANNEL || "transactions.live";

/**
 * Publish a scored transaction event to Redis pub/sub.
 * Laravel (or a WebSocket bridge) subscribes to this channel
 * and pushes events to the merchant dashboard in real time.
 */
export async function publishTransactionEvent(data, scoringResult) {
    const event = {
        type: "transaction.scored",
        transaction_id: data.idempotency_key,
        merchant_id: data.merchant_id,
        card_bin: data.card_bin,
        card_last4: data.card_last4,
        amount: data.amount,
        currency: data.currency || "NGN",
        decision: scoringResult.decision,
        risk_score: scoringResult.score,
        risk_level: scoringResult.risk_level,
        ip_country: data.ip_country,
        card_country: data.card_country,
        scored_at: scoringResult.scored_at,
        duration_ms: scoringResult.duration_ms,
    };

    try {
        await publisherClient.publish(CHANNEL, JSON.stringify(event));
    } catch (err) {
        // Non-fatal — scoring should not fail because pub/sub fails
        console.error("[Publisher] Failed to publish event:", err.message);
    }
}
