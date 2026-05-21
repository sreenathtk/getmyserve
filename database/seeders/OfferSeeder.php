<?php

namespace Database\Seeders;

use App\Models\Offer;
use App\Models\Service;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        $serviceMap = Service::whereIn('name', [
            'Corporate Tax Registration',
            'Bookkeeping',
            'Employment Visa Application',
        ])->pluck('id', 'name');

        $offers = [
            [
                'service_id'   => $serviceMap['Corporate Tax Registration'],
                'offer_type'   => 'best_value',
                'title'        => 'Corporate Tax Registration',
                'offer_price'  => 150.00,
                'offer_detail' => 'Expert guidance for corporate tax registration and full compliance with UAE tax laws.',
                'button_text'  => 'Claim Offer',
                'start_date'   => now()->toDateString(),
                'end_date'     => now()->addDays(30)->toDateString(),
                'is_featured'  => true,
                'is_active'    => true,
                'sort_order'   => 1,
            ],
            [
                'service_id'   => $serviceMap['Bookkeeping'],
                'offer_type'   => 'limited_offer',
                'title'        => 'Monthly Bookkeeping',
                'offer_price'  => 299.00,
                'offer_detail' => 'Professional bookkeeping service with monthly financial reports and reconciliation.',
                'button_text'  => 'Get Started',
                'start_date'   => now()->toDateString(),
                'end_date'     => now()->addDays(15)->toDateString(),
                'is_featured'  => false,
                'is_active'    => true,
                'sort_order'   => 2,
            ],
            [
                'service_id'   => $serviceMap['Employment Visa Application'],
                'offer_type'   => 'flash_sale',
                'title'        => 'Employment Visa Application',
                'offer_price'  => 499.00,
                'offer_detail' => 'Complete employment visa processing including document preparation and submission.',
                'button_text'  => 'Apply Now',
                'start_date'   => now()->toDateString(),
                'end_date'     => now()->addDays(7)->toDateString(),
                'is_featured'  => false,
                'is_active'    => true,
                'sort_order'   => 3,
            ],
        ];

        foreach ($offers as $offer) {
            Offer::create($offer);
        }
    }
}
