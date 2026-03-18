import Redis from "ioredis";
import "dotenv/config";

const redisConfig = {
    host: process.env.REDIS_HOST || "127.0.0.1",
    port: parseInt(process.env.REDIS_PORT) || 6379,
    password: process.env.REDIS_PASSWORD || undefined,
    retryStrategy(times) {
        return Math.min(times * 100, 2000);
    },
};

// General purpose client — velocity counters, cache
export const redisClient = new Redis(redisConfig);

// Dedicated publish client
export const publisherClient = new Redis(redisConfig);

// Dedicated subscribe client — must be separate, cannot run other commands
export const subscriberClient = new Redis(redisConfig);

redisClient.on("connect", () => console.log("[Redis] Connected"));
redisClient.on("error", (err) => console.error("[Redis] Error:", err.message));

publisherClient.on("error", (err) =>
    console.error("[Redis Publisher] Error:", err.message),
);
subscriberClient.on("error", (err) =>
    console.error("[Redis Subscriber] Error:", err.message),
);
