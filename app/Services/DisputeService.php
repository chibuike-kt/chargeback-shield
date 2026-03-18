<?php

namespace App\Services;

use App\Actions\Disputes\GenerateDisputeResponse;
use App\Enums\ActorType;
use App\Enums\DisputeStatus;
use App\Enums\TrustEventType;
use App\Models\AuditLog;
use App\Models\Dispute;
use App\Models\Merchant;
use App\Models\MerchantTrustRegistry;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DisputeService
{
  public function __construct(
    private GenerateDisputeResponse $generateResponse,
    private ReasonCodeRegistry      $registry,
  ) {}

  /**
   * File a new dispute and immediately generate the response document.
   */
  public function fileDispute(
    Merchant    $merchant,
    Transaction $transaction,
    array       $data
  ): Dispute {
    return DB::transaction(function () use ($merchant, $transaction, $data) {

      // Determine network from reason code if not specified
      $network = $data['network']
        ?? $this->registry->getNetworkForCode($data['reason_code']);

      $reasonCodeDef = $this->registry->find($network, $data['reason_code']);

      // Create the dispute record
      $dispute = Dispute::create([
        'transaction_id'     => $transaction->id,
        'merchant_id'        => $merchant->id,
        'reason_code'        => $data['reason_code'],
        'reason_description' => $reasonCodeDef['description'] ?? null,
        'network'            => $network,
        'status'             => DisputeStatus::Open->value,
        'filed_at'           => $data['filed_at'] ?? now(),
      ]);

      // Generate the response document immediately
      $responseDocument = $this->generateResponse->execute($dispute, $merchant);

      // Store the response document on the dispute
      $dispute->update([
        'response_document' => $responseDocument,
        'status'            => DisputeStatus::Responded->value,
        'responded_at'      => now(),
      ]);

      // Write trust registry entry
      MerchantTrustRegistry::create([
        'merchant_id'    => $merchant->id,
        'transaction_id' => $transaction->id,
        'event_type'     => TrustEventType::ChargebackFiled->value,
        'penalty_points' => TrustEventType::ChargebackFiled->penaltyPoints(),
        'notes'          => "Chargeback filed: {$data['reason_code']} via {$network}",
        'created_at'     => now(),
      ]);

      // Audit log
      AuditLog::create([
        'merchant_id'   => $merchant->id,
        'actor_type'    => ActorType::Merchant->value,
        'action'        => 'dispute.filed',
        'resource_type' => 'dispute',
        'resource_id'   => $dispute->ulid,
        'after_state'   => [
          'reason_code' => $data['reason_code'],
          'network'     => $network,
          'status'      => DisputeStatus::Responded->value,
        ],
        'ip_address'    => request()->ip(),
        'created_at'    => now(),
      ]);

      return $dispute->fresh(['transaction', 'transaction.evidenceBundle']);
    });
  }

  /**
   * Resolve a dispute as won or lost.
   */
  public function resolveDispute(Dispute $dispute, string $outcome): Dispute
  {
    $status = $outcome === 'won' ? DisputeStatus::Won : DisputeStatus::Lost;

    $dispute->update([
      'status'      => $status->value,
      'resolved_at' => now(),
    ]);

    // Trust registry entry for resolution
    $eventType = $outcome === 'won'
      ? TrustEventType::DisputeWon
      : TrustEventType::DisputeLost;

    MerchantTrustRegistry::create([
      'merchant_id'    => $dispute->merchant_id,
      'transaction_id' => $dispute->transaction_id,
      'event_type'     => $eventType->value,
      'penalty_points' => $eventType->penaltyPoints(),
      'notes'          => "Dispute {$dispute->ulid} resolved as {$outcome}",
      'created_at'     => now(),
    ]);

    AuditLog::create([
      'merchant_id'   => $dispute->merchant_id,
      'actor_type'    => ActorType::System->value,
      'action'        => "dispute.{$outcome}",
      'resource_type' => 'dispute',
      'resource_id'   => $dispute->ulid,
      'after_state'   => ['status' => $status->value],
      'ip_address'    => request()->ip(),
      'created_at'    => now(),
    ]);

    return $dispute->fresh();
  }
}
