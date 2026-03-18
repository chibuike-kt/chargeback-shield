import { computeVelocitySignal } from "./signals/velocitySignal.js";
import { computeGeoSignal } from "./signals/geoSignal.js";
import { computeDeviceSignal } from "./signals/deviceSignal.js";
import { computeSessionSignal } from "./signals/sessionSignal.js";
import { computeAmountSignal } from "./signals/amountSignal.js";
import { computeBinSignal } from "./signals/binSignal.js";

/**
 * Signal weights — must sum to 1.0
 *
 * velocity:           0.25  (strongest real-time signal)
 * geo_mismatch:       0.20  (card vs IP country)
 * bin_risk:           0.20  (BIN table lookup)
 * device_fingerprint: 0.15  (device identity)
 * session_age:        0.10  (session freshness)
 * amount_risk:        0.10  (transaction size)
 * ─────────────────────────
 * total:              1.00
 */

function getRiskLevel(score) {
    if (score < 0.4) return "low";
    if (score < 0.7) return "medium";
    return "high";
}

function getDecision(score) {
    if (score < 0.4) return "allow";
    if (score < 0.7) return "step_up";
    return "decline";
}

export async function scoreTransaction(data) {
    const startTime = Date.now();

    // Run all signals — velocity is async (Redis), others are sync
    const [
        velocitySignal,
        geoSignal,
        deviceSignal,
        sessionSignal,
        amountSignal,
        binSignal,
    ] = await Promise.all([
        computeVelocitySignal(data),
        Promise.resolve(computeGeoSignal(data)),
        Promise.resolve(computeDeviceSignal(data)),
        Promise.resolve(computeSessionSignal(data)),
        Promise.resolve(computeAmountSignal(data)),
        Promise.resolve(computeBinSignal(data)),
    ]);

    const signals = [
        velocitySignal,
        geoSignal,
        deviceSignal,
        sessionSignal,
        amountSignal,
        binSignal,
    ];

    // Composite score = sum of all weighted contributions
    const compositeScore = signals.reduce(
        (sum, signal) => sum + signal.weighted_contribution,
        0,
    );

    const score = Math.min(1.0, parseFloat(compositeScore.toFixed(4)));
    const duration = Date.now() - startTime;

    return {
        score,
        risk_level: getRiskLevel(score),
        decision: getDecision(score),
        signals,
        scored_at: new Date().toISOString(),
        duration_ms: duration,
    };
}
