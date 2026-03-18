<?php

namespace App\Http\Controllers;

use App\Enums\DecisionType;
use App\Enums\DisputeStatus;
use App\Enums\TransactionStatus;
use App\Models\Transaction;
use App\Models\Dispute;
use App\Models\WebhookDelivery;
use App\Enums\WebhookStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
  public function index(): View
  {
    $merchant = auth('merchant')->user();

    // ── Core stats ────────────────────────────────────────────────────────
    $totalTransactions = Transaction::where('merchant_id', $merchant->id)->count();

    $totalFlagged = Transaction::where('merchant_id', $merchant->id)
      ->where('decision', DecisionType::StepUp->value)
      ->count();

    $totalDeclined = Transaction::where('merchant_id', $merchant->id)
      ->where('decision', DecisionType::Decline->value)
      ->count();

    $totalChargebacks = Dispute::where('merchant_id', $merchant->id)->count();

    $totalDisputesWon = Dispute::where('merchant_id', $merchant->id)
      ->where('status', DisputeStatus::Won->value)
      ->count();

    $totalDisputesOpen = Dispute::where('merchant_id', $merchant->id)
      ->where('status', DisputeStatus::Open->value)
      ->count();

    // ── Webhook health ────────────────────────────────────────────────────
    $webhookDelivered = WebhookDelivery::where('merchant_id', $merchant->id)
      ->where('status', WebhookStatus::Delivered->value)
      ->count();

    $webhookFailed = WebhookDelivery::where('merchant_id', $merchant->id)
      ->where('status', WebhookStatus::Failed->value)
      ->count();

    $webhookTotal    = $webhookDelivered + $webhookFailed;
    $webhookHealthPct = $webhookTotal > 0
      ? round(($webhookDelivered / $webhookTotal) * 100)
      : 100;

    // ── Risk distribution for chart ───────────────────────────────────────
    $riskDistribution = Transaction::where('merchant_id', $merchant->id)
      ->select('risk_level', DB::raw('count(*) as count'))
      ->groupBy('risk_level')
      ->pluck('count', 'risk_level')
      ->toArray();

    $riskChartData = [
      'low'    => $riskDistribution['low']    ?? 0,
      'medium' => $riskDistribution['medium'] ?? 0,
      'high'   => $riskDistribution['high']   ?? 0,
    ];

    // ── Decision distribution ─────────────────────────────────────────────
    $decisionDistribution = Transaction::where('merchant_id', $merchant->id)
      ->select('decision', DB::raw('count(*) as count'))
      ->groupBy('decision')
      ->pluck('count', 'decision')
      ->toArray();

    // ── Transaction volume last 7 days ────────────────────────────────────
    $volumeData = Transaction::where('merchant_id', $merchant->id)
      ->where('created_at', '>=', now()->subDays(6)->startOfDay())
      ->select(
        DB::raw('DATE(created_at) as date'),
        DB::raw('count(*) as count'),
        DB::raw('sum(amount) as total_amount')
      )
      ->groupBy('date')
      ->orderBy('date')
      ->get();

    // Fill missing days with zeros
    $volumeLabels = [];
    $volumeCounts = [];
    for ($i = 6; $i >= 0; $i--) {
      $date            = now()->subDays($i)->format('Y-m-d');
      $volumeLabels[]  = now()->subDays($i)->format('M d');
      $dayData         = $volumeData->firstWhere('date', $date);
      $volumeCounts[]  = $dayData ? $dayData->count : 0;
    }

    // ── Recent transactions ───────────────────────────────────────────────
    $recentTransactions = Transaction::with('evidenceBundle')
      ->where('merchant_id', $merchant->id)
      ->latest()
      ->limit(10)
      ->get();

    // ── Recent disputes ───────────────────────────────────────────────────
    $recentDisputes = Dispute::with('transaction')
      ->where('merchant_id', $merchant->id)
      ->latest()
      ->limit(5)
      ->get();

    return view('dashboard.index', compact(
      'merchant',
      'totalTransactions',
      'totalFlagged',
      'totalDeclined',
      'totalChargebacks',
      'totalDisputesWon',
      'totalDisputesOpen',
      'webhookHealthPct',
      'webhookDelivered',
      'webhookFailed',
      'riskChartData',
      'decisionDistribution',
      'volumeLabels',
      'volumeCounts',
      'recentTransactions',
      'recentDisputes',
    ));
  }
}
