<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScanPdfController extends Controller
{
    public function __invoke(Request $request, Scan $scan): Response
    {
        abort_unless($request->user()->currentTeam?->is($scan->team), 404);
        abort_unless($scan->status === 'completed' && $scan->report, 404);

        $scan->loadMissing(['report', 'team', 'user', 'evidenceListings']);

        $pdf = Pdf::loadView('scans.pdf', [
            'scan' => $scan,
        ])->setPaper('a4');

        $filename = sprintf(
            'ghostfrog-edge-report-%s-scan-%d.pdf',
            Str::slug($scan->keyword ?: 'scan'),
            $scan->id
        );

        return $pdf->download($filename);
    }
}
