<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceProviderController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ChangeRequestController as AdminChangeRequestController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ServiceReviewController as AdminServiceReviewController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ServiceReviewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\AssistanceRequestController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use App\Http\Controllers\Admin\AssistanceRequestController as AdminAssistanceRequestController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\AssistanceRequestController as StaffAssistanceRequestController;
use App\Http\Controllers\Staff\ChangeRequestController as StaffChangeRequestController;
use App\Http\Controllers\Staff\CustomerController as StaffCustomerController;
use App\Http\Controllers\Staff\EnquiryController as StaffEnquiryController;
use App\Http\Controllers\Provider\ChangeRequestController as ProviderChangeRequestController;
use App\Http\Controllers\Provider\EnquiryController as ProviderEnquiryController;
use App\Http\Controllers\Provider\ProfileController as ProviderProfileController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ProviderRegistrationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceBookingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EnquiryPaymentController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\WatiTemplateController;
use App\Http\Controllers\Admin\ZiwoWebhookController;
use App\Http\Controllers\Admin\CallController;
use App\Http\Controllers\Admin\CallLogController;
use App\Http\Controllers\Admin\CallAgentController;
use App\Http\Controllers\Admin\CommunicationHistoryController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Provider\OrderController as ProviderOrderController;

// Public landing page
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search-services', [HomeController::class, 'searchServices'])->name('search.services');
Route::view('/about', 'about')->name('about');
Route::view('/faq', 'faq')->name('faq');
Route::view('/contact', 'contact')->name('contact');

// Browse by category → sub-category
Route::get('/categories', [CategoriesController::class, 'index'])->name('categories.index');

// Services listing (filtered by sub-category)
Route::get('/services', [ServicesController::class, 'index'])->name('services.index');

// Package detail (public)
Route::get('/packages/{offer}', [PackageController::class, 'show'])->name('packages.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/add-offer', [CartController::class, 'addOffer'])->name('cart.addOffer');
Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

// Checkout (Stripe) — requires login
Route::middleware('auth')->group(function () {
    Route::post('/checkout/session', [CheckoutController::class, 'session'])->name('checkout.session');
    Route::post('/packages/{offer}/checkout', [PackageController::class, 'checkout'])->name('packages.checkout');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');

    // Customer orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel-request', [OrderController::class, 'requestCancellation'])->name('orders.cancel-request');
    Route::post('/orders/{order}/refund-request', [OrderController::class, 'requestRefund'])->name('orders.refund-request');
    Route::post('/orders/{order}/items/{item}/cancel', [OrderController::class, 'cancelItem'])->name('orders.items.cancel');

    // Customer profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('/my-requests/{enquiry}', [ProfileController::class, 'enquiryDetail'])->name('profile.enquiry');
    Route::get('/my-assistance/{assistanceRequest}', [ProfileController::class, 'assistanceDetail'])->name('profile.assistance');

    // Enquiry payment (quote → Stripe → success)
    Route::post('/my-requests/{enquiry}/checkout', [EnquiryPaymentController::class, 'checkout'])->name('enquiries.checkout');
    Route::get('/my-requests/{enquiry}/payment/success', [EnquiryPaymentController::class, 'success'])->name('enquiries.payment.success');
});

// Stripe webhook — no CSRF, handled by Stripe signature verification
Route::post('/stripe/webhook', [CheckoutController::class, 'webhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Enquiry — requires login; guests are redirected to /login with intended URL stored
Route::middleware('auth')->group(function () {
    Route::get('/book/{service}', [ServiceBookingController::class, 'create'])->name('book.create');
    Route::post('/book', [ServiceBookingController::class, 'store'])->name('book.store');

    Route::get('/enquiry/{service}', [EnquiryController::class, 'create'])->name('enquiry.create');
    Route::post('/enquiry', [EnquiryController::class, 'store'])->name('enquiry.store');
});

// Assistance Requests — open to all (logged in or guest)
Route::get('/assistance-request', [AssistanceRequestController::class, 'create'])->name('assistance-request.create');
Route::post('/assistance-request', [AssistanceRequestController::class, 'store'])->name('assistance-request.store');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Public provider self-registration
Route::get('/register-provider', [ProviderRegistrationController::class, 'create'])->name('provider-registration.create');
Route::post('/register-provider', [ProviderRegistrationController::class, 'store'])->name('provider-registration.store');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories CRUD
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');

    // Sub-categories CRUD (nested under categories)
    Route::get('/categories/{category}/sub-categories', [SubCategoryController::class, 'index'])->name('categories.sub-categories.index');
    Route::get('/categories/{category}/sub-categories/create', [SubCategoryController::class, 'create'])->name('categories.sub-categories.create');
    Route::post('/categories/{category}/sub-categories', [SubCategoryController::class, 'store'])->name('categories.sub-categories.store');
    Route::get('/categories/{category}/sub-categories/{subCategory}/edit', [SubCategoryController::class, 'edit'])->name('categories.sub-categories.edit');
    Route::put('/categories/{category}/sub-categories/{subCategory}', [SubCategoryController::class, 'update'])->name('categories.sub-categories.update');
    Route::patch('/categories/{category}/sub-categories/{subCategory}/toggle', [SubCategoryController::class, 'toggleStatus'])->name('categories.sub-categories.toggle');

    // Services CRUD
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::patch('/services/{service}/toggle', [ServiceController::class, 'toggleStatus'])->name('services.toggle');
    Route::get('/categories/{category}/sub-categories-list', [ServiceController::class, 'getSubCategories'])->name('categories.sub-categories-list');
    Route::get('/services/{service}/reviews', [AdminServiceReviewController::class, 'index'])->name('services.reviews.index');
    Route::patch('/services/{service}/reviews/{review}/toggle', [AdminServiceReviewController::class, 'toggle'])->name('services.reviews.toggle');

    // Service Providers
    Route::get('/service-providers', [ServiceProviderController::class, 'index'])->name('service-providers.index');
    Route::get('/service-providers/create', [ServiceProviderController::class, 'create'])->name('service-providers.create');
    Route::post('/service-providers', [ServiceProviderController::class, 'store'])->name('service-providers.store');
    Route::get('/service-providers/{serviceProvider}', [ServiceProviderController::class, 'show'])->name('service-providers.show');
    Route::get('/service-providers/{serviceProvider}/edit', [ServiceProviderController::class, 'edit'])->name('service-providers.edit');
    Route::put('/service-providers/{serviceProvider}', [ServiceProviderController::class, 'update'])->name('service-providers.update');
    Route::patch('/service-providers/{serviceProvider}/toggle', [ServiceProviderController::class, 'toggleStatus'])->name('service-providers.toggle');
    Route::patch('/service-providers/{serviceProvider}/toggle-trusted', [ServiceProviderController::class, 'toggleTrusted'])->name('service-providers.toggle-trusted');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::patch('/customers/{customer}/toggle', [CustomerController::class, 'toggleStatus'])->name('customers.toggle');
    Route::post('/customers/{customer}/files/{file}/link', [CustomerController::class, 'linkFile'])->name('customers.files.link');
    Route::post('/customers/{customer}/order-files/{file}/link', [CustomerController::class, 'linkOrderFile'])->name('customers.order-files.link');

    // Staff management
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::patch('/staff/{staff}/toggle', [StaffController::class, 'toggleStatus'])->name('staff.toggle');

    // Service Enquiries
    Route::get('/enquiries', [AdminEnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('/enquiries/{enquiry}', [AdminEnquiryController::class, 'show'])->name('enquiries.show');
    Route::patch('/enquiries/{enquiry}/status', [AdminEnquiryController::class, 'updateStatus'])->name('enquiries.update-status');
    Route::patch('/enquiries/{enquiry}/link-customer', [AdminEnquiryController::class, 'linkCustomer'])->name('enquiries.link-customer');
    Route::patch('/enquiries/{enquiry}/assign-sp', [AdminEnquiryController::class, 'assignSp'])->name('enquiries.assign-sp');
    Route::post('/enquiries/{enquiry}/updates', [AdminEnquiryController::class, 'storeUpdate'])->name('enquiries.updates.store');
    Route::post('/enquiries/{enquiry}/files', [AdminEnquiryController::class, 'storeFiles'])->name('enquiries.files.store');
    Route::delete('/enquiries/{enquiry}/files/{file}', [AdminEnquiryController::class, 'destroyFile'])->name('enquiries.files.destroy');

    // Offers
    Route::get('/offers', [OfferController::class, 'index'])->name('offers.index');
    Route::get('/offers/create', [OfferController::class, 'create'])->name('offers.create');
    Route::post('/offers', [OfferController::class, 'store'])->name('offers.store');
    Route::get('/offers/{offer}/edit', [OfferController::class, 'edit'])->name('offers.edit');
    Route::put('/offers/{offer}', [OfferController::class, 'update'])->name('offers.update');
    Route::patch('/offers/{offer}/toggle', [OfferController::class, 'toggleStatus'])->name('offers.toggle');

    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{order}/assign-staff', [AdminOrderController::class, 'assignStaff'])->name('orders.assign-staff');
    Route::patch('/orders/{order}/assign-sp', [AdminOrderController::class, 'assignSp'])->name('orders.assign-sp');
    Route::patch('/orders/{order}/notes', [AdminOrderController::class, 'saveNotes'])->name('orders.save-notes');
    Route::post('/orders/{order}/files', [AdminOrderController::class, 'storeFiles'])->name('orders.files.store');
    Route::delete('/orders/{order}/files/{file}', [AdminOrderController::class, 'destroyFile'])->name('orders.files.destroy');
    Route::post('/orders/{order}/refund', [AdminOrderController::class, 'refund'])->name('orders.refund');
    Route::post('/orders/{order}/accept-cancellation', [AdminOrderController::class, 'acceptCancellation'])->name('orders.accept-cancellation');

    // Enquiry quote & refund (admin)
    Route::patch('/enquiries/{enquiry}/price', [AdminEnquiryController::class, 'updatePrice'])->name('enquiries.update-price');
    Route::post('/enquiries/{enquiry}/refund', [AdminEnquiryController::class, 'refund'])->name('enquiries.refund');

    // Payments
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');

    // General Settings
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // WhatsApp — Templates
    Route::get('/whatsapp/templates', [WatiTemplateController::class, 'index'])->name('whatsapp.templates.index');
    Route::post('/whatsapp/templates/sync', [WatiTemplateController::class, 'sync'])->name('whatsapp.templates.sync');

    // Assistance Requests
    Route::get('/assistance-requests', [AdminAssistanceRequestController::class, 'index'])->name('assistance-requests.index');
    Route::get('/assistance-requests/{assistanceRequest}', [AdminAssistanceRequestController::class, 'show'])->name('assistance-requests.show');
    Route::patch('/assistance-requests/{assistanceRequest}/assign-staff', [AdminAssistanceRequestController::class, 'assignStaff'])->name('assistance-requests.assign-staff');
    Route::patch('/assistance-requests/{assistanceRequest}/status', [AdminAssistanceRequestController::class, 'updateStatus'])->name('assistance-requests.update-status');

    // Change Requests (from staff & service providers)
    Route::get('/change-requests', [AdminChangeRequestController::class, 'index'])->name('change-requests.index');
    Route::get('/change-requests/{changeRequest}', [AdminChangeRequestController::class, 'show'])->name('change-requests.show');
    Route::patch('/change-requests/{changeRequest}/approve', [AdminChangeRequestController::class, 'approve'])->name('change-requests.approve');
    Route::patch('/change-requests/{changeRequest}/reject', [AdminChangeRequestController::class, 'reject'])->name('change-requests.reject');
});

// Service review page (public) + submission (auth)
Route::get('/services/{service}/review', [ServiceReviewController::class, 'show'])->name('services.review');
Route::middleware('auth')->group(function () {
    Route::post('/services/{service}/reviews', [ServiceReviewController::class, 'store'])->name('reviews.store');
    Route::post('/review-image/upload', [ServiceReviewController::class, 'uploadImage'])->name('reviews.upload-image');
});

// Staff routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    // Service Requests
    Route::get('/enquiries', [StaffEnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('/enquiries/{enquiry}', [StaffEnquiryController::class, 'show'])->name('enquiries.show');
    Route::patch('/enquiries/{enquiry}/status', [StaffEnquiryController::class, 'updateStatus'])->name('enquiries.update-status');
    Route::patch('/enquiries/{enquiry}/price', [StaffEnquiryController::class, 'updatePrice'])->name('enquiries.update-price');
    Route::post('/enquiries/{enquiry}/updates', [StaffEnquiryController::class, 'storeUpdate'])->name('enquiries.updates.store');

    // Customers (read-only)
    Route::get('/customers', [StaffCustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [StaffCustomerController::class, 'show'])->name('customers.show');
    Route::post('/customers/{customer}/files/{file}/link', [StaffCustomerController::class, 'linkFile'])->name('customers.files.link');
    Route::post('/customers/{customer}/order-files/{file}/link', [StaffCustomerController::class, 'linkOrderFile'])->name('customers.order-files.link');

    // Assistance Requests
    Route::get('/assistance-requests', [StaffAssistanceRequestController::class, 'index'])->name('assistance-requests.index');
    Route::get('/assistance-requests/{assistanceRequest}', [StaffAssistanceRequestController::class, 'show'])->name('assistance-requests.show');
    Route::patch('/assistance-requests/{assistanceRequest}', [StaffAssistanceRequestController::class, 'update'])->name('assistance-requests.update');

    // Orders assigned to staff
    Route::get('/orders', [StaffOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [StaffOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [StaffOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('/orders/{order}/assign-sp', [StaffOrderController::class, 'assignSp'])->name('orders.assign-sp');
    Route::post('/orders/{order}/accept-cancellation', [StaffOrderController::class, 'acceptCancellation'])->name('orders.accept-cancellation');

    Route::get('/change-requests', [StaffChangeRequestController::class, 'index'])->name('change-requests.index');
    Route::get('/change-requests/create', [StaffChangeRequestController::class, 'create'])->name('change-requests.create');
    Route::post('/change-requests', [StaffChangeRequestController::class, 'store'])->name('change-requests.store');
    Route::get('/change-requests/{changeRequest}/edit', [StaffChangeRequestController::class, 'edit'])->name('change-requests.edit');
    Route::put('/change-requests/{changeRequest}', [StaffChangeRequestController::class, 'update'])->name('change-requests.update');
});

// Service Provider routes
Route::middleware(['auth', 'role:service_provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('/dashboard', function () {
        return view('provider.dashboard');
    })->name('dashboard');

    Route::get('/orders', [ProviderOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [ProviderOrderController::class, 'show'])->name('orders.show');

    Route::get('/enquiries', [ProviderEnquiryController::class, 'index'])->name('enquiries.index');
    Route::get('/enquiries/{enquiry}', [ProviderEnquiryController::class, 'show'])->name('enquiries.show');
    Route::post('/enquiries/{enquiry}/updates', [ProviderEnquiryController::class, 'storeUpdate'])->name('enquiries.updates.store');

    Route::get('/change-requests/create', [ProviderChangeRequestController::class, 'create'])->name('change-requests.create');
    Route::post('/change-requests', [ProviderChangeRequestController::class, 'store'])->name('change-requests.store');

    Route::get('/change-password', [ProviderProfileController::class, 'showChangePassword'])->name('change-password');
    Route::post('/change-password', [ProviderProfileController::class, 'changePassword']);
});

// ── ZIWO Webhook (no CSRF, no auth) ──────────────────────────────────────────
Route::post('/webhooks/ziwo', [ZiwoWebhookController::class, 'receive'])
     ->name('webhooks.ziwo');

// ── ZIWO Call Center — Admin + Staff ─────────────────────────────────────────
Route::middleware(['auth', 'role:admin,staff'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {

    // Softphone API endpoints (AJAX)
    Route::prefix('calls')->name('calls.')->group(function () {
        Route::post('dial',                       [CallController::class, 'dial'])->name('dial');
        Route::get('agent-token',                 [CallController::class, 'agentToken'])->name('agent-token');
        Route::get('stats',                       [CallController::class, 'stats'])->name('stats');
        Route::post('{callLog}/link',             [CallController::class, 'linkCall'])->name('link');
        Route::post('{callLog}/notes',            [CallController::class, 'addNote'])->name('notes.store');
        Route::get('{callLog}/recording-url',     [CallController::class, 'recordingUrl'])->name('recording-url');
        Route::get('{callLog}/recording/stream',  [CallController::class, 'streamRecording'])->name('recording.stream');
    });

    // Call Logs — browseable UI
    Route::get('call-logs',           [CallLogController::class, 'index'])->name('call-logs.index');
    Route::get('call-logs/{callLog}', [CallLogController::class, 'show'])->name('call-logs.show');

    // Communication Timeline
    Route::prefix('communication')->name('communication.')->group(function () {
        Route::get('/{type}/{id}',      [CommunicationHistoryController::class, 'timeline'])->name('timeline');
        Route::post('/{type}/{id}/note',[CommunicationHistoryController::class, 'addNote'])->name('note');
    });

    // Call Agents
    Route::prefix('call-agents')->name('call-agents.')->group(function () {
        Route::get('/',               [CallAgentController::class, 'index'])->name('index');
        Route::post('/sync',          [CallAgentController::class, 'sync'])->name('sync');
        Route::patch('/{agent}',      [CallAgentController::class, 'update'])->name('update');
    });
});
