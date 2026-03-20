export class Transactions {
    constructor(client) {
        this.client = client;
    }

    /**
     * Pre-auth interception.
     * Call BEFORE approving the transaction.
     * Returns a decision: allow, step_up, or decline.
     *
     * @param {Object} params
     * @param {string} params.idempotencyKey    - Unique key per transaction attempt
     * @param {string} params.cardBin           - First 6 digits of card number
     * @param {string} params.cardLast4         - Last 4 digits of card number
     * @param {string} [params.cardCountry]     - 2-letter ISO country code
     * @param {number} params.amount            - Amount in minor units (kobo/cents)
     * @param {string} params.currency          - 3-letter ISO currency code
     * @param {string} [params.ipAddress]       - IPv4 or IPv6 address
     * @param {string} [params.ipCountry]       - 2-letter ISO country from IP geo
     * @param {string} [params.deviceFingerprint] - Unique device identifier
     * @param {number} [params.sessionAgeSeconds] - Session age in seconds
     * @param {string} [params.merchantCategory]  - 4-digit MCC code
     */
    intercept(params) {
        return this.client.post("/transaction/intercept", {
            idempotency_key: params.idempotencyKey,
            card_bin: params.cardBin,
            card_last4: params.cardLast4,
            card_country: params.cardCountry,
            amount: params.amount,
            currency: params.currency,
            ip_address: params.ipAddress,
            ip_country: params.ipCountry,
            ip_city: params.ipCity,
            device_fingerprint: params.deviceFingerprint,
            session_token: params.sessionToken,
            session_age_seconds: params.sessionAgeSeconds,
            merchant_category: params.merchantCategory,
        });
    }

    /**
     * Post-auth scoring.
     * Call AFTER approving the transaction — fire and forget.
     * Scores the transaction and locks evidence in the background.
     * Fires transaction.high_risk_detected webhook if score >= 0.70.
     *
     * @param {Object} params - Same as intercept() plus:
     * @param {string} [params.externalReference] - Your internal transaction ref
     */
    score(params) {
        return this.client.post("/transaction/score", {
            idempotency_key: params.idempotencyKey,
            card_bin: params.cardBin,
            card_last4: params.cardLast4,
            card_country: params.cardCountry,
            amount: params.amount,
            currency: params.currency,
            ip_address: params.ipAddress,
            ip_country: params.ipCountry,
            ip_city: params.ipCity,
            device_fingerprint: params.deviceFingerprint,
            session_token: params.sessionToken,
            session_age_seconds: params.sessionAgeSeconds,
            merchant_category: params.merchantCategory,
            external_reference: params.externalReference,
        });
    }

    /**
     * Retrieve a transaction by ID.
     * @param {string} transactionId - The transaction ULID
     */
    retrieve(transactionId) {
        return this.client.get(`/transaction/${transactionId}`);
    }

    /**
     * Retrieve the evidence bundle for a transaction.
     * @param {string} transactionId - The transaction ULID
     */
    evidence(transactionId) {
        return this.client.get(`/transaction/${transactionId}/evidence`);
    }
}
