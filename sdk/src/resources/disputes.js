export class Disputes {
    constructor(client) {
        this.client = client;
    }

    /**
     * File a dispute for a transaction.
     * Call when a chargeback notification lands.
     * Returns a complete dispute response document instantly.
     *
     * @param {Object} params
     * @param {string} params.transactionId  - The disputed transaction ULID
     * @param {string} params.reasonCode     - Visa or Mastercard reason code
     * @param {string} params.network        - 'visa' or 'mastercard'
     * @param {string} [params.filedAt]      - ISO datetime when chargeback was filed
     */
    file(params) {
        return this.client.post("/dispute", {
            transaction_id: params.transactionId,
            reason_code: params.reasonCode,
            network: params.network,
            filed_at: params.filedAt,
        });
    }

    /**
     * Retrieve a dispute by ID.
     * @param {string} disputeId - The dispute ULID
     */
    retrieve(disputeId) {
        return this.client.get(`/dispute/${disputeId}`);
    }

    /**
     * Get the dispute response document.
     * @param {string} disputeId - The dispute ULID
     */
    response(disputeId) {
        return this.client.get(`/dispute/${disputeId}/response`);
    }

    /**
     * List all disputes.
     */
    list() {
        return this.client.get("/disputes");
    }
}
