# Chargeback Shield

**Real-time chargeback protection for African fintechs.**

Built by yours truly - a developer-focused fintech middleware platform that intercepts, scores, and protects African card and payment transactions against chargebacks and fraud in real time.

---

## What It Does

Every time a card transaction is processed through Chargeback Shield:

1. **The transaction is scored** — a composite risk score is computed in real time from 6 signals: velocity, geolocation, device fingerprint, session age, amount risk, and BIN risk
2. **Evidence is locked** — an AES-256 encrypted, HMAC-SHA256 signed evidence bundle is created at approval time and stored immutably
3. **A decision is returned** — `allow`, `step_up` (trigger 3DS), or `decline`
4. **If a chargeback is filed** — the evidence bundle is retrieved, mapped to the card network reason code, and a structured dispute response document is generated in seconds

The entire flow takes under 100ms.

---

## The Problem

African fintechs lose millions annually to chargebacks they should win. The reasons:

- No cryptographic proof of legitimate transactions at dispute time
- Manual, slow dispute response processes (14+ days)
- No real-time fraud detection tuned for African card patterns
- Poor tooling for Visa and Mastercard reason code mapping

Chargeback Shield solves all four.

---

## Architecture
```
┌─────────────────────────────────────────────────────────────┐
│                        BROWSER CLIENT                        │
│              Blade + Tailwind + Alpine.js                    │
└───────────────────────┬─────────────────────────────────────┘
                        │ HTTP / WebSocket
          ┌─────────────▼──────────────┐
          │       LARAVEL (PHP)         │
          │  - Auth & Dashboard         │
          │  - REST API layer           │
          │  - Evidence Vault           │
          │  - Dispute Engine           │
          │  - Webhook Dispatcher       │
          │  - Audit Trail              │
          └──────┬──────────┬───────────┘
                 │          │
         MySQL   │          │  Redis Pub/Sub
                 │          │
          ┌──────▼──┐  ┌────▼──────────────────┐
          │  MySQL  │  │   NODE.JS SERVICE      │
          │         │  │  - Scoring Engine      │
          └─────────┘  │  - Velocity Windows    │
                       │  - WebSocket Server    │
                       │  - Redis Subscriber    │
                       └────────────┬───────────┘
                                    │
                              ┌─────▼──────┐
                              │   REDIS    │
                              │ - Velocity │
                              │ - Pub/Sub  │
                              │ - Cache    │
                              └────────────┘
```

**Laravel** handles auth, the web dashboard, all REST API endpoints, evidence vault operations, dispute management, webhook dispatch, and audit logging.

**Node.js** runs the real-time scoring engine. It receives transaction data via HTTP, computes the composite risk score using Redis sliding windows, publishes scored events to Redis pub/sub, and serves a WebSocket endpoint for the live dashboard feed.

**Redis** provides velocity sliding windows (sorted sets), pub/sub for real-time event broadcasting, and idempotency key caching.

**MySQL** is the persistent store for all records — transactions, evidence bundles, disputes, webhook logs, audit trail.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend framework | Laravel 11 (PHP 8.2) |
| Scoring service | Node.js 20 + Express |
| Database | MySQL 8 |
| Cache / Pub-Sub | Redis 7 |
| Frontend | Blade + Tailwind CSS v4 + Alpine.js |
| Charts | Chart.js 4 |
| Real-time | WebSocket (ws) + Redis pub/sub |
| PDF generation | Laravel DomPDF |
| Queue | Laravel database queue |
| Auth | Laravel custom guard (merchant) |

---

## Project Structure
```
chargeback-shield/
├── app/
│   ├── Actions/
│   │   ├── Disputes/
│   │   │   └── GenerateDisputeResponse.php
│   │   └── Transactions/
│   │       ├── CreateEvidenceBundle.php
│   │       └── InterceptTransaction.php
│   ├── DTOs/
│   │   └── ScoringResult.php
│   ├── Enums/
│   │   ├── ActorType.php
│   │   ├── DecisionType.php
│   │   ├── DisputeNetwork.php
│   │   ├── DisputeStatus.php
│   │   ├── RiskLevel.php
│   │   ├── TransactionStatus.php
│   │   ├── TrustEventType.php
│   │   ├── WebhookEventType.php
│   │   └── WebhookStatus.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/V1/
│   │   │   │   ├── DisputeController.php
│   │   │   │   └── TransactionController.php
│   │   │   ├── AuditLogController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DisputeController.php
│   │   │   ├── SimulationController.php
│   │   │   ├── TransactionController.php
│   │   │   ├── TrustRegistryController.php
│   │   │   └── WebhookController.php
│   │   ├── Middleware/
│   │   │   └── ValidateMerchantApiKey.php
│   │   └── Requests/
│   │       └── Api/
│   │           ├── FileDisputeRequest.php
│   │           └── TransactionInterceptRequest.php
│   ├── Jobs/
│   │   └── DispatchWebhook.php
│   ├── Models/
│   │   ├── AuditLog.php
│   │   ├── Dispute.php
│   │   ├── EvidenceBundle.php
│   │   ├── Merchant.php
│   │   ├── MerchantTrustRegistry.php
│   │   ├── RiskSignalLog.php
│   │   ├── Transaction.php
│   │   └── WebhookDelivery.php
│   └── Services/
│       ├── DisputeService.php
│       ├── EvidenceService.php
│       ├── IdempotencyService.php
│       ├── ReasonCodeRegistry.php
│       ├── ScoringService.php
│       ├── TransactionService.php
│       └── WebhookDispatcher.php
├── scoring-service/
│   └── src/
│       ├── config/
│       │   └── redis.js
│       ├── signals/
│       │   ├── amountSignal.js
│       │   ├── binSignal.js
│       │   ├── deviceSignal.js
│       │   ├── geoSignal.js
│       │   ├── sessionSignal.js
│       │   └── velocitySignal.js
│       ├── publisher.js
│       ├── scorer.js
│       ├── server.js
│       └── websocket.js
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── AuditLogSeeder.php
│       ├── DemoSeeder.php
│       ├── DisputeSeeder.php
│       ├── MerchantSeeder.php
│       ├── TransactionSeeder.php
│       ├── TrustRegistrySeeder.php
│       └── WebhookDeliverySeeder.php
└── resources/
    └── views/
        ├── audit/
        ├── dashboard/
        ├── disputes/
        ├── layouts/
        ├── pdf/
        ├── simulate/
        ├── transactions/
        ├── trust-registry/
        └── webhooks/
```

---

## Prerequisites

- PHP 8.2+ with extensions: `openssl`, `pdo_mysql`, `redis` or predis
- Composer
- Node.js 20+
- npm
- MySQL 8
- Redis 7 (or Memurai on Windows)

---

## Installation

### 1. Clone and install PHP dependencies
```bash
git clone https://github.com/your-org/chargeback-shield.git
cd chargeback-shield
composer install
```

### 2. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME="Chargeback Shield"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chargeback_shield
DB_USERNAME=root
DB_PASSWORD=

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

SESSION_DRIVER=database
QUEUE_CONNECTION=database

NODE_SCORING_SERVICE_URL=http://localhost:3001
```

### 3. Database setup
```bash
php artisan migrate
php artisan db:seed --class=DemoSeeder
```

### 4. Install frontend dependencies
```bash
npm install
```

### 5. Install and configure the scoring service
```bash
cd scoring-service
npm install
cp .env.example .env
cd ..
```

The scoring service `.env` should contain:
```env
PORT=3001
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
PUBSUB_CHANNEL=transactions.live
```

---

## Running the Application

You need four terminal processes running simultaneously:

**Terminal 1 — Laravel application server:**
```bash
php artisan serve
```

**Terminal 2 — Vite asset bundler (development):**
```bash
npm run dev
```

**Terminal 3 — Laravel queue worker (webhook dispatch):**
```bash
php artisan queue:work --sleep=3 --tries=1 --timeout=30
```

**Terminal 4 — Node.js scoring service:**
```bash
cd scoring-service
npm run dev
```

Visit `http://localhost:8000` in your browser.

---

## Demo Accounts

After running the seeder, two demo accounts are available:

| Company | Email | Password |
|---------|-------|----------|
| Flutterwave Demo | demo@flutterwave.test | password |
| Paystack Demo | demo@paystack.test | password |

---

## API Reference

All API endpoints require the `X-API-Key` header with your merchant API key.

### Intercept a transaction
```
POST /api/v1/transaction/intercept
```

**Headers:**
```
X-API-Key: cs_live_your_key_here
Content-Type: application/json
```

**Request body:**
```json
{
    "idempotency_key": "unique-key-per-transaction",
    "card_bin": "459234",
    "card_last4": "4242",
    "card_country": "NG",
    "amount": 500000,
    "currency": "NGN",
    "ip_address": "197.210.1.1",
    "ip_country": "NG",
    "device_fingerprint": "fp_abc123",
    "session_age_seconds": 900,
    "merchant_category": "5411"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Transaction approved.",
    "data": {
        "transaction_id": "01kkzedkjzwxkjmkefbehe2tdh",
        "decision": "allow",
        "risk_score": 0.1300,
        "risk_level": "low",
        "status": "approved",
        "currency": "NGN",
        "amount": 500000,
        "evidence_bundle_id": "01kkzedkk0abc123def456gh",
        "signals": [...],
        "processed_at": "2026-03-18T03:04:24+00:00",
        "idempotent": false
    }
}
```

**Decisions:**

| Decision | Score Range | Meaning |
|----------|-------------|---------|
| `allow` | 0.00 – 0.39 | Transaction approved |
| `step_up` | 0.40 – 0.69 | Approved, trigger 3DS authentication |
| `decline` | 0.70 – 1.00 | Transaction blocked |

### Retrieve evidence bundle
```
GET /api/v1/transaction/:id/evidence
```

Returns the decrypted evidence bundle with HMAC signature verification status.

### File a dispute
```
POST /api/v1/dispute
```

**Request body:**
```json
{
    "transaction_id": "01kkzedkjzwxkjmkefbehe2tdh",
    "reason_code": "4863",
    "network": "mastercard"
}
```

Returns a complete dispute response document ready to submit to the card network.

### Get dispute response
```
GET /api/v1/dispute/:id/response
```

### List disputes
```
GET /api/v1/disputes
```

---

## Supported Reason Codes

### Visa
| Code | Description |
|------|-------------|
| 10.1 | EMV Liability Shift Counterfeit Fraud |
| 10.4 | Other Fraud – Card Absent Environment |
| 10.5 | Visa Fraud Monitoring Program |
| 11.1 | Card Recovery Bulletin |
| 12.5 | Incorrect Transaction Amount |
| 13.1 | Merchandise / Services Not Received |
| 13.3 | Not as Described or Defective Merchandise |
| 13.6 | Credit Not Processed |

### Mastercard
| Code | Description |
|------|-------------|
| 4853 | Cardholder Dispute |
| 4855 | Goods or Services Not Provided |
| 4859 | Addendum, No-show, or ATM Dispute |
| 4863 | Cardholder Does Not Recognize Transaction |
| 4834 | Duplicate Processing |
| 4837 | No Cardholder Authorization |
| 4840 | Fraudulent Processing of Transactions |

---

## Risk Scoring Engine

The scoring engine runs in Node.js and computes a composite score from 6 weighted signals:

| Signal | Weight | Description |
|--------|--------|-------------|
| Velocity | 25% | Redis sliding windows: tx/hour, spend/24h, unique merchants, unique countries |
| Geo Mismatch | 20% | Card issuing country vs IP geolocation country |
| BIN Risk | 20% | BIN lookup table — known high-risk prepaid and virtual card ranges |
| Device Fingerprint | 15% | New or missing device fingerprints carry higher risk |
| Session Age | 10% | Brand new sessions (0–60s) indicate automated fraud |
| Amount Risk | 10% | High transaction amounts on new sessions carry elevated risk |

Composite score = sum of (normalized_signal_score × weight)

Velocity uses Redis sorted sets as exact sliding windows — no drift, no approximation.

---

## Evidence Vault

Every approved or stepped-up transaction produces an evidence bundle:

- **Payload** — full transaction context: card, network, device, session, merchant, risk signals, decision, timestamp
- **Encryption** — AES-256-CBC with a per-bundle random IV
- **Signing** — HMAC-SHA256 signed with the merchant's webhook secret
- **Key derivation** — SHA-256 hash of the webhook secret ensures correct AES-256 key length
- **Immutability** — the `EvidenceBundle` model throws `RuntimeException` on any update attempt
- **Verification** — signature is re-verified on every retrieval

---

## Webhook Events

Webhooks fire for every significant event. All payloads are HMAC-SHA256 signed with the `X-Chargeback-Shield-Sig: sha256=<hex>` header.

| Event | Trigger |
|-------|---------|
| `transaction.scored` | Transaction approved or stepped up |
| `transaction.declined` | Transaction declined |
| `dispute.filed` | Chargeback filed and response generated |
| `dispute.responded` | Dispute response submitted |
| `dispute.won` | Dispute resolved as won |
| `dispute.lost` | Dispute resolved as lost |

Failed deliveries are retried up to 3 times with exponential backoff: 1 minute, 5 minutes, 15 minutes.

---

## Security Design

| Concern | Implementation |
|---------|---------------|
| API authentication | Per-merchant API keys (`cs_live_*`) validated on every request |
| Evidence integrity | AES-256-CBC encryption + HMAC-SHA256 signing |
| Evidence immutability | Model-level `save()` override + no `updated_at` column |
| Trust registry integrity | Append-only model — no updates or deletes permitted |
| Idempotency | Redis-backed 24-hour idempotency key deduplication |
| Webhook verification | HMAC-SHA256 signed payloads with `X-Chargeback-Shield-Sig` header |
| Audit completeness | Every business action produces an `AuditLog` entry |

---

## Simulation Panel

The simulation panel at `/simulate` runs 6 end-to-end scenarios without any API integration:

| Scenario | What It Demonstrates |
|----------|---------------------|
| Normal Transaction | Clean low-risk flow — approved, evidence locked |
| Card Testing Attack | 8 rapid micro-transactions — velocity windows trigger, card blocked |
| Account Takeover | New device + foreign IP + zero session age — all signals fire |
| High-Value Step-Up | Legitimate high-value transaction — 3DS triggered |
| Chargeback Filed | Full dispute flow — evidence retrieved, response auto-generated |
| Webhook Failure | Failed delivery + exponential backoff retry cycle |

Each scenario produces real database records, real evidence bundles, real webhook logs, and real audit entries — visible across all dashboard views.

---

## Database Schema

| Table | Purpose |
|-------|---------|
| `merchants` | Merchant accounts, API keys, webhook config |
| `transactions` | All intercepted transactions with scoring output |
| `evidence_bundles` | Encrypted, signed evidence — no `updated_at`, immutable |
| `risk_signal_logs` | Per-signal breakdown for every transaction |
| `disputes` | Chargeback cases with generated response documents |
| `merchant_trust_registry` | Append-only reputation ledger |
| `webhook_deliveries` | Delivery attempts, retry state, response logs |
| `audit_logs` | Complete action audit trail |

---

## Environment Variables

### Laravel (`.env`)

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name | Chargeback Shield |
| `APP_URL` | Application URL | http://localhost:8000 |
| `DB_*` | MySQL connection settings | — |
| `REDIS_CLIENT` | Redis client driver | predis |
| `REDIS_HOST` | Redis host | 127.0.0.1 |
| `REDIS_PORT` | Redis port | 6379 |
| `SESSION_DRIVER` | Session storage | database |
| `QUEUE_CONNECTION` | Queue driver | database |
| `NODE_SCORING_SERVICE_URL` | Scoring service URL | http://localhost:3001 |

### Node.js scoring service (`scoring-service/.env`)

| Variable | Description | Default |
|----------|-------------|---------|
| `PORT` | HTTP + WebSocket port | 3001 |
| `REDIS_HOST` | Redis host | 127.0.0.1 |
| `REDIS_PORT` | Redis port | 6379 |
| `REDIS_PASSWORD` | Redis password | — |
| `PUBSUB_CHANNEL` | Pub/sub channel name | transactions.live |

---

## Contributing

This project was built as a hackathon submission for RaenestXDev_career. For questions or contributions, open an issue or pull request.

---

## License

MIT License — see `LICENSE` for details.

---

*Chargeback Shield — Real-time chargeback protection for African fintechs.*
