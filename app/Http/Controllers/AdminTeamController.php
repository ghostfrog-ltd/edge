<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTeamController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $teams = Team::query()
            ->with(['owner'])
            ->withCount(['users', 'scans'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.teams.index', [
            'teams' => $teams,
            'search' => $search,
        ]);
    }
}
