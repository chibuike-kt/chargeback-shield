<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
  public function index(Request $request): View
  {
    $merchant = auth('merchant')->user();

    $query = AuditLog::where('merchant_id', $merchant->id)
      ->orderByDesc('created_at');

    // Filter by action
    if ($request->filled('action')) {
      $query->where('action', $request->action);
    }

    // Filter by resource type
    if ($request->filled('resource')) {
      $query->where('resource_type', $request->resource);
    }

    $logs = $query->paginate(30)->withQueryString();

    // Get distinct actions for filter dropdown
    $actions = AuditLog::where('merchant_id', $merchant->id)
      ->distinct()
      ->pluck('action')
      ->sort()
      ->values();

    $resources = AuditLog::where('merchant_id', $merchant->id)
      ->distinct()
      ->pluck('resource_type')
      ->filter()
      ->sort()
      ->values();

    $totalCount = AuditLog::where('merchant_id', $merchant->id)->count();

    return view('audit.index', compact(
      'logs',
      'actions',
      'resources',
      'totalCount',
    ));
  }
}
