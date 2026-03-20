import { HttpClient } from "./src/client.js";
import { Transactions } from "./src/resources/transactions.js";
import { Disputes } from "./src/resources/disputes.js";
import { Webhooks } from "./src/resources/webhooks.js";

export {
    ChargebackShieldError,
    AuthenticationError,
    ValidationError,
    RateLimitError,
    NotFoundError,
} from "./src/errors.js";

export class ChargebackShield {
    /**
     * Create a new Chargeback Shield client.
     *
     * @param {string} apiKey               - Your merchant API key (cs_live_...)
     * @param {Object} [options]
     * @param {string} [options.baseUrl]    - Override the API base URL
     * @param {number} [options.timeout]    - Request timeout in ms (default: 10000)
     * @param {string} [options.webhookSecret] - Your webhook secret for verification
     */
    constructor(apiKey, options = {}) {
        const client = new HttpClient(apiKey, options);

        this.transactions = new Transactions(client);
        this.disputes = new Disputes(client);
        this.webhooks = new Webhooks(options.webhookSecret ?? "");
    }
}

export default ChargebackShield;
