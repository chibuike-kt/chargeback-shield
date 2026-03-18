/**
 * Session age signal.
 * Very new sessions are a strong indicator of automated fraud or
 * account takeover — the attacker just created a session to transact.
 */

export function computeSessionSignal(data) {
    const ageSeconds = parseInt(data.session_age_seconds) || 0;

    // Brand new session — very high risk
    if (ageSeconds === 0) {
        return buildSignal(0.9, "0s", "instant_session");
    }

    // Under 1 minute
    if (ageSeconds < 60) {
        return buildSignal(0.75, `${ageSeconds}s`, "very_new_session");
    }

    // 1–5 minutes
    if (ageSeconds < 300) {
        return buildSignal(0.5, `${ageSeconds}s`, "new_session");
    }

    // 5–15 minutes
    if (ageSeconds < 900) {
        return buildSignal(0.25, `${ageSeconds}s`, "recent_session");
    }

    // 15 minutes or older — established session
    return buildSignal(0.05, `${ageSeconds}s`, "established_session");
}

function buildSignal(score, rawValue, reason) {
    return {
        signal_name: "session_age",
        raw_value: `${rawValue} (${reason})`,
        normalized_score: score,
        weight: 0.1,
        weighted_contribution: parseFloat((score * 0.1).toFixed(4)),
    };
}
