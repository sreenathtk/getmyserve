<?php

return [
    'endpoint' => env('WATI_API_ENDPOINT'),
    'token'    => env('WATI_API_TOKEN'),

    'templates' => [
        // Customer notifications
        'customer_welcome'             => 'getmyserve_welcome',
        'customer_enquiry_received'    => 'getmyserve_enquiry_received',
        'customer_enquiry_status'      => 'getmyserve_enquiry_status_updated',
        'customer_enquiry_completed'   => 'getmyserve_enquiry_completed',
        'customer_order_confirmed'     => 'getmyserve_order_confirmed',
        'customer_order_status'        => 'getmyserve_order_status_updated',
        'customer_order_completed'     => 'getmyserve_order_completed',
        // Service provider notifications
        'provider_enquiry_assigned'    => 'getmyserve_provider_enquiry_assigned',
        'provider_order_assigned'      => 'getmyserve_provider_order_assigned',
        'provider_status_request'      => 'getmyserve_provider_status_request',
        'provider_new_service_enquiry' => 'getmyserve_provider_new_service_enquiry',
    ],
];
