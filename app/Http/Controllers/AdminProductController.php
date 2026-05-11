<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => [
                [
                    'name' => 'Fuzzynode Ebay Edge',
                    'slug' => 'fuzzynode-ebay-edge',
                    'status' => 'active-build',
                    'description' => 'eBay market intelligence product with scan intake, credits, reports, and a proprietary analysis engine.',
                    'focus' => 'Active product',
                ],
                [
                    'name' => 'Fuzzynode Auction Radar',
                    'slug' => 'fuzzynode-auction-radar',
                    'status' => 'future',
                    'description' => 'A future tool focused on surfacing unusual auction opportunities, undervalued listings, and fast-moving resale signals.',
                    'focus' => 'Opportunity hunting',
                ],
                [
                    'name' => 'Fuzzynode Listing Doctor',
                    'slug' => 'fuzzynode-listing-doctor',
                    'status' => 'future',
                    'description' => 'A future optimization product focused on improving one listing at a time with titles, specifics, image guidance, and conversion fixes.',
                    'focus' => 'Listing optimization',
                ],
                [
                    'name' => 'Fuzzynode Review Edge',
                    'slug' => 'fuzzynode-review-edge',
                    'status' => 'future',
                    'description' => 'A future insight product focused on mining reviews and buyer complaints to reveal product and messaging gaps competitors ignore.',
                    'focus' => 'Voice of customer',
                ],
            ],
        ]);
    }
}
