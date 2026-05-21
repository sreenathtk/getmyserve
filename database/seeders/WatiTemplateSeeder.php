<?php

namespace Database\Seeders;

use App\Models\WatiTemplate;
use Illuminate\Database\Seeder;

class WatiTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ── Customer templates ────────────────────────────────────────
            [
                'template_key'   => 'customer_welcome',
                'element_name'   => 'getmyserve_welcome',
                'description'    => 'Sent to a new customer immediately after registration.',
                'recipient_type' => 'customer',
                'body'           => 'Hi {{1}}, welcome to GetMyServe! Your account has been created successfully. We\'re here to help you with all your service needs.',
            ],
            [
                'template_key'   => 'customer_enquiry_received',
                'element_name'   => 'getmyserve_enquiry_received',
                'description'    => 'Sent to the customer when a service enquiry is submitted.',
                'recipient_type' => 'customer',
                'body'           => 'Hi {{1}}, your service enquiry for *{{2}}* has been received (Ref: #{{3}}). Our team will review your request and get back to you shortly. Thank you for choosing GetMyServe!',
            ],
            [
                'template_key'   => 'customer_enquiry_status',
                'element_name'   => 'getmyserve_enquiry_status_updated',
                'description'    => 'Sent to the customer when a service enquiry status changes.',
                'recipient_type' => 'customer',
                'body'           => 'Hi {{1}}, your service enquiry #{{2}} has been updated. New status: *{{3}}*. Please log in to your account to view the full details.',
            ],
            [
                'template_key'   => 'customer_enquiry_completed',
                'element_name'   => 'getmyserve_enquiry_completed',
                'description'    => 'Sent to the customer when a service enquiry is completed or resolved.',
                'recipient_type' => 'customer',
                'body'           => 'Hi {{1}}, great news! Your service request for *{{2}}* (Ref: #{{3}}) has been completed. Thank you for choosing GetMyServe! We\'d love to hear your feedback.',
            ],
            [
                'template_key'   => 'customer_order_confirmed',
                'element_name'   => 'getmyserve_order_confirmed',
                'description'    => 'Sent to the customer when payment is confirmed for an order.',
                'recipient_type' => 'customer',
                'body'           => 'Hi {{1}}, your order #{{2}} has been confirmed! Total: AED {{3}}. We\'ll keep you updated on the progress. Thank you for choosing GetMyServe!',
            ],
            [
                'template_key'   => 'customer_order_status',
                'element_name'   => 'getmyserve_order_status_updated',
                'description'    => 'Sent to the customer when an order status changes.',
                'recipient_type' => 'customer',
                'body'           => 'Hi {{1}}, your order #{{2}} status has been updated to *{{3}}*. Log in to your account to view the details.',
            ],
            [
                'template_key'   => 'customer_order_completed',
                'element_name'   => 'getmyserve_order_completed',
                'description'    => 'Sent to the customer when an order is marked completed.',
                'recipient_type' => 'customer',
                'body'           => 'Hi {{1}}, your order #{{2}} is now complete! Thank you for choosing GetMyServe. We\'d love to hear your feedback.',
            ],

            // ── Service provider templates ────────────────────────────────
            [
                'template_key'   => 'provider_enquiry_assigned',
                'element_name'   => 'getmyserve_provider_enquiry_assigned',
                'description'    => 'Sent to the service provider when a service enquiry is assigned to them.',
                'recipient_type' => 'provider',
                'body'           => 'Hi {{1}}, a new service enquiry has been assigned to you. Service: *{{2}}*, Ref: #{{3}}, Customer: {{4}}. Please log in to your GetMyServe provider dashboard to review and respond.',
            ],
            [
                'template_key'   => 'provider_order_assigned',
                'element_name'   => 'getmyserve_provider_order_assigned',
                'description'    => 'Sent to the service provider when an order is assigned to them.',
                'recipient_type' => 'provider',
                'body'           => 'Hi {{1}}, a new order has been assigned to you. Order #{{2}}, Customer: {{3}}. Please log in to your GetMyServe provider dashboard to review and begin processing.',
            ],
            [
                'template_key'   => 'provider_status_request',
                'element_name'   => 'getmyserve_provider_status_request',
                'description'    => 'Sent to the service provider when staff requests a status update on an enquiry.',
                'recipient_type' => 'provider',
                'body'           => 'Hi {{1}}, an update has been requested for enquiry #{{2}}. Message: *{{3}}*. Please log in to your GetMyServe provider dashboard to respond.',
            ],
            [
                'template_key'   => 'provider_new_service_enquiry',
                'element_name'   => 'getmyserve_provider_new_service_enquiry',
                'description'    => 'Sent to all service providers registered for a service when a new enquiry is submitted for that service.',
                'recipient_type' => 'provider',
                'body'           => 'Hi {{1}}, a new enquiry for *{{2}}* has been received from {{3}}. Log in to your GetMyServe provider dashboard to view the full details.',
            ],
        ];

        foreach ($templates as $data) {
            WatiTemplate::updateOrCreate(
                ['template_key' => $data['template_key']],
                $data,
            );
        }
    }
}
