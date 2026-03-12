<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $users = User::query()
            ->with(['ownedTeams', 'scans'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function show(User $user): View
    {
        $user->load([
            'ownedTeams.creditLedgers',
            'ownedTeams.scans',
            'scans' => fn ($query) => $query->with('team')->latest()->limit(10),
            'creditLedgers' => fn ($query) => $query->with('team')->latest()->limit(10),
        ]);

        return view('admin.users.show', [
            'user' => $user,
            'ownedTeams' => $user->ownedTeams,
            'recentScans' => $user->scans,
            'recentLedgerEntries' => $user->creditLedgers,
        ]);
    }
}
