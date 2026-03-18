/**
 * Device fingerprint signal.
 * New or missing device fingerprints carry higher risk.
 * In a production system this would check against a device history store.
 * For now: presence and format validation.
 */

export function computeDeviceSignal(data) {
    const fp = data.device_fingerprint;

    // No fingerprint at all — high risk
    if (!fp || fp.trim() === "") {
        return buildSignal(0.75, "none", "missing_fingerprint");
    }

    // Too short to be a real fingerprint
    if (fp.length < 8) {
        return buildSignal(0.65, fp, "weak_fingerprint");
    }

    // Looks like a placeholder or test value
    const suspicious = [
        "test",
        "unknown",
        "null",
        "undefined",
        "00000000",
        "ffffffff",
    ];
    if (suspicious.includes(fp.toLowerCase())) {
        return buildSignal(0.7, fp, "suspicious_fingerprint");
    }

    // Valid fingerprint present — low risk
    // In production: check Redis device history here
    return buildSignal(0.1, fp.substring(0, 8) + "...", "valid_fingerprint");
}

function buildSignal(score, rawValue, reason) {
    return {
        signal_name: "device_fingerprint",
        raw_value: `${rawValue} (${reason})`,
        normalized_score: score,
        weight: 0.15,
        weighted_contribution: parseFloat((score * 0.15).toFixed(4)),
    };
}
