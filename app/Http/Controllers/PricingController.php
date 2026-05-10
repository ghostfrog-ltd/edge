<?php

namespace App\Http\Controllers;

use App\Support\BillingCatalog;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __invoke(BillingCatalog $catalog): View
    {
        $offers = $catalog->allOffers();

        return view('pricing', [
            'plans' => $offers['plans'],
            'topUps' => $offers['topUps'],
        ]);
    }
}
