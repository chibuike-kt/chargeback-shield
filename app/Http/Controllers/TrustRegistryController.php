<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\MerchantTrustRegistry;
use Illuminate\View\View;

class TrustRegistryController extends Controller
{
  public function index(): View
  {
    $merchant = auth('merchant')->user();

    $entries = MerchantTrustRegistry::with('transaction')
      ->where('merchant_id', $merchant->id)
      ->orderByDesc('created_at')
      ->paginate(30);

    $totalPenalty  = MerchantTrustRegistry::where('merchant_id', $merchant->id)
      ->sum('penalty_points');

    $trustScore    = $merchant->trustScore();

    $eventCounts = MerchantTrustRegistry::where('merchant_id', $merchant->id)
      ->selectRaw('event_type, count(*) as count')
      ->groupBy('event_type')
      ->pluck('count', 'event_type')
      ->toArray();

    return view('trust-registry.index', compact(
      'entries',
      'totalPenalty',
      'trustScore',
      'eventCounts',
    ));
  }
}
