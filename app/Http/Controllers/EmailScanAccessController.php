<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailScanAccessController extends Controller
{
    public function __invoke(Request $request, Scan $scan): RedirectResponse
    {
        abort_unless($request->user()->id === $scan->user_id, 403);

        return redirect()->route('scans.show', $scan);
    }
}
