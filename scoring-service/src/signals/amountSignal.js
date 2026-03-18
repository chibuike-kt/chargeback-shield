/**
 * Amount risk signal.
 * High amounts on new or unverified sessions carry elevated risk.
 * Amounts stored in minor units (kobo for NGN).
 */

// Thresholds in kobo (NGN minor units)
const THRESHOLDS = {
    VERY_LOW: 10_000, // 100 NGN
    LOW: 100_000, // 1,000 NGN
    MEDIUM: 1_000_000, // 10,000 NGN
    HIGH: 5_000_000, // 50,000 NGN
    VERY_HIGH: 20_000_000, // 200,000 NGN
};

export function computeAmountSignal(data) {
    const amount = parseInt(data.amount) || 0;

    if (amount <= THRESHOLDS.VERY_LOW) {
        // Very small amounts — common in card testing attacks
        // Slightly elevated because attackers test with tiny amounts
        return buildSignal(0.2, amount, "micro_transaction");
    }

    if (amount <= THRESHOLDS.LOW) {
        return buildSignal(0.05, amount, "low_amount");
    }

    if (amount <= THRESHOLDS.MEDIUM) {
        return buildSignal(0.1, amount, "normal_amount");
    }

    if (amount <= THRESHOLDS.HIGH) {
        return buildSignal(0.3, amount, "elevated_amount");
    }

    if (amount <= THRESHOLDS.VERY_HIGH) {
        return buildSignal(0.6, amount, "high_amount");
    }

    return buildSignal(0.85, amount, "very_high_amount");
}

function buildSignal(score, amount, reason) {
    const formatted = (amount / 100).toLocaleString("en-NG", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    return {
        signal_name: "amount_risk",
        raw_value: `NGN ${formatted} (${reason})`,
        normalized_score: score,
        weight: 0.1,
        weighted_contribution: parseFloat((score * 0.1).toFixed(4)),
    };
}
