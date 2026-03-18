import Redis from "ioredis";
import "dotenv/config";

/**
 * We create two Redis clients:
 * - redisClient: used for velocity counters and general data
 * - publisherClient: dedicated to pub/sub publishing
 *
 * ioredis requires separate client instances for pub/sub.
 */

const redisConfig = {
    host: process.env.REDIS_HOST || "127.0.0.1",
    port: parseInt(process.env.REDIS_PORT) || 6379,
    password: process.env.REDIS_PASSWORD || undefined,
    retryStrategy(times) {
        const delay = Math.min(times * 100, 2000);
        return delay;
    },
};

export const redisClient = new Redis(redisConfig);
export const publisherClient = new Redis(redisConfig);

redisClient.on("connect", () => console.log("[Redis] Connected"));
redisClient.on("error", (err) => console.error("[Redis] Error:", err.message));
publisherClient.on("error", (err) =>
    console.error("[Redis Publisher] Error:", err.message),
);
