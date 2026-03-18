<?php

namespace Database\Seeders;

use App\Enums\ActorType;
use App\Models\AuditLog;
use App\Models\Dispute;
use App\Models\Merchant;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
  public function run(): void
  {
    $merchants = Merchant::all();

    foreach ($merchants as $merchant) {
      // Audit entries for transactions
      $transactions = Transaction::where('merchant_id', $merchant->id)
        ->limit(20)
        ->get();

      foreach ($transactions as $tx) {
        AuditLog::create([
          'merchant_id'   => $merchant->id,
          'actor_type'    => ActorType::Merchant->value,
          'action'        => 'transaction.intercepted',
          'resource_type' => 'transaction',
          'resource_id'   => $tx->ulid,
          'before_state'  => null,
          'after_state'   => [
            'decision'   => $tx->decision->value,
            'risk_score' => $tx->risk_score,
            'status'     => $tx->status->value,
          ],
          'ip_address'    => $this->randomIp(),
          'created_at'    => $tx->created_at,
        ]);
      }

      // Audit entries for disputes
      $disputes = Dispute::where('merchant_id', $merchant->id)->get();

      foreach ($disputes as $dispute) {
        AuditLog::create([
          'merchant_id'   => $merchant->id,
          'actor_type'    => ActorType::Merchant->value,
          'action'        => 'dispute.filed',
          'resource_type' => 'dispute',
          'resource_id'   => $dispute->ulid,
          'before_state'  => null,
          'after_state'   => [
            'reason_code' => $dispute->reason_code,
            'network'     => $dispute->network->value,
            'status'      => $dispute->status->value,
          ],
          'ip_address'    => $this->randomIp(),
          'created_at'    => $dispute->created_at,
        ]);

        // Resolution audit for won/lost disputes
        if ($dispute->resolved_at) {
          $outcome = $dispute->status->value;
          AuditLog::create([
            'merchant_id'   => $merchant->id,
            'actor_type'    => ActorType::System->value,
            'action'        => "dispute.{$outcome}",
            'resource_type' => 'dispute',
            'resource_id'   => $dispute->ulid,
            'before_state'  => ['status' => 'responded'],
            'after_state'   => ['status' => $outcome],
            'ip_address'    => null,
            'created_at'    => $dispute->resolved_at,
          ]);
        }
      }

      // System audit entries
      $systemEvents = [
        'merchant.registered',
        'api_key.generated',
        'webhook.configured',
      ];

      foreach ($systemEvents as $event) {
        AuditLog::create([
          'merchant_id'   => $merchant->id,
          'actor_type'    => ActorType::System->value,
          'action'        => $event,
          'resource_type' => 'merchant',
          'resource_id'   => $merchant->ulid,
          'before_state'  => null,
          'after_state'   => ['merchant_id' => $merchant->id],
          'ip_address'    => null,
          'created_at'    => $merchant->created_at,
        ]);
      }
    }

    $this->command->info('✓ Audit logs seeded');
  }

  private function randomIp(): string
  {
    return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
  }
}
