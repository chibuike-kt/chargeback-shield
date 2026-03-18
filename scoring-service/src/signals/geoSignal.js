/**
 * Geo mismatch signal.
 * Compares card issuing country against IP geolocation country.
 * Cross-border mismatches are a strong fraud indicator in African markets.
 */

// Countries with historically higher fraud rates in cross-border scenarios
const HIGH_RISK_COUNTRIES = new Set([
    "RU",
    "CN",
    "NG_CROSS",
    "BR",
    "PK",
    "RO",
    "UA",
]);

// African country codes — same-continent is lower risk than intercontinental
const AFRICAN_COUNTRIES = new Set([
    "NG",
    "GH",
    "KE",
    "ZA",
    "TZ",
    "UG",
    "RW",
    "ET",
    "SN",
    "CI",
    "CM",
    "ZM",
    "ZW",
    "MZ",
    "AO",
    "EG",
    "MA",
    "TN",
]);

export function computeGeoSignal(data) {
    const cardCountry = (data.card_country || "").toUpperCase();
    const ipCountry = (data.ip_country || "").toUpperCase();

    // No data — moderate risk
    if (!cardCountry || !ipCountry) {
        return buildSignal(0.35, `card:unknown ip:unknown`, "no_geo_data");
    }

    // Perfect match — low risk
    if (cardCountry === ipCountry) {
        return buildSignal(0.05, `${cardCountry}==${ipCountry}`, "match");
    }

    // Both African countries — low-medium risk (regional travel)
    if (
        AFRICAN_COUNTRIES.has(cardCountry) &&
        AFRICAN_COUNTRIES.has(ipCountry)
    ) {
        return buildSignal(
            0.3,
            `${cardCountry}->${ipCountry}`,
            "africa_cross_border",
        );
    }

    // African card, high-risk IP country — high risk
    if (
        AFRICAN_COUNTRIES.has(cardCountry) &&
        HIGH_RISK_COUNTRIES.has(ipCountry)
    ) {
        return buildSignal(
            0.9,
            `${cardCountry}->${ipCountry}`,
            "high_risk_country",
        );
    }

    // African card, other foreign IP — medium-high risk
    if (
        AFRICAN_COUNTRIES.has(cardCountry) &&
        !AFRICAN_COUNTRIES.has(ipCountry)
    ) {
        return buildSignal(
            0.7,
            `${cardCountry}->${ipCountry}`,
            "intercontinental",
        );
    }

    // Generic mismatch fallback
    return buildSignal(0.55, `${cardCountry}->${ipCountry}`, "mismatch");
}

function buildSignal(score, rawValue, reason) {
    return {
        signal_name: "geo_mismatch",
        raw_value: `${rawValue} (${reason})`,
        normalized_score: score,
        weight: 0.2,
        weighted_contribution: parseFloat((score * 0.2).toFixed(4)),
    };
}
