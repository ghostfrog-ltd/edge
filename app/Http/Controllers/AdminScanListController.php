<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminScanListController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));
        $status = trim((string) $request->string('status'));

        $scans = Scan::query()
            ->with(['team', 'user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('keyword', 'like', "%{$search}%")
                        ->orWhereHas('team', fn ($team) => $team->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.scans.index', [
            'scans' => $scans,
            'search' => $search,
            'status' => $status,
            'statuses' => ['queued', 'processing', 'completed', 'failed'],
        ]);
    }
}
