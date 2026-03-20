<?php

namespace App\Http\Controllers;

use App\Enums\DecisionType;
use App\Enums\RiskLevel;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
  public function index(Request $request): View
  {
    $merchant = auth('merchant')->user();

    $query = Transaction::with('evidenceBundle')
      ->where('merchant_id', $merchant->id)
      ->latest();

    // Filter by decision
    if ($request->filled('decision')) {
      $query->where('decision', $request->decision);
    }

    // Filter by risk level
    if ($request->filled('risk_level')) {
      $query->where('risk_level', $request->risk_level);
    }

    // Search by card BIN or last4
    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where('card_bin', 'like', "%{$search}%")
          ->orWhere('card_last4', 'like', "%{$search}%");
      });
    }

    // Filter by date range
    if ($request->filled('from')) {
      $query->whereDate('created_at', '>=', $request->from);
    }
    if ($request->filled('to')) {
      $query->whereDate('created_at', '<=', $request->to);
    }

    $transactions = $query->paginate(25)->withQueryString();

    // Stats for this merchant
    $stats = [
      'total'    => Transaction::where('merchant_id', $merchant->id)->count(),
      'approved' => Transaction::where('merchant_id', $merchant->id)
        ->where('decision', DecisionType::Allow->value)->count(),
      'step_up'  => Transaction::where('merchant_id', $merchant->id)
        ->where('decision', DecisionType::StepUp->value)->count(),
      'declined' => Transaction::where('merchant_id', $merchant->id)
        ->where('decision', DecisionType::Decline->value)->count(),
    ];

    return view('transactions.index', compact('transactions', 'stats'));
  }

  public function show(string $ulid): View
  {
    $merchant    = auth('merchant')->user();
    $transaction = Transaction::with([
      'evidenceBundle',
      'riskSignalLogs',
      'dispute',
    ])
      ->where('ulid', $ulid)
      ->where('merchant_id', $merchant->id)
      ->firstOrFail();

    return view('transactions.show', compact('transaction'));
  }

  public function export(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
  {
    $merchant = auth('merchant')->user();

    $query = Transaction::with('evidenceBundle')
      ->where('merchant_id', $merchant->id)
      ->latest();

    // Apply same filters as index
    if ($request->filled('decision')) {
      $query->where('decision', $request->decision);
    }
    if ($request->filled('risk_level')) {
      $query->where('risk_level', $request->risk_level);
    }
    if ($request->filled('from')) {
      $query->whereDate('created_at', '>=', $request->from);
    }
    if ($request->filled('to')) {
      $query->whereDate('created_at', '<=', $request->to);
    }

    $filename = 'transactions-' . now()->format('Y-m-d') . '.csv';

    return response()->streamDownload(function () use ($query) {

      $handle = fopen('php://output', 'w');

      // CSV headers
      fputcsv($handle, [
        'Transaction ID',
        'Card BIN',
        'Card Last4',
        'Card Country',
        'Amount',
        'Currency',
        'Risk Score',
        'Risk Level',
        'Decision',
        'Status',
        'IP Address',
        'IP Country',
        'Device Fingerprint',
        'Session Age (s)',
        'Merchant Category',
        'Evidence Bundle ID',
        'Evidence Locked',
        'Created At',
      ]);

      // Stream in chunks — never load all rows into memory
      $query->chunk(500, function ($transactions) use ($handle) {
        foreach ($transactions as $tx) {
          fputcsv($handle, [
            $tx->ulid,
            $tx->card_bin,
            $tx->card_last4,
            $tx->card_country ?? '',
            number_format($tx->amount / 100, 2),
            $tx->currency,
            $tx->risk_score,
            $tx->risk_level->value,
            $tx->decision->value,
            $tx->status->value,
            $tx->ip_address ?? '',
            $tx->ip_country ?? '',
            $tx->device_fingerprint ?? '',
            $tx->session_age_seconds,
            $tx->merchant_category ?? '',
            $tx->evidenceBundle?->ulid ?? '',
            $tx->evidenceBundle ? 'Yes' : 'No',
            $tx->created_at->toIso8601String(),
          ]);
        }
      });

      fclose($handle);
    }, $filename, [
      'Content-Type'        => 'text/csv',
      'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ]);
  }
}
