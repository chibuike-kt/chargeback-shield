<?php

namespace App\Http\Controllers;

use App\Models\WebhookDelivery;
use App\Services\WebhookDispatcher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WebhookController extends Controller
{
  public function __construct(
    private WebhookDispatcher $dispatcher,
  ) {}

  public function index(): View
  {
    $merchant  = auth('merchant')->user();

    $deliveries = WebhookDelivery::with(['transaction', 'dispute'])
      ->where('merchant_id', $merchant->id)
      ->latest()
      ->paginate(25);

    $stats = [
      'total'     => WebhookDelivery::where('merchant_id', $merchant->id)->count(),
      'delivered' => WebhookDelivery::where('merchant_id', $merchant->id)
        ->where('status', 'delivered')->count(),
      'failed'    => WebhookDelivery::where('merchant_id', $merchant->id)
        ->where('status', 'failed')->count(),
      'retrying'  => WebhookDelivery::where('merchant_id', $merchant->id)
        ->where('status', 'retrying')->count(),
    ];

    return view('webhooks.index', compact('deliveries', 'stats'));
  }

  public function retrigger(string $ulid): RedirectResponse
  {
    $merchant = auth('merchant')->user();

    $delivery = WebhookDelivery::where('ulid', $ulid)
      ->where('merchant_id', $merchant->id)
      ->firstOrFail();

    $this->dispatcher->retrigger($delivery);

    session()->flash('success', 'Webhook re-triggered successfully.');

    return back();
  }
}
