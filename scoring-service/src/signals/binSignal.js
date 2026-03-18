/**
 * BIN (Bank Identification Number) risk signal.
 * BIN = first 6 digits of a card number.
 * Certain BIN ranges are associated with prepaid cards, virtual cards
 * from high-risk issuers, or card ranges commonly used in fraud.
 *
 * In production this would be a maintained BIN database lookup.
 * Here we use a representative lookup table for demo purposes.
 */

// BIN prefix → risk level mapping
// Format: first 3-6 digits as string key
const BIN_RISK_TABLE = {
    // High-risk virtual/prepaid card ranges
    490: 0.75,
    491: 0.75,
    556: 0.7,
    557: 0.7,
    670: 0.8,
    671: 0.8,
    677: 0.75,

    // Medium-risk ranges
    520: 0.4,
    521: 0.4,
    530: 0.35,

    // Known low-risk Nigerian bank issued cards
    539983: 0.05, // GTBank Mastercard
    507822: 0.05, // Access Bank Verve
    650002: 0.05, // First Bank Verve
    440647: 0.08, // Zenith Bank Visa
    459234: 0.05, // UBA Visa

    // Default fallback is computed below
};

const CARD_TYPE_PATTERNS = {
    visa: /^4/,
    mastercard: /^5[1-5]/,
    verve: /^650[0-3]/,
    amex: /^3[47]/,
};

export function computeBinSignal(data) {
    const bin = String(data.card_bin || "000000").padEnd(6, "0");

    // Check 6-digit match first (most specific)
    if (BIN_RISK_TABLE[bin]) {
        return buildSignal(BIN_RISK_TABLE[bin], bin, "known_bin");
    }

    // Check 3-digit prefix
    const prefix3 = bin.substring(0, 3);
    if (BIN_RISK_TABLE[prefix3]) {
        return buildSignal(BIN_RISK_TABLE[prefix3], bin, "known_prefix");
    }

    // Determine card type for context
    let cardType = "unknown";
    for (const [type, pattern] of Object.entries(CARD_TYPE_PATTERNS)) {
        if (pattern.test(bin)) {
            cardType = type;
            break;
        }
    }

    // Default by card type
    const defaultScores = {
        visa: 0.15,
        mastercard: 0.15,
        verve: 0.1, // Verve is domestic Nigerian — lower risk
        amex: 0.2,
        unknown: 0.35,
    };

    const score = defaultScores[cardType] ?? 0.35;
    return buildSignal(score, bin, `${cardType}_default`);
}

function buildSignal(score, bin, reason) {
    return {
        signal_name: "bin_risk",
        raw_value: `${bin} (${reason})`,
        normalized_score: score,
        weight: 0.2,
        weighted_contribution: parseFloat((score * 0.2).toFixed(4)),
    };
}
