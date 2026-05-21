<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Service;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Finance Services',
                'description' => 'Financial, accounting and banking related services',
                'sub_categories' => [
                    [
                        'name' => 'Tax Services',
                        'description' => 'VAT, corporate tax and related compliance services',
                        'services' => [
                            ['name' => 'VAT Registration', 'description' => 'Register your business for VAT with the Federal Tax Authority', 'service_type' => 'book_now'],
                            ['name' => 'VAT Deregistration', 'description' => 'Deregister your business from VAT obligations', 'service_type' => 'book_now'],
                            ['name' => 'VAT Filing', 'description' => 'Prepare and submit your periodic VAT returns', 'service_type' => 'both'],
                            ['name' => 'Corporate Tax Registration', 'description' => 'Register your business for corporate tax compliance', 'service_type' => 'book_now'],
                            ['name' => 'Corporate Tax Filing', 'description' => 'Prepare and file corporate tax returns', 'service_type' => 'both'],
                        ],
                    ],
                    [
                        'name' => 'Accounting Services',
                        'description' => 'Bookkeeping, financial statements and auditing',
                        'services' => [
                            ['name' => 'Bookkeeping', 'description' => 'Maintain accurate financial records for your business', 'service_type' => 'both'],
                            ['name' => 'Financial Statement Preparation', 'description' => 'Prepare balance sheets, income statements and cash flow reports', 'service_type' => 'enquire_now'],
                            ['name' => 'Audit Assistance', 'description' => 'Support during internal or external audits', 'service_type' => 'enquire_now'],
                        ],
                    ],
                    [
                        'name' => 'Banking Services',
                        'description' => 'Business bank account opening and related services',
                        'services' => [
                            ['name' => 'Business Bank Account Opening', 'description' => 'Assistance opening a corporate bank account in the UAE', 'service_type' => 'enquire_now'],
                            ['name' => 'WPS Registration', 'description' => 'Register with the Wages Protection System for salary compliance', 'service_type' => 'book_now'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Visa & Immigration Services',
                'description' => 'UAE visa applications, renewals and immigration support',
                'sub_categories' => [
                    [
                        'name' => 'Employment Visas',
                        'description' => 'Work permit and employment visa services',
                        'services' => [
                            ['name' => 'Employment Visa Application', 'description' => 'New employment visa processing for UAE residents', 'service_type' => 'book_now'],
                            ['name' => 'Employment Visa Renewal', 'description' => 'Renew an existing employment visa', 'service_type' => 'book_now'],
                            ['name' => 'Employment Visa Cancellation', 'description' => 'Cancel an employment visa upon leaving a company', 'service_type' => 'book_now'],
                        ],
                    ],
                    [
                        'name' => 'Investor & Partner Visas',
                        'description' => 'Business investor and partner visa services',
                        'services' => [
                            ['name' => 'Investor Visa Application', 'description' => 'Apply for an investor visa in the UAE', 'service_type' => 'enquire_now'],
                            ['name' => 'Partner Visa Application', 'description' => 'Apply for a partner visa for a UAE-registered company', 'service_type' => 'enquire_now'],
                        ],
                    ],
                    [
                        'name' => 'Dependent Visas',
                        'description' => 'Family and dependent visa sponsorship',
                        'services' => [
                            ['name' => 'Family Visa Sponsorship', 'description' => 'Sponsor a family member\'s UAE residency visa', 'service_type' => 'both'],
                            ['name' => 'Maid / Domestic Worker Visa', 'description' => 'Visa processing for domestic workers', 'service_type' => 'book_now'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Legal Services',
                'description' => 'Legal consultancy, documentation and compliance services',
                'sub_categories' => [
                    [
                        'name' => 'Business Setup',
                        'description' => 'Company formation and trade license services',
                        'services' => [
                            ['name' => 'Mainland Company Formation', 'description' => 'Incorporate a mainland LLC or sole establishment in the UAE', 'service_type' => 'enquire_now'],
                            ['name' => 'Free Zone Company Formation', 'description' => 'Set up a free zone entity in the UAE', 'service_type' => 'enquire_now'],
                            ['name' => 'Trade License Renewal', 'description' => 'Renew your existing UAE trade license', 'service_type' => 'book_now'],
                        ],
                    ],
                    [
                        'name' => 'Contracts & Documentation',
                        'description' => 'Legal drafting, review and attestation services',
                        'services' => [
                            ['name' => 'Contract Drafting', 'description' => 'Draft legally sound contracts for business or personal use', 'service_type' => 'enquire_now'],
                            ['name' => 'Document Attestation', 'description' => 'Attest documents for official use in the UAE', 'service_type' => 'book_now'],
                            ['name' => 'Power of Attorney', 'description' => 'Prepare and notarize a power of attorney document', 'service_type' => 'both'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($data as $catData) {
            $category = Category::create([
                'name' => $catData['name'],
                'description' => $catData['description'],
                'is_active' => true,
            ]);

            foreach ($catData['sub_categories'] as $subCatData) {
                $subCategory = $category->subCategories()->create([
                    'name' => $subCatData['name'],
                    'description' => $subCatData['description'],
                    'is_active' => true,
                ]);

                foreach ($subCatData['services'] as $serviceData) {
                    Service::create([
                        'sub_category_id' => $subCategory->id,
                        'name' => $serviceData['name'],
                        'description' => $serviceData['description'],
                        'service_type' => $serviceData['service_type'],
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
