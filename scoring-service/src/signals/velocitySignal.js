import { redisClient } from "../config/redis.js";

/**
 * Velocity signal using Redis sorted sets as sliding windows.
 *
 * For each dimension we track, the key is a sorted set where:
 * - member = a unique identifier (transaction ID or value)
 * - score  = Unix timestamp in milliseconds
 *
 * To count events in the last N seconds:
 * 1. ZADD the current event with timestamp as score
 * 2. ZREMRANGEBYSCORE to remove entries older than the window
 * 3. ZCARD to count remaining members
 *
 * This gives an exact sliding window with no drift.
 */

const WINDOWS = {
    TX_PER_HOUR: 60 * 60, // 1 hour in seconds
    SPEND_PER_24H: 24 * 60 * 60, // 24 hours
    UNIQUE_MERCHANTS_24H: 24 * 60 * 60,
    UNIQUE_COUNTRIES_24H: 24 * 60 * 60,
};

const TTL_BUFFER = 60; // extra seconds before key expires

/**
 * Score thresholds — tune these for your risk appetite.
 */
const THRESHOLDS = {
    TX_PER_HOUR: { low: 3, medium: 8, high: 15 },
    SPEND_PER_24H: { low: 5_000_000, medium: 20_000_000, high: 50_000_000 }, // kobo
    UNIQUE_MERCHANTS_24H: { low: 2, medium: 5, high: 10 },
    UNIQUE_COUNTRIES_24H: { low: 1, medium: 2, high: 3 },
};

function scoreFromThreshold(value, thresholds) {
    if (value <= thresholds.low) return 0.05;
    if (value <= thresholds.medium) return 0.4;
    if (value <= thresholds.high) return 0.7;
    return 0.95;
}

async function slidingWindowCount(key, windowSeconds, member, score) {
    const now = Date.now();
    const windowMs = windowSeconds * 1000;
    const cutoff = now - windowMs;

    const pipeline = redisClient.pipeline();
    pipeline.zadd(key, now, `${member}:${now}`);
    pipeline.zremrangebyscore(key, "-inf", cutoff);
    pipeline.zcard(key);
    pipeline.expire(key, windowSeconds + TTL_BUFFER);

    const results = await pipeline.exec();
    // zcard result is at index 2
    return results[2][1];
}

async function slidingWindowSum(key, windowSeconds, member, value) {
    const now = Date.now();
    const windowMs = windowSeconds * 1000;
    const cutoff = now - windowMs;

    // For spend sum: store value in member, use timestamp as score
    const pipeline = redisClient.pipeline();
    pipeline.zadd(key, now, `${value}:${now}`);
    pipeline.zremrangebyscore(key, "-inf", cutoff);
    // Get all remaining members to sum their values
    pipeline.zrange(key, 0, -1);
    pipeline.expire(key, windowSeconds + TTL_BUFFER);

    const results = await pipeline.exec();
    const members = results[2][1]; // array of "value:timestamp" strings

    return members.reduce((sum, m) => {
        const amount = parseFloat(m.split(":")[0]);
        return sum + (isNaN(amount) ? 0 : amount);
    }, 0);
}

async function slidingWindowUnique(key, windowSeconds, value) {
    const now = Date.now();
    const windowMs = windowSeconds * 1000;
    const cutoff = now - windowMs;

    const pipeline = redisClient.pipeline();
    // Use the value itself as member so duplicates are auto-deduplicated
    pipeline.zadd(key, now, String(value));
    pipeline.zremrangebyscore(key, "-inf", cutoff);
    pipeline.zcard(key);
    pipeline.expire(key, windowSeconds + TTL_BUFFER);

    const results = await pipeline.exec();
    return results[2][1];
}

export async function computeVelocitySignal(data) {
    const cardKey = `vel:card:${data.card_bin}${data.card_last4}`;
    const deviceKey = `vel:device:${data.device_fingerprint || "unknown"}`;

    try {
        const [txCount, totalSpend, uniqueMerchants, uniqueCountries] =
            await Promise.all([
                // Transactions per hour on this card
                slidingWindowCount(
                    `${cardKey}:tx_hour`,
                    WINDOWS.TX_PER_HOUR,
                    data.idempotency_key || Date.now(),
                    Date.now(),
                ),

                // Total spend in last 24h on this card
                slidingWindowSum(
                    `${cardKey}:spend_24h`,
                    WINDOWS.SPEND_PER_24H,
                    data.amount || 0,
                    Date.now(),
                ),

                // Unique merchant categories in 24h on this device
                slidingWindowUnique(
                    `${deviceKey}:merchants_24h`,
                    WINDOWS.UNIQUE_MERCHANTS_24H,
                    data.merchant_category || "unknown",
                ),

                // Unique countries in 24h on this card
                slidingWindowUnique(
                    `${cardKey}:countries_24h`,
                    WINDOWS.UNIQUE_COUNTRIES_24H,
                    data.ip_country || "unknown",
                ),
            ]);

        // Score each dimension
        const txScore = scoreFromThreshold(txCount, THRESHOLDS.TX_PER_HOUR);
        const spendScore = scoreFromThreshold(
            totalSpend,
            THRESHOLDS.SPEND_PER_24H,
        );
        const merchantScore = scoreFromThreshold(
            uniqueMerchants,
            THRESHOLDS.UNIQUE_MERCHANTS_24H,
        );
        const countryScore = scoreFromThreshold(
            uniqueCountries,
            THRESHOLDS.UNIQUE_COUNTRIES_24H,
        );

        // Composite velocity score = weighted average of dimensions
        const compositeScore =
            txScore * 0.4 +
            spendScore * 0.3 +
            merchantScore * 0.15 +
            countryScore * 0.15;

        return {
            signal_name: "velocity",
            raw_value: `tx_hour:${txCount} spend_24h:${Math.round(totalSpend / 100)} merchants:${uniqueMerchants} countries:${uniqueCountries}`,
            normalized_score: parseFloat(compositeScore.toFixed(4)),
            weight: 0.25,
            weighted_contribution: parseFloat(
                (compositeScore * 0.25).toFixed(4),
            ),
            breakdown: {
                tx_count_last_hour: txCount,
                total_spend_last_24h: Math.round(totalSpend / 100), // convert to major units
                unique_merchants_last_24h: uniqueMerchants,
                unique_countries_last_24h: uniqueCountries,
            },
        };
    } catch (err) {
        console.error("[VelocitySignal] Redis error:", err.message);
        // Safe fallback — don't let Redis failure break scoring
        return {
            signal_name: "velocity",
            raw_value: "redis_error",
            normalized_score: 0.1,
            weight: 0.25,
            weighted_contribution: 0.025,
            breakdown: {},
        };
    }
}
