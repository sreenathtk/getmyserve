@extends('layouts.app')

@php
    /* Determine which step to open when redirected back with validation errors */
    $initialStep = 1;
    if ($errors->has('trade_license_copy')) $initialStep = 8;
    if ($errors->hasAny(['head_office_address', 'emirate_city'])) $initialStep = 3;
    if ($errors->hasAny(['primary_contact_name', 'primary_contact_mobile'])) $initialStep = 2;
    if ($errors->hasAny(['email', 'password', 'company_name', 'trade_license', 'license_expiry_date'])) $initialStep = 1;
@endphp

@section('title', 'Register as Service Provider | GetMyServe')

@push('styles')
<link rel="stylesheet" href="/assets/css/register-provider.css">
@endpush

@section('content')

{{-- ── Banner ──────────────────────────────────────────────── --}}
<section class="about-banner ban-register">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="about-banner-content">
                    <h2>Service Provider Registration</h2>
                    <h4>
                        <a href="{{ url('/') }}">home</a>
                        <span>// Register</span>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Registration Section ────────────────────────────────── --}}
<section class="register-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-xl-10 col-lg-11 col-12 mx-auto">

                <div class="multi-reg-wrapper">

                    <div class="reg-header">
                        <h2>Register Your Company</h2>
                        <p>Complete all steps below to apply as a verified service provider on GetMyServe</p>
                    </div>

                    {{-- Success message --}}
                    @if(session('success'))
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                            <i class="fas fa-check-circle fa-lg"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    {{-- Validation errors summary --}}
                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following:</strong>
                            <ul class="mb-0 mt-2 ps-3" style="font-size:13px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @unless(session('success'))

                    {{-- ── Step Indicator Bar ───────────────────────────── --}}
                    <div class="reg-step-bar" id="regStepBar">
                        <div class="reg-step active" data-step="1">
                            <div class="reg-step-circle">1</div>
                            <div class="reg-step-label">Profile</div>
                        </div>
                        <div class="reg-step" data-step="2">
                            <div class="reg-step-circle">2</div>
                            <div class="reg-step-label">Contact</div>
                        </div>
                        <div class="reg-step" data-step="3">
                            <div class="reg-step-circle">3</div>
                            <div class="reg-step-label">Location</div>
                        </div>
                        <div class="reg-step" data-step="4">
                            <div class="reg-step-circle">4</div>
                            <div class="reg-step-label">Services</div>
                        </div>
                        <div class="reg-step" data-step="5">
                            <div class="reg-step-circle">5</div>
                            <div class="reg-step-label">Business Ops</div>
                        </div>
                        <div class="reg-step" data-step="6">
                            <div class="reg-step-circle">6</div>
                            <div class="reg-step-label">Bank Details</div>
                        </div>
                        <div class="reg-step" data-step="7">
                            <div class="reg-step-circle">7</div>
                            <div class="reg-step-label">Communication</div>
                        </div>
                        <div class="reg-step" data-step="8">
                            <div class="reg-step-circle">8</div>
                            <div class="reg-step-label">Documents</div>
                        </div>
                    </div>

                    {{-- ================================================================
                         FORM
                    ================================================================ --}}
                    <form action="{{ route('provider-registration.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          id="providerRegForm"
                          novalidate>
                        @csrf

                        {{-- ============================================================
                             STEP 1 – PROFILE
                        ============================================================ --}}
                        <div class="step-pane active" id="step-1">

                            <h4 class="reg-section-title">
                                <span class="title-bar"></span>Login Account
                            </h4>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="reg-label">Login Email <span class="reg-required">*</span></label>
                                    <input type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="Email for portal login"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="reg-label">Password <span class="reg-required">*</span></label>
                                    <input type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           name="password"
                                           id="pwField"
                                           placeholder="Min. 8 characters"
                                           minlength="8"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="reg-label">Confirm Password <span class="reg-required">*</span></label>
                                    <input type="password"
                                           class="form-control"
                                           name="password_confirmation"
                                           id="pwConfirmField"
                                           placeholder="Re-enter password"
                                           required>
                                    <div class="invalid-feedback" id="pwMismatchMsg">Passwords do not match.</div>
                                </div>
                            </div>

                            <h4 class="reg-section-title">
                                <span class="title-bar"></span>Company Profile
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="reg-label">Company Name <span class="reg-required">*</span></label>
                                    <input type="text"
                                           class="form-control @error('company_name') is-invalid @enderror"
                                           name="company_name"
                                           value="{{ old('company_name') }}"
                                           placeholder="Registered company name"
                                           required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Company Email</label>
                                    <input type="email"
                                           class="form-control @error('company_email') is-invalid @enderror"
                                           name="company_email"
                                           value="{{ old('company_email') }}"
                                           placeholder="Official company email">
                                    @error('company_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Trade License Number <span class="reg-required">*</span></label>
                                    <input type="text"
                                           class="form-control @error('trade_license') is-invalid @enderror"
                                           name="trade_license"
                                           value="{{ old('trade_license') }}"
                                           placeholder="Trade license number"
                                           required>
                                    @error('trade_license')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">License Expiry Date <span class="reg-required">*</span></label>
                                    <input type="date"
                                           class="form-control @error('license_expiry_date') is-invalid @enderror"
                                           name="license_expiry_date"
                                           value="{{ old('license_expiry_date') }}"
                                           required>
                                    @error('license_expiry_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Business Activity</label>
                                    <input type="text"
                                           class="form-control"
                                           name="business_activity"
                                           value="{{ old('business_activity') }}"
                                           placeholder="e.g. Financial consultancy, Visa services">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Company Type</label>
                                    <select class="form-select" name="company_type">
                                        <option value="">Select type</option>
                                        @foreach(['LLC','Sole Proprietorship','Free Zone Company','Branch Office','Representative Office'] as $type)
                                            <option value="{{ $type }}" {{ old('company_type') === $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Website</label>
                                    <input type="url"
                                           class="form-control"
                                           name="website"
                                           value="{{ old('website') }}"
                                           placeholder="https://yourcompany.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Company Logo</label>
                                    <input type="file" class="form-control" name="company_logo" accept="image/*">
                                    <small class="text-muted">JPG, PNG – max 2 MB</small>
                                </div>
                            </div>

                        </div>{{-- /step-1 --}}

                        {{-- ============================================================
                             STEP 2 – CONTACT DETAILS
                        ============================================================ --}}
                        <div class="step-pane" id="step-2">

                            <h4 class="reg-section-title">
                                <span class="title-bar"></span>Primary Contact
                            </h4>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="reg-label">Full Name <span class="reg-required">*</span></label>
                                    <input type="text"
                                           class="form-control @error('primary_contact_name') is-invalid @enderror"
                                           name="primary_contact_name"
                                           value="{{ old('primary_contact_name') }}"
                                           placeholder="Primary contact person"
                                           required>
                                    @error('primary_contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Mobile Number <span class="reg-required">*</span></label>
                                    <input type="tel"
                                           class="form-control @error('primary_contact_mobile') is-invalid @enderror"
                                           name="primary_contact_mobile"
                                           value="{{ old('primary_contact_mobile') }}"
                                           placeholder="+971 50 000 0000"
                                           required>
                                    @error('primary_contact_mobile')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Email Address</label>
                                    <input type="email"
                                           class="form-control"
                                           name="primary_contact_email"
                                           value="{{ old('primary_contact_email') }}"
                                           placeholder="Contact email">
                                </div>
                                <div class="col-md-3">
                                    <label class="reg-label">Language</label>
                                    <select class="form-select" name="primary_contact_language">
                                        <option value="">Select</option>
                                        @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $lang)
                                            <option value="{{ $lang }}" {{ old('primary_contact_language') === $lang ? 'selected' : '' }}>
                                                {{ $lang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="reg-label">Designation</label>
                                    <input type="text"
                                           class="form-control"
                                           name="primary_contact_designation"
                                           value="{{ old('primary_contact_designation') }}"
                                           placeholder="e.g. CEO">
                                </div>
                            </div>

                            <h4 class="reg-section-title">
                                <span class="title-bar"></span>Secondary Contact
                                <small class="text-muted fw-normal" style="font-size:13px;">(Optional)</small>
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="reg-label">Full Name</label>
                                    <input type="text"
                                           class="form-control"
                                           name="secondary_contact_name"
                                           value="{{ old('secondary_contact_name') }}"
                                           placeholder="Secondary contact person">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Mobile Number</label>
                                    <input type="tel"
                                           class="form-control"
                                           name="secondary_contact_mobile"
                                           value="{{ old('secondary_contact_mobile') }}"
                                           placeholder="+971 50 000 0000">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Email Address</label>
                                    <input type="email"
                                           class="form-control"
                                           name="secondary_contact_email"
                                           value="{{ old('secondary_contact_email') }}"
                                           placeholder="Contact email">
                                </div>
                                <div class="col-md-3">
                                    <label class="reg-label">Language</label>
                                    <select class="form-select" name="secondary_contact_language">
                                        <option value="">Select</option>
                                        @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $lang)
                                            <option value="{{ $lang }}" {{ old('secondary_contact_language') === $lang ? 'selected' : '' }}>
                                                {{ $lang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="reg-label">Designation</label>
                                    <input type="text"
                                           class="form-control"
                                           name="secondary_contact_designation"
                                           value="{{ old('secondary_contact_designation') }}"
                                           placeholder="e.g. Manager">
                                </div>
                            </div>

                        </div>{{-- /step-2 --}}

                        {{-- ============================================================
                             STEP 3 – LOCATION
                        ============================================================ --}}
                        <div class="step-pane" id="step-3">

                            <h4 class="reg-section-title">
                                <span class="title-bar"></span>Location Details
                            </h4>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="reg-label">Head Office Address <span class="reg-required">*</span></label>
                                    <input type="text"
                                           class="form-control @error('head_office_address') is-invalid @enderror"
                                           name="head_office_address"
                                           value="{{ old('head_office_address') }}"
                                           placeholder="Full office address"
                                           required>
                                    @error('head_office_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Emirate / City <span class="reg-required">*</span></label>
                                    <select class="form-select @error('emirate_city') is-invalid @enderror"
                                            name="emirate_city" required>
                                        <option value="">Select emirate / city</option>
                                        @foreach(['Abu Dhabi','Dubai','Sharjah','Ajman','Umm Al Quwain','Ras Al Khaimah','Fujairah'] as $em)
                                            <option value="{{ $em }}" {{ old('emirate_city') === $em ? 'selected' : '' }}>
                                                {{ $em }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('emirate_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Google Maps Pin URL</label>
                                    <input type="url"
                                           class="form-control"
                                           name="google_map_pin"
                                           value="{{ old('google_map_pin') }}"
                                           placeholder="https://maps.google.com/?q=...">
                                </div>
                                <div class="col-12">
                                    <label class="reg-label">Branch Details <small class="text-muted">(if any)</small></label>
                                    <textarea class="form-control" name="branch_details" rows="3"
                                              placeholder="List any branch offices and their locations">{{ old('branch_details') }}</textarea>
                                </div>
                            </div>

                        </div>{{-- /step-3 --}}

                        {{-- ============================================================
                             STEP 4 – SERVICE DETAILS
                        ============================================================ --}}
                        <div class="step-pane" id="step-4">

                            <h4 class="reg-section-title">
                                <span class="title-bar"></span>Service Details
                            </h4>
                            <p class="text-muted mb-4" style="font-size:14px;">
                                Select every service your company provides. A details form will appear for each selection.
                            </p>

                            @if(empty($grouped))
                                <div class="alert alert-warning d-flex gap-2 align-items-center">
                                    <i class="fas fa-info-circle"></i>
                                    <div>Service list could not be loaded. You may continue – our team will assign services after reviewing your application.</div>
                                </div>
                            @else
                                {{-- Service selection checkboxes --}}
                                @foreach($grouped as $catName => $subcats)
                                <div class="svc-cat-block">
                                    <div class="svc-cat-name">
                                        <i class="fas fa-layer-group me-2"></i>{{ $catName }}
                                    </div>
                                    @foreach($subcats as $subName => $services)
                                        <div class="svc-sub-name">{{ $subName }}</div>
                                        <div>
                                            @foreach($services as $service)
                                                <label class="svc-check-label {{ in_array($service->id, old('selected_services', [])) ? 'checked' : '' }}"
                                                       id="svc-lbl-{{ $service->id }}">
                                                    <input type="checkbox"
                                                           class="svc-checkbox"
                                                           name="selected_services[]"
                                                           value="{{ $service->id }}"
                                                           data-sid="{{ $service->id }}"
                                                           data-sname="{{ $service->name }}"
                                                           {{ in_array($service->id, old('selected_services', [])) ? 'checked' : '' }}>
                                                    {{ $service->name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                                @endforeach

                                {{-- Dynamic detail cards rendered by JS --}}
                                <div id="svcDetailsContainer" class="mt-3"></div>
                            @endif

                        </div>{{-- /step-4 --}}

                        {{-- ============================================================
                             STEP 5 – BUSINESS OPERATIONS
                        ============================================================ --}}
                        <div class="step-pane" id="step-5">

                            <h4 class="reg-section-title">
                                <span class="title-bar"></span>Business Operations
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="reg-label">Working Days</label>
                                    <select class="form-select" name="working_days">
                                        <option value="">Select working days</option>
                                        @foreach(['Mon-Fri' => 'Monday – Friday','Mon-Sat' => 'Monday – Saturday','Sun-Thu' => 'Sunday – Thursday','All Days' => 'All Days (7 days)'] as $val => $label)
                                            <option value="{{ $val }}" {{ old('working_days') === $val ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="reg-label">Working Hours (From)</label>
                                    <input type="time" class="form-control" name="working_hours_from"
                                           value="{{ old('working_hours_from') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="reg-label">Working Hours (To)</label>
                                    <input type="time" class="form-control" name="working_hours_to"
                                           value="{{ old('working_hours_to') }}">
                                </div>
                            </div>

                        </div>{{-- /step-5 --}}

                        {{-- ============================================================
                             STEP 6 – BANK DETAILS
                        ============================================================ --}}
                        <div class="step-pane" id="step-6">

                            <h4 class="reg-section-title">
                                <span class="title-bar"></span>Bank Details
                            </h4>
                            <p class="text-muted mb-4" style="font-size:14px;">
                                Banking information is securely stored and used only for payment processing.
                            </p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="reg-label">Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name"
                                           value="{{ old('bank_name') }}" placeholder="e.g. Emirates NBD">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Account Holder Name</label>
                                    <input type="text" class="form-control" name="account_name"
                                           value="{{ old('account_name') }}" placeholder="Name on the bank account">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">IBAN / Account Number</label>
                                    <input type="text" class="form-control" name="iban_account"
                                           value="{{ old('iban_account') }}" placeholder="AE000000000000000000000">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Branch</label>
                                    <input type="text" class="form-control" name="bank_branch"
                                           value="{{ old('bank_branch') }}" placeholder="Branch name / location">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">SWIFT Code <small class="text-muted">(optional)</small></label>
                                    <input type="text" class="form-control" name="swift_code"
                                           value="{{ old('swift_code') }}" placeholder="SWIFT / BIC code">
                                </div>
                            </div>

                        </div>{{-- /step-6 --}}

                        {{-- ============================================================
                             STEP 7 – COMMUNICATION
                        ============================================================ --}}
                        <div class="step-pane" id="step-7">

                            <h4 class="reg-section-title">
                                <span class="title-bar"></span>Communication Preferences
                            </h4>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="reg-label">Communication Mobile</label>
                                    <input type="tel" class="form-control" name="comm_mobile"
                                           value="{{ old('comm_mobile') }}" placeholder="+971 50 000 0000">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Preferred Language</label>
                                    <select class="form-select" name="comm_language">
                                        <option value="">Select language</option>
                                        @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $lang)
                                            <option value="{{ $lang }}" {{ old('comm_language') === $lang ? 'selected' : '' }}>
                                                {{ $lang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>{{-- /step-7 --}}

                        {{-- ============================================================
                             STEP 8 – DOCUMENTS
                        ============================================================ --}}
                        <div class="step-pane" id="step-8">

                            <h4 class="reg-section-title">
                                <span class="title-bar"></span>Document Upload
                            </h4>
                            <p class="text-muted mb-4" style="font-size:14px;">
                                Accepted formats: PDF, JPG, PNG &nbsp;&middot;&nbsp; Max 5 MB per file
                            </p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="reg-label">
                                        Trade License Copy <span class="reg-required">*</span>
                                    </label>
                                    <input type="file"
                                           class="form-control @error('trade_license_copy') is-invalid @enderror"
                                           name="trade_license_copy"
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           required>
                                    @error('trade_license_copy')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Emirates ID Number</label>
                                    <input type="text" class="form-control" name="emirates_id_doc"
                                           value="{{ old('emirates_id_doc') }}" placeholder="784-XXXX-XXXXXXX-X">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Passport Copy</label>
                                    <input type="file" class="form-control" name="passport_copy"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Visa Copy</label>
                                    <input type="file" class="form-control" name="visa_copy"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">VAT Certificate <small class="text-muted">(if registered)</small></label>
                                    <input type="file" class="form-control" name="vat_certificate"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Ejari / Tenancy Contract</label>
                                    <input type="file" class="form-control" name="ejari_contract"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Owner Emirates ID Number</label>
                                    <input type="text" class="form-control" name="owner_emirates_id"
                                           value="{{ old('owner_emirates_id') }}" placeholder="Owner's Emirates ID">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Company Stamp <small class="text-muted">(optional)</small></label>
                                    <input type="file" class="form-control" name="company_stamp"
                                           accept=".pdf,.jpg,.jpeg,.png">
                                </div>
                                <div class="col-md-6">
                                    <label class="reg-label">Signboard Image</label>
                                    <input type="file" class="form-control" name="signboard_image"
                                           accept=".jpg,.jpeg,.png">
                                </div>
                            </div>

                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                <label class="form-check-label" for="termsCheck" style="font-size:13px;">
                                    I agree to the
                                    <a href="{{ url('/privacy-policy') }}" target="_blank">Terms &amp; Conditions and Privacy Policy</a>
                                </label>
                                <div class="invalid-feedback">You must accept the terms to proceed.</div>
                            </div>

                        </div>{{-- /step-8 --}}

                        {{-- ── Navigation Buttons ─────────────────────────────── --}}
                        <div class="reg-nav" id="regNav">
                            <button type="button" class="btn btn-outline-secondary" id="prevBtn" style="display:none;">
                                <i class="fas fa-arrow-left me-2"></i>Previous
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-primary px-4" id="nextBtn">
                                    Next <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                <button type="submit" class="btn btn-success px-4" id="submitBtn" style="display:none;">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Registration
                                </button>
                            </div>
                        </div>

                    </form>{{-- /providerRegForm --}}

                    @endunless

                </div>{{-- /multi-reg-wrapper --}}

                <p class="text-center text-muted mt-3" style="font-size:13px;">
                    Already registered?
                    <a href="{{ route('login') }}">Sign in here</a>
                </p>

            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── Constants & state ────────────────────────────────── */
    var TOTAL   = 8;
    var current = {{ $initialStep }};

    /* ── Data from old() for restoring service details ────── */
    var oldServices      = @json(old('selected_services', []));
    var oldTurnaround    = @json(old('turnaround_times', []));
    var oldRemarks       = @json(old('service_remarks', []));

    /* ── HTML-escape helper ───────────────────────────────── */
    function esc(str) {
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    /* ── Show a step ──────────────────────────────────────── */
    function showStep(step) {
        document.querySelectorAll('.step-pane').forEach(function (p) {
            p.classList.remove('active');
        });
        document.getElementById('step-' + step).classList.add('active');

        document.querySelectorAll('#regStepBar .reg-step').forEach(function (s, i) {
            var n      = i + 1;
            var circle = s.querySelector('.reg-step-circle');
            s.classList.remove('active', 'completed');
            if (n < step) {
                s.classList.add('completed');
                circle.innerHTML = '<i class="fas fa-check" style="font-size:11px;"></i>';
            } else {
                circle.textContent = n;
                if (n === step) s.classList.add('active');
            }
        });

        document.getElementById('prevBtn').style.display   = step === 1     ? 'none' : 'inline-flex';
        document.getElementById('nextBtn').style.display   = step === TOTAL  ? 'none' : 'inline-flex';
        document.getElementById('submitBtn').style.display = step === TOTAL  ? 'inline-flex' : 'none';

        var bar = document.getElementById('regStepBar');
        if (bar) bar.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /* ── Validate active step ─────────────────────────────── */
    function validateStep(step) {
        var pane   = document.getElementById('step-' + step);
        var fields = pane.querySelectorAll('[required]');
        var valid  = true;

        fields.forEach(function (f) {
            f.classList.remove('is-invalid');
            var empty = f.type === 'checkbox' ? !f.checked
                      : f.type === 'file'     ? f.files.length === 0
                      : f.value.trim() === '';
            if (empty) { f.classList.add('is-invalid'); valid = false; }
        });

        if (step === 1) {
            var pw  = document.getElementById('pwField');
            var pwc = document.getElementById('pwConfirmField');
            if (pw && pwc && pw.value && pwc.value && pw.value !== pwc.value) {
                pwc.classList.add('is-invalid');
                document.getElementById('pwMismatchMsg').textContent = 'Passwords do not match.';
                valid = false;
            }
        }

        if (!valid) {
            var first = pane.querySelector('.is-invalid');
            if (first) { first.scrollIntoView({ behavior: 'smooth', block: 'center' }); first.focus(); }
        }
        return valid;
    }

    /* ── Navigation buttons ───────────────────────────────── */
    document.getElementById('nextBtn').addEventListener('click', function () {
        if (validateStep(current) && current < TOTAL) { current++; showStep(current); }
    });
    document.getElementById('prevBtn').addEventListener('click', function () {
        if (current > 1) { current--; showStep(current); }
    });

    /* ── Clickable step indicators ────────────────────────── */
    document.querySelectorAll('#regStepBar .reg-step').forEach(function (s) {
        s.addEventListener('click', function () {
            var target = parseInt(this.getAttribute('data-step'), 10);
            if (target < current) {
                current = target; showStep(current);
            } else if (target === current + 1 && validateStep(current)) {
                current = target; showStep(current);
            }
        });
    });

    /* ── Clear invalid on input ───────────────────────────── */
    document.querySelectorAll('.form-control, .form-select, .form-check-input').forEach(function (el) {
        ['input', 'change'].forEach(function (evt) {
            el.addEventListener(evt, function () { this.classList.remove('is-invalid'); });
        });
    });

    /* ── Service detail card builder ──────────────────────── */
    function buildServiceCard(id, name, oldData) {
        oldData = oldData || {};

        return '<div class="svc-detail-card" id="svc-detail-' + id + '">'
            +   '<div class="svc-card-title"><i class="fas fa-cog"></i>' + esc(name) + '</div>'
            +   '<div class="row g-3">'
            +     '<div class="col-md-6">'
            +       '<label class="reg-label">Turnaround Time</label>'
            +       '<input type="text" class="form-control" name="turnaround_times[' + id + ']"'
            +       ' value="' + esc(oldData.turnaround || '') + '" placeholder="e.g. 2 working days">'
            +     '</div>'
            +     '<div class="col-md-6">'
            +       '<label class="reg-label">Remarks</label>'
            +       '<input type="text" class="form-control" name="service_remarks[' + id + ']"'
            +       ' value="' + esc(oldData.remark || '') + '" placeholder="Any specific notes">'
            +     '</div>'
            +   '</div>'
            + '</div>';
    }

    /* ── Service checkbox toggling ────────────────────────── */
    function wireCheckbox(cb) {
        cb.addEventListener('change', function () {
            var id        = this.dataset.sid;
            var name      = this.dataset.sname;
            var container = document.getElementById('svcDetailsContainer');
            var lbl       = document.getElementById('svc-lbl-' + id);

            if (this.checked) {
                if (lbl) lbl.classList.add('checked');
                if (!document.getElementById('svc-detail-' + id)) {
                    var wrap = document.createElement('div');
                    wrap.innerHTML = buildServiceCard(id, name, {});
                    container.appendChild(wrap.firstElementChild);
                }
            } else {
                if (lbl) lbl.classList.remove('checked');
                var existing = document.getElementById('svc-detail-' + id);
                if (existing) existing.remove();
            }
        });
    }

    document.querySelectorAll('.svc-checkbox').forEach(wireCheckbox);

    /* ── Restore old service selections on validation error ── */
    if (oldServices.length > 0) {
        oldServices.forEach(function (id) {
            var cb  = document.querySelector('.svc-checkbox[data-sid="' + id + '"]');
            var lbl = document.getElementById('svc-lbl-' + id);
            var container = document.getElementById('svcDetailsContainer');
            if (!cb || !container) return;

            cb.checked = true;
            if (lbl) lbl.classList.add('checked');

            var oldData = {
                turnaround : oldTurnaround[id] || '',
                remark     : oldRemarks[id]    || '',
            };

            if (!document.getElementById('svc-detail-' + id)) {
                var wrap = document.createElement('div');
                wrap.innerHTML = buildServiceCard(id, cb.dataset.sname, oldData);
                container.appendChild(wrap.firstElementChild);
            }
        });
    }

    /* ── Init ─────────────────────────────────────────────── */
    showStep(current);

}());
</script>
@endpush
