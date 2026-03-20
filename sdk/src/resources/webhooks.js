import { createHmac, timingSafeEqual } from "crypto";

export class Webhooks {
    constructor(webhookSecret) {
        this.secret = webhookSecret;
    }

    /**
     * Verify a webhook signature.
     * Call this in your webhook handler before processing the event.
     *
     * @param {Object|string} payload   - The raw request body
     * @param {string}        signature - The X-Chargeback-Shield-Sig header value
     * @returns {boolean}
     *
     * @example
     * app.post('/webhooks', (req, res) => {
     *   const isValid = shield.webhooks.verify(req.body, req.headers['x-chargeback-shield-sig']);
     *   if (!isValid) return res.status(401).send('Invalid signature');
     *   // process event
     * });
     */
    verify(payload, signature) {
        if (!signature || !this.secret) return false;

        const body =
            typeof payload === "string" ? payload : JSON.stringify(payload);

        const expected =
            "sha256=" +
            createHmac("sha256", this.secret).update(body).digest("hex");

        try {
            return timingSafeEqual(
                Buffer.from(expected),
                Buffer.from(signature),
            );
        } catch {
            return false;
        }
    }

    /**
     * Parse and verify a webhook event in one call.
     * Throws if signature is invalid.
     *
     * @param {Object|string} payload
     * @param {string}        signature
     * @returns {Object} The parsed event
     */
    constructEvent(payload, signature) {
        if (!this.verify(payload, signature)) {
            throw new Error("Webhook signature verification failed.");
        }

        return typeof payload === "string" ? JSON.parse(payload) : payload;
    }
}
