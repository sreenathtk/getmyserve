@extends('admin.layouts.app')
@section('title', 'Add Service Provider')
@section('page-title', 'Add Service Provider')

@section('styles')
<style>
    .step-indicator { display: flex; justify-content: center; margin-bottom: 2rem; }
    .step-indicator .step {
        display: flex; align-items: center; padding: 8px 16px; margin: 0 4px;
        background: #f5f6f8; border-radius: 4px; font-size: 13px; color: #74788d;
        cursor: pointer; transition: all 0.2s;
    }
    .step-indicator .step.active { background: #5664d2; color: #fff; }
    .step-indicator .step.completed { background: #34c38f; color: #fff; }
    .step-indicator .step i { margin-right: 6px; }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
    .service-row { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px; }
</style>
@endsection

@section('content')
<form action="{{ route('admin.service-providers.store') }}" method="POST" enctype="multipart/form-data" id="providerForm">
    @csrf

    {{-- Step Indicators --}}
    <div class="step-indicator">
        <div class="step active" data-step="1"><i class="ri-building-line"></i> Company Profile</div>
        <div class="step" data-step="2"><i class="ri-contacts-line"></i> Contact Details</div>
        <div class="step" data-step="3"><i class="ri-map-pin-line"></i> Location</div>
        <div class="step" data-step="4"><i class="ri-stack-line"></i> Service Mgmt</div>
        <div class="step" data-step="5"><i class="ri-briefcase-line"></i> Business Ops</div>
        <div class="step" data-step="6"><i class="ri-bank-line"></i> Bank Details</div>
        <div class="step" data-step="7"><i class="ri-message-2-line"></i> Communication</div>
        <div class="step" data-step="8"><i class="ri-shield-check-line"></i> Compliances</div>
        <div class="step" data-step="9"><i class="ri-file-upload-line"></i> Documents</div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Step 1: Company Profile --}}
    <div class="tab-pane active" id="step-1">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Company Profile</h4>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Company name <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="company_name" placeholder="Name" value="{{ old('company_name') }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Company Logo</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="company_logo" accept="image/*">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Trade license</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="trade_license" placeholder="License number" value="{{ old('trade_license') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">License expiry date</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="date" name="license_expiry_date" value="{{ old('license_expiry_date') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Business activity</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="business_activity" placeholder="Business activity" value="{{ old('business_activity') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Select company type</label>
                    <div class="col-sm-10">
                        <select class="form-select" name="company_type">
                            <option value="">Select company type</option>
                            <option value="LLC" {{ old('company_type') == 'LLC' ? 'selected' : '' }}>LLC</option>
                            <option value="Sole Proprietorship" {{ old('company_type') == 'Sole Proprietorship' ? 'selected' : '' }}>Sole Proprietorship</option>
                            <option value="Free Zone" {{ old('company_type') == 'Free Zone' ? 'selected' : '' }}>Free Zone</option>
                            <option value="Partnership" {{ old('company_type') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Website</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="website" placeholder="Website" value="{{ old('website') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Company email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" name="company_email" placeholder="company@example.com" value="{{ old('company_email') }}">
                    </div>
                </div>

                <hr>
                <h5 class="font-size-14 mb-3">Login Credentials</h5>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Login Email <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" name="email" placeholder="Login email for provider" value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Password <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="password" placeholder="Password" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 2: Contact Details --}}
    <div class="tab-pane" id="step-2">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Contact Details</h4>
                <h5 class="font-size-14 mb-4">Primary Contact</h5>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="primary_contact_name" placeholder="Name" value="{{ old('primary_contact_name') }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Mobile number <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="tel" name="primary_contact_mobile" placeholder="+91" value="{{ old('primary_contact_mobile') }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" name="primary_contact_email" placeholder="Email" value="{{ old('primary_contact_email') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Preferred languages</label>
                    <div class="col-sm-10">
                        <select class="form-select" name="primary_contact_language">
                            <option value="">Select languages</option>
                            <option value="English" {{ old('primary_contact_language') == 'English' ? 'selected' : '' }}>English</option>
                            <option value="Arabic" {{ old('primary_contact_language') == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                            <option value="Hindi" {{ old('primary_contact_language') == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                            <option value="Urdu" {{ old('primary_contact_language') == 'Urdu' ? 'selected' : '' }}>Urdu</option>
                            <option value="Malayalam" {{ old('primary_contact_language') == 'Malayalam' ? 'selected' : '' }}>Malayalam</option>
                            <option value="Tamil" {{ old('primary_contact_language') == 'Tamil' ? 'selected' : '' }}>Tamil</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Designation</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="primary_contact_designation" placeholder="Designation" value="{{ old('primary_contact_designation') }}">
                    </div>
                </div>

                <h5 class="font-size-14 mb-4 mt-4">Secondary Contact</h5>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Name</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="secondary_contact_name" placeholder="Name" value="{{ old('secondary_contact_name') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Mobile number</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="tel" name="secondary_contact_mobile" placeholder="+91" value="{{ old('secondary_contact_mobile') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" name="secondary_contact_email" placeholder="Email" value="{{ old('secondary_contact_email') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Preferred languages</label>
                    <div class="col-sm-10">
                        <select class="form-select" name="secondary_contact_language">
                            <option value="">Select languages</option>
                            <option value="English">English</option>
                            <option value="Arabic">Arabic</option>
                            <option value="Hindi">Hindi</option>
                            <option value="Urdu">Urdu</option>
                            <option value="Malayalam">Malayalam</option>
                            <option value="Tamil">Tamil</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Designation</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="secondary_contact_designation" placeholder="Designation" value="{{ old('secondary_contact_designation') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 3: Location Details --}}
    <div class="tab-pane" id="step-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Location Details</h4>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Head office address (full)</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="head_office_address" placeholder="Office address" value="{{ old('head_office_address') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Emirate/City</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="emirate_city" placeholder="Emirate/City" value="{{ old('emirate_city') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Google map location pin</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="google_map_pin" placeholder="Pin URL" value="{{ old('google_map_pin') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Branch details</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="branch_details" placeholder="Branch details" value="{{ old('branch_details') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 4: Service Management --}}
    <div class="tab-pane" id="step-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Service Management</h4>

                <div id="servicesList">
                    <div class="service-row" id="service-row-template">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Service name</label>
                            <div class="col-sm-10">
                                <select class="form-select" name="services[]">
                                    <option value="">Select service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Turn around time</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="text" name="turnaround_times[]" placeholder="e.g. 2 hours, 1 day">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">B2B Price</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="number" step="0.01" name="b2b_prices[]" placeholder="Price">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Markup%</label>
                            <div class="col-sm-10">
                                <select class="form-select svc-markup" name="markup_percents[]">
                                    <option value="">Select</option>
                                    <option value="5">5%</option>
                                    <option value="10">10%</option>
                                    <option value="15">15%</option>
                                    <option value="20">20%</option>
                                    <option value="25">25%</option>
                                    <option value="30">30%</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Market Price (AED)</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="number" step="0.01" name="market_prices[]" placeholder="Market / retail price">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Final Price (AED)</label>
                            <div class="col-sm-10">
                                <input class="form-control svc-final-price" type="number" step="0.01" name="final_prices[]" placeholder="Auto-calculated" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Remarks</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="text" name="service_remarks[]" placeholder="Remarks">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success btn-sm mt-2" id="addServiceBtn">
                    <i class="ri-add-line me-1"></i> Add Another Service
                </button>
            </div>
        </div>
    </div>

    {{-- Step 5: Business Operations --}}
    <div class="tab-pane" id="step-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Business Operations</h4>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Working days</label>
                    <div class="col-sm-10">
                        <select class="form-select" name="working_days">
                            <option value="">Select</option>
                            <option value="Mon-Fri">Monday to Friday</option>
                            <option value="Mon-Sat">Monday to Saturday</option>
                            <option value="Sun-Thu">Sunday to Thursday</option>
                            <option value="All Days">All Days</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Working hours (From)</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="time" name="working_hours_from" value="{{ old('working_hours_from') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Working hours (To)</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="time" name="working_hours_to" value="{{ old('working_hours_to') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 6: Bank Details --}}
    <div class="tab-pane" id="step-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Bank Details</h4>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Bank Name</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="bank_name" placeholder="Bank name" value="{{ old('bank_name') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Account name</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="account_name" placeholder="Account holder name" value="{{ old('account_name') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">IBAN/Account</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="iban_account" placeholder="IBAN/Account number" value="{{ old('iban_account') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Branch</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="bank_branch" placeholder="Branch" value="{{ old('bank_branch') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Swift code (Optional)</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="swift_code" placeholder="SWIFT code" value="{{ old('swift_code') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 7: Communication --}}
    <div class="tab-pane" id="step-7">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Communication Details</h4>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Mobile number</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="tel" name="comm_mobile" placeholder="+91" value="{{ old('comm_mobile') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Preferred communication language</label>
                    <div class="col-sm-10">
                        <select class="form-select" name="comm_language">
                            <option value="">Select language</option>
                            <option value="English">English</option>
                            <option value="Arabic">Arabic</option>
                            <option value="Hindi">Hindi</option>
                            <option value="Urdu">Urdu</option>
                            <option value="Malayalam">Malayalam</option>
                            <option value="Tamil">Tamil</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 8: Compliances & Verification --}}
    <div class="tab-pane" id="step-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Compliances & Verification</h4>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">KYC Status</label>
                    <div class="col-sm-10">
                        <select class="form-select" name="kyc_status">
                            <option value="yes">Yes</option>
                            <option value="no" selected>No</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Verified by</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="verified_by" placeholder="Verification" value="{{ old('verified_by') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Approval status</label>
                    <div class="col-sm-10">
                        <select class="form-select" name="approval_status">
                            <option value="pending" selected>Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Trusted Service Provider</label>
                    <div class="col-sm-10 d-flex align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_trusted" id="is_trusted" value="1" {{ old('is_trusted') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_trusted">Mark as Trusted</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 9: Document Upload --}}
    <div class="tab-pane" id="step-9">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Document Upload</h4>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Trade license copy (Upload)</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="trade_license_copy">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Emirates ID</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="emirates_id_doc" placeholder="Emirates ID" value="{{ old('emirates_id_doc') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Passport</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="passport_copy">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Visa copy</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="visa_copy">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">VAT Certificate</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="vat_certificate">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Ejari/Tenancy contract</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="ejari_contract">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Owner Emirates ID</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="owner_emirates_id" placeholder="ID" value="{{ old('owner_emirates_id') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Company stamp (Optional)</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="company_stamp">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Signboard image (Upload)</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="signboard_image">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation Buttons --}}
    <div class="d-flex justify-content-between mt-3 mb-5">
        <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;">
            <i class="ri-arrow-left-line me-1"></i> Previous
        </button>
        <div class="ms-auto">
            <button type="button" class="btn btn-primary" id="nextBtn">
                Next <i class="ri-arrow-right-line ms-1"></i>
            </button>
            <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">
                <i class="ri-save-line me-1"></i> Save Service Provider
            </button>
        </div>
    </div>
</form>

@section('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 9;

    function showStep(step) {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.getElementById('step-' + step).classList.add('active');

        document.querySelectorAll('.step-indicator .step').forEach((s, i) => {
            s.classList.remove('active', 'completed');
            if (i + 1 < step) s.classList.add('completed');
            if (i + 1 === step) s.classList.add('active');
        });

        document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-block';
        document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'inline-block';
        document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-block' : 'none';
    }

    document.getElementById('nextBtn').addEventListener('click', () => {
        if (currentStep < totalSteps) { currentStep++; showStep(currentStep); }
    });
    document.getElementById('prevBtn').addEventListener('click', () => {
        if (currentStep > 1) { currentStep--; showStep(currentStep); }
    });

    // Click on step indicator to jump
    document.querySelectorAll('.step-indicator .step').forEach(s => {
        s.addEventListener('click', () => {
            currentStep = parseInt(s.getAttribute('data-step'));
            showStep(currentStep);
        });
    });

    function calcFinalPrice(row) {
        const b2b = parseFloat(row.querySelector('[name="b2b_prices[]"]').value) || 0;
        const markup = parseFloat(row.querySelector('.svc-markup').value) || 0;
        const finalInput = row.querySelector('.svc-final-price');
        finalInput.value = (b2b > 0 && markup > 0) ? (b2b * (1 + markup / 100)).toFixed(2) : '';
    }

    function wireServiceRow(row) {
        row.querySelector('[name="b2b_prices[]"]').addEventListener('input', () => calcFinalPrice(row));
        row.querySelector('.svc-markup').addEventListener('change', () => calcFinalPrice(row));
    }

    // Wire existing row
    document.querySelectorAll('.service-row').forEach(wireServiceRow);

    // Add service row
    document.getElementById('addServiceBtn').addEventListener('click', function() {
        const template = document.getElementById('service-row-template');
        const clone = template.cloneNode(true);
        clone.removeAttribute('id');
        clone.querySelectorAll('input, select').forEach(el => el.value = '');
        document.getElementById('servicesList').appendChild(clone);
        wireServiceRow(clone);
    });
</script>
@endsection
@endsection
