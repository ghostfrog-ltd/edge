<?php

namespace App\Http\Controllers;

use App\Models\CreditLedger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCreditLedgerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $entries = CreditLedger::query()
            ->with(['team', 'user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('type', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('team', fn ($team) => $team->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', fn ($user) => $user->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.credits.index', [
            'entries' => $entries,
            'search' => $search,
        ]);
    }
}
