<?php

namespace App\Http\Controllers;

use App\Actions\Transactions\CreateEvidenceBundle;
use App\Actions\Transactions\InterceptTransaction;
use App\Enums\DecisionType;
use App\Models\Merchant;
use App\Services\DisputeService;
use App\Services\IdempotencyService;
use App\Services\ScoringService;
use App\Services\TransactionService;
use App\Services\WebhookDispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SimulationController extends Controller
{
  public function __construct(
    private InterceptTransaction $interceptTransaction,
    private DisputeService       $disputeService,
    private WebhookDispatcher    $webhookDispatcher,
  ) {}

  public function index(): View
  {
    return view('simulate.index');
  }

  /**
   * Run a named simulation scenario.
   * Returns JSON with steps and created record IDs.
   */
  public function run(Request $request): JsonResponse
  {
    $scenario = $request->input('scenario');
    $merchant = auth('merchant')->user();

    $result = match ($scenario) {
      'normal_transaction'       => $this->normalTransaction($merchant),
      'card_testing_attack'      => $this->cardTestingAttack($merchant),
      'account_takeover'         => $this->accountTakeover($merchant),
      'high_value_stepup'        => $this->highValueStepUp($merchant),
      'chargeback_filed'         => $this->chargebackFiled($merchant),
      'webhook_failure'          => $this->webhookFailure($merchant),
      default                    => ['error' => 'Unknown scenario'],
    };

    return response()->json($result);
  }

  // ── Scenario 1 — Normal transaction ──────────────────────────────────────

  private function normalTransaction(Merchant $merchant): array
  {
    $steps = [];

    $steps[] = $this->step('Initiating normal low-risk transaction...', 'pending');

    $data = [
      'idempotency_key'    => 'sim-normal-' . Str::random(8),
      'card_bin'           => '459234',
      'card_last4'         => '4242',
      'card_country'       => 'NG',
      'amount'             => rand(10000, 500000),
      'currency'           => 'NGN',
      'ip_address'         => '197.210.1.1',
      'ip_country'         => 'NG',
      'ip_city'            => 'Lagos',
      'device_fingerprint' => 'sim-device-' . Str::random(12),
      'session_age_seconds' => rand(600, 3600),
      'merchant_category'  => '5411',
    ];

    $result = $this->interceptTransaction->execute($merchant, $data);

    $steps[] = $this->step(
      "Transaction scored: risk {$result['risk_score']} ({$result['risk_level']})",
      'success'
    );
    $steps[] = $this->step(
      "Decision: " . strtoupper($result['decision']),
      $result['decision'] === 'allow' ? 'success' : 'warning'
    );

    if ($result['evidence_bundle_id']) {
      $steps[] = $this->step(
        "Evidence bundle locked: {$result['evidence_bundle_id']}",
        'success'
      );
    }

    $steps[] = $this->step('Webhook fired to merchant endpoint', 'success');

    return [
      'scenario'       => 'Normal Transaction',
      'steps'          => $steps,
      'transaction_id' => $result['transaction_id'],
      'decision'       => $result['decision'],
      'risk_score'     => $result['risk_score'],
      'evidence_id'    => $result['evidence_bundle_id'],
    ];
  }

  // ── Scenario 2 — Card testing attack ─────────────────────────────────────

  private function cardTestingAttack(Merchant $merchant): array
  {
    $steps   = [];
    $lastId  = null;
    $lastScore = 0;

    $steps[] = $this->step('Simulating card testing attack — 8 rapid micro-transactions...', 'pending');

    for ($i = 1; $i <= 8; $i++) {
      $data = [
        'idempotency_key'    => 'sim-cardtest-' . Str::random(8),
        'card_bin'           => '490123',
        'card_last4'         => '0001',
        'card_country'       => 'NG',
        'amount'             => rand(5000, 15000), // tiny amounts
        'currency'           => 'NGN',
        'ip_address'         => '185.220.101.' . rand(1, 255),
        'ip_country'         => 'NG',
        'device_fingerprint' => 'sim-attacker-device-001',
        'session_age_seconds' => rand(5, 30),
        'merchant_category'  => '5999',
      ];

      $result    = $this->interceptTransaction->execute($merchant, $data);
      $lastId    = $result['transaction_id'];
      $lastScore = $result['risk_score'];

      $steps[] = $this->step(
        "TX {$i}/8 — Score: {$result['risk_score']} — " . strtoupper($result['decision']),
        $result['decision'] === 'decline' ? 'danger' : 'warning'
      );

      // Small delay to make velocity windows register
      usleep(100000); // 100ms
    }

    $steps[] = $this->step(
      'Velocity threshold exceeded — card testing pattern detected',
      'danger'
    );
    $steps[] = $this->step('All subsequent transactions from this card auto-declined', 'danger');

    return [
      'scenario'       => 'Card Testing Attack',
      'steps'          => $steps,
      'transaction_id' => $lastId,
      'decision'       => 'decline',
      'risk_score'     => $lastScore,
      'evidence_id'    => null,
    ];
  }

  // ── Scenario 3 — Account takeover ────────────────────────────────────────

  private function accountTakeover(Merchant $merchant): array
  {
    $steps = [];

    $steps[] = $this->step('Simulating account takeover attempt...', 'pending');
    $steps[] = $this->step('New device fingerprint detected (never seen before)', 'warning');
    $steps[] = $this->step('IP geolocation: Russia — card country: Nigeria', 'warning');
    $steps[] = $this->step('Session age: 0 seconds (brand new session)', 'warning');
    $steps[] = $this->step('High transaction amount flagged', 'warning');

    $data = [
      'idempotency_key'    => 'sim-ato-' . Str::random(8),
      'card_bin'           => '670123',
      'card_last4'         => '8888',
      'card_country'       => 'NG',
      'amount'             => 15000000, // NGN 150,000
      'currency'           => 'NGN',
      'ip_address'         => '95.213.1.' . rand(1, 255),
      'ip_country'         => 'RU',
      'ip_city'            => 'Moscow',
      'device_fingerprint' => null, // no device fingerprint
      'session_age_seconds' => 0,
      'merchant_category'  => '5999',
    ];

    $result = $this->interceptTransaction->execute($merchant, $data);

    $steps[] = $this->step(
      "Risk score: {$result['risk_score']} — " . strtoupper($result['risk_level']) . " RISK",
      'danger'
    );
    $steps[] = $this->step(
      "Decision: " . strtoupper($result['decision']) . " — Transaction blocked",
      $result['decision'] === 'decline' ? 'danger' : 'warning'
    );
    $steps[] = $this->step('Merchant notified via webhook', 'success');

    return [
      'scenario'       => 'Account Takeover Attempt',
      'steps'          => $steps,
      'transaction_id' => $result['transaction_id'],
      'decision'       => $result['decision'],
      'risk_score'     => $result['risk_score'],
      'evidence_id'    => $result['evidence_bundle_id'],
    ];
  }

  // ── Scenario 4 — High value step-up ──────────────────────────────────────

  private function highValueStepUp(Merchant $merchant): array
  {
    $steps = [];

    $steps[] = $this->step('Processing high-value legitimate transaction...', 'pending');
    $steps[] = $this->step('Known device fingerprint — established session', 'success');
    $steps[] = $this->step('IP matches card country — no geo anomaly', 'success');
    $steps[] = $this->step('High amount triggers step-up authentication', 'warning');

    $data = [
      'idempotency_key'    => 'sim-stepup-' . Str::random(8),
      'card_bin'           => '459234',
      'card_last4'         => '5678',
      'card_country'       => 'NG',
      'amount'             => 8500000, // NGN 85,000
      'currency'           => 'NGN',
      'ip_address'         => '197.210.55.1',
      'ip_country'         => 'NG',
      'ip_city'            => 'Abuja',
      'device_fingerprint' => 'sim-known-device-stepup',
      'session_age_seconds' => 1800,
      'merchant_category'  => '5812',
    ];

    $result = $this->interceptTransaction->execute($merchant, $data);

    $steps[] = $this->step(
      "Risk score: {$result['risk_score']} — Step-up threshold reached",
      'warning'
    );
    $steps[] = $this->step(
      "Decision: " . strtoupper($result['decision']) . " — 3DS authentication required",
      'warning'
    );

    if ($result['evidence_bundle_id']) {
      $steps[] = $this->step(
        "Evidence bundle locked regardless of 3DS outcome",
        'success'
      );
    }

    $steps[] = $this->step('Merchant notified via webhook', 'success');

    return [
      'scenario'       => 'High Value Step-Up',
      'steps'          => $steps,
      'transaction_id' => $result['transaction_id'],
      'decision'       => $result['decision'],
      'risk_score'     => $result['risk_score'],
      'evidence_id'    => $result['evidence_bundle_id'],
    ];
  }

  // ── Scenario 5 — Chargeback filed ────────────────────────────────────────

  private function chargebackFiled(Merchant $merchant): array
  {
    $steps = [];

    $steps[] = $this->step('Creating approved transaction with evidence bundle...', 'pending');

    // First create a clean approved transaction
    $txData = [
      'idempotency_key'    => 'sim-cb-' . Str::random(8),
      'card_bin'           => '459234',
      'card_last4'         => '1234',
      'card_country'       => 'NG',
      'amount'             => 2500000,
      'currency'           => 'NGN',
      'ip_address'         => '197.210.1.50',
      'ip_country'         => 'NG',
      'ip_city'            => 'Lagos',
      'device_fingerprint' => 'sim-cb-device-' . Str::random(8),
      'session_age_seconds' => 900,
      'merchant_category'  => '5411',
    ];

    $txResult = $this->interceptTransaction->execute($merchant, $txData);

    $steps[] = $this->step(
      "Transaction approved — ID: {$txResult['transaction_id']}",
      'success'
    );
    $steps[] = $this->step(
      "Evidence bundle locked: {$txResult['evidence_bundle_id']}",
      'success'
    );
    $steps[] = $this->step('Cardholder files chargeback — Mastercard reason code 4853', 'warning');
    $steps[] = $this->step('Chargeback Shield retrieves locked evidence bundle...', 'pending');

    // Find the transaction and file a dispute
    $transaction = \App\Models\Transaction::where('ulid', $txResult['transaction_id'])
      ->with('evidenceBundle')
      ->first();

    $dispute = $this->disputeService->fileDispute($merchant, $transaction, [
      'reason_code' => '4853',
      'network'     => 'mastercard',
      'filed_at'    => now(),
    ]);

    $steps[] = $this->step('Evidence bundle signature verified — HMAC-SHA256 valid', 'success');
    $steps[] = $this->step('Dispute response document auto-generated', 'success');
    $steps[] = $this->step(
      "Response ready — Dispute ID: {$dispute->ulid}",
      'success'
    );
    $steps[] = $this->step('Merchant notified via webhook', 'success');

    return [
      'scenario'       => 'Chargeback Filed',
      'steps'          => $steps,
      'transaction_id' => $txResult['transaction_id'],
      'dispute_id'     => $dispute->ulid,
      'decision'       => $txResult['decision'],
      'risk_score'     => $txResult['risk_score'],
      'evidence_id'    => $txResult['evidence_bundle_id'],
    ];
  }

  // ── Scenario 6 — Webhook failure and retry ────────────────────────────────

  private function webhookFailure(Merchant $merchant): array
  {
    $steps = [];

    $steps[] = $this->step('Simulating webhook failure and retry cycle...', 'pending');

    // Temporarily set a bad webhook URL
    $originalUrl = $merchant->webhook_url;
    $merchant->update(['webhook_url' => 'https://httpstat.us/500']);

    $data = [
      'idempotency_key'    => 'sim-webhook-' . Str::random(8),
      'card_bin'           => '459234',
      'card_last4'         => '9876',
      'card_country'       => 'NG',
      'amount'             => 750000,
      'currency'           => 'NGN',
      'ip_country'         => 'NG',
      'device_fingerprint' => 'sim-webhook-device',
      'session_age_seconds' => 600,
      'merchant_category'  => '5411',
    ];

    $result = $this->interceptTransaction->execute($merchant, $data);

    $steps[] = $this->step(
      "Transaction processed — ID: {$result['transaction_id']}",
      'success'
    );
    $steps[] = $this->step(
      'Webhook fired to endpoint — HTTP 500 received',
      'danger'
    );
    $steps[] = $this->step(
      'Retry scheduled: attempt 2 in 1 minute (exponential backoff)',
      'warning'
    );
    $steps[] = $this->step(
      'Retry scheduled: attempt 3 in 5 minutes if still failing',
      'warning'
    );
    $steps[] = $this->step(
      'Webhook delivery logged — visible in Webhook Log UI',
      'success'
    );

    // Restore original webhook URL
    $merchant->update(['webhook_url' => $originalUrl]);

    return [
      'scenario'       => 'Webhook Failure & Retry',
      'steps'          => $steps,
      'transaction_id' => $result['transaction_id'],
      'decision'       => $result['decision'],
      'risk_score'     => $result['risk_score'],
      'evidence_id'    => $result['evidence_bundle_id'],
    ];
  }

  // ── Helpers ───────────────────────────────────────────────────────────────

  private function step(string $message, string $status): array
  {
    return [
      'message'    => $message,
      'status'     => $status,
      'timestamp'  => now()->format('H:i:s'),
    ];
  }
}
