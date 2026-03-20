<?php

namespace App\Console\Commands;

use App\Enums\DecisionType;
use App\Enums\DisputeStatus;
use App\Enums\WebhookStatus;
use App\Mail\WeeklySummaryMail;
use App\Models\Dispute;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\WebhookDelivery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklySummary extends Command
{
    protected $signature   = 'shield:weekly-summary';
    protected $description = 'Send weekly summary emails to all active merchants';

    public function handle(): void
    {
        $merchants = Merchant::where('is_active', true)->get();
        $from      = now()->subDays(7)->startOfDay();
        $to        = now()->endOfDay();

        $this->info("Sending weekly summaries to {$merchants->count()} merchants...");

        foreach ($merchants as $merchant) {
            $stats = [
                'total_transactions' => Transaction::where('merchant_id', $merchant->id)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                'flagged' => Transaction::where('merchant_id', $merchant->id)
                    ->where('decision', DecisionType::StepUp->value)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                'declined' => Transaction::where('merchant_id', $merchant->id)
                    ->where('decision', DecisionType::Decline->value)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                'chargebacks' => Dispute::where('merchant_id', $merchant->id)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                'disputes_won' => Dispute::where('merchant_id', $merchant->id)
                    ->where('status', DisputeStatus::Won->value)
                    ->whereBetween('resolved_at', [$from, $to])
                    ->count(),

                'webhooks_delivered' => WebhookDelivery::where('merchant_id', $merchant->id)
                    ->where('status', WebhookStatus::Delivered->value)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),

                'webhooks_failed' => WebhookDelivery::where('merchant_id', $merchant->id)
                    ->where('status', WebhookStatus::Failed->value)
                    ->whereBetween('created_at', [$from, $to])
                    ->count(),
            ];

            Mail::to($merchant->email)
                ->queue(new WeeklySummaryMail($merchant, $stats));

            $this->line("  ✓ Queued for {$merchant->company_name}");
        }

        $this->info('Done.');
    }
}
