<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DisputeController extends Controller
{
    public function index(): View
    {
        $merchant = auth('merchant')->user();

        $disputes = Dispute::with('transaction')
            ->where('merchant_id', $merchant->id)
            ->latest()
            ->paginate(20);

        return view('disputes.index', compact('disputes'));
    }

    public function show(string $ulid): View
    {
        $merchant = auth('merchant')->user();

        $dispute = Dispute::with(['transaction', 'transaction.evidenceBundle'])
            ->where('ulid', $ulid)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();

        return view('disputes.show', compact('dispute'));
    }

    public function downloadPdf(string $ulid): Response
    {
        $merchant = auth('merchant')->user();

        $dispute = Dispute::with(['transaction', 'transaction.evidenceBundle'])
            ->where('ulid', $ulid)
            ->where('merchant_id', $merchant->id)
            ->firstOrFail();

        if (!$dispute->response_document) {
            abort(404, 'No response document available.');
        }

        $pdf = Pdf::loadView('pdf.dispute-response', [
            'doc' => $dispute->response_document,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("dispute-response-{$dispute->ulid}.pdf");
    }
}
