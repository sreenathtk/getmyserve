@extends('admin.layouts.app')
@section('title', 'Edit Service Provider')
@section('page-title', 'Edit Service Provider')

@section('styles')
<style>
    .step-indicator { display: flex; justify-content: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 4px; }
    .step-indicator .step {
        display: flex; align-items: center; padding: 8px 16px;
        background: #f5f6f8; border-radius: 4px; font-size: 13px; color: #74788d;
        cursor: pointer; transition: all 0.2s;
    }
    .step-indicator .step.active { background: #5664d2; color: #fff; }
    .step-indicator .step.completed { background: #34c38f; color: #fff; }
    .step-indicator .step i { margin-right: 6px; }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
    .service-row { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px; position: relative; }
    .remove-service { position: absolute; top: 10px; right: 10px; }
</style>
@endsection

@section('content')
<form action="{{ route('admin.service-providers.update', $serviceProvider) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

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
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Step 1: Company Profile --}}
    <div class="tab-pane active" id="step-1">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Company Profile</h4>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Company name <span class="text-danger">*</span></label>
                <div class="col-sm-10"><input class="form-control" type="text" name="company_name" value="{{ old('company_name', $serviceProvider->company_name) }}" required></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Company Logo</label>
                <div class="col-sm-10">
                    @if($serviceProvider->company_logo)<p class="text-muted mb-1"><small>Current: {{ basename($serviceProvider->company_logo) }}</small></p>@endif
                    <input type="file" class="form-control" name="company_logo" accept="image/*">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Trade license</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="trade_license" value="{{ old('trade_license', $serviceProvider->trade_license) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">License expiry date</label>
                <div class="col-sm-10"><input class="form-control" type="date" name="license_expiry_date" value="{{ old('license_expiry_date', $serviceProvider->license_expiry_date?->format('Y-m-d')) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Business activity</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="business_activity" value="{{ old('business_activity', $serviceProvider->business_activity) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Select company type</label>
                <div class="col-sm-10">
                    <select class="form-select" name="company_type">
                        <option value="">Select</option>
                        @foreach(['LLC','Sole Proprietorship','Free Zone','Partnership'] as $t)
                        <option value="{{ $t }}" {{ old('company_type', $serviceProvider->company_type) == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Website</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="website" value="{{ old('website', $serviceProvider->website) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Company email</label>
                <div class="col-sm-10"><input class="form-control" type="email" name="company_email" value="{{ old('company_email', $serviceProvider->company_email) }}"></div>
            </div>

            <hr>
            <h5 class="font-size-14 mb-3">Login Credentials</h5>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Login Email</label>
                <div class="col-sm-10">
                    <input class="form-control @error('email') is-invalid @enderror"
                           type="email" name="email"
                           value="{{ old('email', $serviceProvider->user->email) }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">New Password</label>
                <div class="col-sm-10">
                    <input class="form-control @error('password') is-invalid @enderror"
                           type="password" name="password"
                           placeholder="Leave blank to keep current password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Leave blank to keep the existing password.</small>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Confirm Password</label>
                <div class="col-sm-10">
                    <input class="form-control" type="password" name="password_confirmation"
                           placeholder="Confirm new password">
                </div>
            </div>
        </div></div>
    </div>

    {{-- Step 2: Contact Details --}}
    <div class="tab-pane" id="step-2">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Contact Details</h4>
            <h5 class="font-size-14 mb-4">Primary Contact</h5>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                <div class="col-sm-10"><input class="form-control" type="text" name="primary_contact_name" value="{{ old('primary_contact_name', $serviceProvider->primary_contact_name) }}" required></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Mobile number <span class="text-danger">*</span></label>
                <div class="col-sm-10"><input class="form-control" type="tel" name="primary_contact_mobile" value="{{ old('primary_contact_mobile', $serviceProvider->primary_contact_mobile) }}" required></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10"><input class="form-control" type="email" name="primary_contact_email" value="{{ old('primary_contact_email', $serviceProvider->primary_contact_email) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Preferred languages</label>
                <div class="col-sm-10">
                    <select class="form-select" name="primary_contact_language">
                        <option value="">Select</option>
                        @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $l)
                        <option value="{{ $l }}" {{ old('primary_contact_language', $serviceProvider->primary_contact_language) == $l ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Designation</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="primary_contact_designation" value="{{ old('primary_contact_designation', $serviceProvider->primary_contact_designation) }}"></div>
            </div>
            <h5 class="font-size-14 mb-4 mt-4">Secondary Contact</h5>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="secondary_contact_name" value="{{ old('secondary_contact_name', $serviceProvider->secondary_contact_name) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Mobile number</label>
                <div class="col-sm-10"><input class="form-control" type="tel" name="secondary_contact_mobile" value="{{ old('secondary_contact_mobile', $serviceProvider->secondary_contact_mobile) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10"><input class="form-control" type="email" name="secondary_contact_email" value="{{ old('secondary_contact_email', $serviceProvider->secondary_contact_email) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Preferred languages</label>
                <div class="col-sm-10">
                    <select class="form-select" name="secondary_contact_language">
                        <option value="">Select</option>
                        @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $l)
                        <option value="{{ $l }}" {{ old('secondary_contact_language', $serviceProvider->secondary_contact_language) == $l ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Designation</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="secondary_contact_designation" value="{{ old('secondary_contact_designation', $serviceProvider->secondary_contact_designation) }}"></div>
            </div>
        </div></div>
    </div>

    {{-- Step 3: Location --}}
    <div class="tab-pane" id="step-3">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Location Details</h4>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Head office address</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="head_office_address" value="{{ old('head_office_address', $serviceProvider->head_office_address) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Emirate/City</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="emirate_city" value="{{ old('emirate_city', $serviceProvider->emirate_city) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Google map location pin</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="google_map_pin" value="{{ old('google_map_pin', $serviceProvider->google_map_pin) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Branch details</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="branch_details" value="{{ old('branch_details', $serviceProvider->branch_details) }}"></div>
            </div>
        </div></div>
    </div>

    {{-- Step 4: Service Management --}}
    <div class="tab-pane" id="step-4">
        <div class="card"><div class="card-body">
            <h4 class="card-title mb-4">Service Management</h4>
            <div id="servicesList">
                @forelse($serviceProvider->services as $index => $svc)
                <div class="service-row">
                    <button type="button" class="btn btn-danger btn-sm remove-service" onclick="this.closest('.service-row').remove()"><i class="ri-close-line"></i></button>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Service name</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="services[]">
                                <option value="">Select</option>
                                @foreach($services as $s)
                                <option value="{{ $s->id }}" {{ $svc->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Turn around time</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="turnaround_times[]" value="{{ $svc->pivot->turnaround_time }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">B2B Price</label>
                        <div class="col-sm-10"><input class="form-control" type="number" step="0.01" name="b2b_prices[]" value="{{ $svc->pivot->b2b_price }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Markup%</label>
                        <div class="col-sm-10">
                            <select class="form-select svc-markup" name="markup_percents[]">
                                <option value="">Select</option>
                                @foreach([5,10,15,20,25,30] as $p)
                                <option value="{{ $p }}" {{ $svc->pivot->markup_percent == $p ? 'selected' : '' }}>{{ $p }}%</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Market Price (AED)</label>
                        <div class="col-sm-10"><input class="form-control" type="number" step="0.01" name="market_prices[]" value="{{ $svc->pivot->market_price }}" placeholder="Market / retail price"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Final Price (AED)</label>
                        <div class="col-sm-10"><input class="form-control svc-final-price" type="number" step="0.01" name="final_prices[]" value="{{ $svc->pivot->final_price }}" placeholder="Auto-calculated" disabled></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Remarks</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="service_remarks[]" value="{{ $svc->pivot->remarks }}"></div>
                    </div>
                </div>
                @empty
                <div class="service-row" id="service-row-template">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Service name</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="services[]">
                                <option value="">Select</option>
                                @foreach($services as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Turn around time</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="turnaround_times[]"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">B2B Price</label>
                        <div class="col-sm-10"><input class="form-control" type="number" step="0.01" name="b2b_prices[]"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Markup%</label>
                        <div class="col-sm-10">
                            <select class="form-select svc-markup" name="markup_percents[]">
                                <option value="">Select</option>
                                @foreach([5,10,15,20,25,30] as $p) <option value="{{ $p }}">{{ $p }}%</option> @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Market Price (AED)</label>
                        <div class="col-sm-10"><input class="form-control" type="number" step="0.01" name="market_prices[]" placeholder="Market / retail price"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Final Price (AED)</label>
                        <div class="col-sm-10"><input class="form-control svc-final-price" type="number" step="0.01" name="final_prices[]" placeholder="Auto-calculated" disabled></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Remarks</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="service_remarks[]"></div>
                    </div>
                </div>
                @endforelse
            </div>
            <button type="button" class="btn btn-success btn-sm mt-2" id="addServiceBtn"><i class="ri-add-line me-1"></i> Add Another Service</button>
        </div></div>
    </div>

    {{-- Step 5: Business Operations --}}
    <div class="tab-pane" id="step-5">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Business Operations</h4>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Working days</label>
                <div class="col-sm-10">
                    <select class="form-select" name="working_days">
                        <option value="">Select</option>
                        @foreach(['Mon-Fri'=>'Monday to Friday','Mon-Sat'=>'Monday to Saturday','Sun-Thu'=>'Sunday to Thursday','All Days'=>'All Days'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('working_days', $serviceProvider->working_days) == $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Working hours (From)</label>
                <div class="col-sm-10"><input class="form-control" type="time" name="working_hours_from" value="{{ old('working_hours_from', $serviceProvider->working_hours_from) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Working hours (To)</label>
                <div class="col-sm-10"><input class="form-control" type="time" name="working_hours_to" value="{{ old('working_hours_to', $serviceProvider->working_hours_to) }}"></div>
            </div>
        </div></div>
    </div>

    {{-- Step 6: Bank Details --}}
    <div class="tab-pane" id="step-6">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Bank Details</h4>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Bank Name</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="bank_name" value="{{ old('bank_name', $serviceProvider->bank_name) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Account name</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="account_name" value="{{ old('account_name', $serviceProvider->account_name) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">IBAN/Account</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="iban_account" value="{{ old('iban_account', $serviceProvider->iban_account) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Branch</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="bank_branch" value="{{ old('bank_branch', $serviceProvider->bank_branch) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Swift code (Optional)</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="swift_code" value="{{ old('swift_code', $serviceProvider->swift_code) }}"></div>
            </div>
        </div></div>
    </div>

    {{-- Step 7: Communication --}}
    <div class="tab-pane" id="step-7">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Communication Details</h4>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Mobile number</label>
                <div class="col-sm-10"><input class="form-control" type="tel" name="comm_mobile" value="{{ old('comm_mobile', $serviceProvider->comm_mobile) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Preferred communication language</label>
                <div class="col-sm-10">
                    <select class="form-select" name="comm_language">
                        <option value="">Select</option>
                        @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $l)
                        <option value="{{ $l }}" {{ old('comm_language', $serviceProvider->comm_language) == $l ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div></div>
    </div>

    {{-- Step 8: Compliances --}}
    <div class="tab-pane" id="step-8">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Compliances & Verification</h4>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">KYC Status</label>
                <div class="col-sm-10">
                    <select class="form-select" name="kyc_status">
                        <option value="yes" {{ $serviceProvider->kyc_status == 'yes' ? 'selected' : '' }}>Yes</option>
                        <option value="no" {{ $serviceProvider->kyc_status == 'no' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Verified by</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="verified_by" value="{{ old('verified_by', $serviceProvider->verified_by) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Approval status</label>
                <div class="col-sm-10">
                    <select class="form-select" name="approval_status">
                        <option value="pending" {{ $serviceProvider->approval_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ $serviceProvider->approval_status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $serviceProvider->approval_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Trusted Service Provider</label>
                <div class="col-sm-10 d-flex align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_trusted" id="is_trusted" value="1" {{ old('is_trusted', $serviceProvider->is_trusted) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_trusted">Mark as Trusted</label>
                    </div>
                </div>
            </div>
        </div></div>
    </div>

    {{-- Step 9: Documents --}}
    <div class="tab-pane" id="step-9">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Document Upload</h4>
            @foreach([
                'trade_license_copy' => 'Trade license copy (Upload)',
                'passport_copy' => 'Passport',
                'visa_copy' => 'Visa copy',
                'vat_certificate' => 'VAT Certificate',
                'ejari_contract' => 'Ejari/Tenancy contract',
                'company_stamp' => 'Company stamp (Optional)',
                'signboard_image' => 'Signboard image (Upload)',
            ] as $field => $label)
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">{{ $label }}</label>
                <div class="col-sm-10">
                    @if($serviceProvider->$field)<p class="text-muted mb-1"><small>Current: {{ basename($serviceProvider->$field) }}</small></p>@endif
                    <input type="file" class="form-control" name="{{ $field }}">
                </div>
            </div>
            @endforeach
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Emirates ID</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="emirates_id_doc" value="{{ old('emirates_id_doc', $serviceProvider->emirates_id_doc) }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Owner Emirates ID</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="owner_emirates_id" value="{{ old('owner_emirates_id', $serviceProvider->owner_emirates_id) }}"></div>
            </div>
        </div></div>
    </div>

    {{-- Navigation --}}
    <div class="d-flex justify-content-between mt-3 mb-5">
        <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;"><i class="ri-arrow-left-line me-1"></i> Previous</button>
        <div class="ms-auto">
            <button type="button" class="btn btn-primary" id="nextBtn">Next <i class="ri-arrow-right-line ms-1"></i></button>
            <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;"><i class="ri-save-line me-1"></i> Update Service Provider</button>
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
    document.getElementById('nextBtn').addEventListener('click', () => { if (currentStep < totalSteps) { currentStep++; showStep(currentStep); }});
    document.getElementById('prevBtn').addEventListener('click', () => { if (currentStep > 1) { currentStep--; showStep(currentStep); }});
    document.querySelectorAll('.step-indicator .step').forEach(s => {
        s.addEventListener('click', () => { currentStep = parseInt(s.getAttribute('data-step')); showStep(currentStep); });
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

    document.querySelectorAll('.service-row').forEach(wireServiceRow);

    document.getElementById('addServiceBtn').addEventListener('click', function() {
        const rows = document.querySelectorAll('.service-row');
        const lastRow = rows[rows.length - 1];
        const clone = lastRow.cloneNode(true);
        clone.removeAttribute('id');
        clone.querySelectorAll('input, select').forEach(el => el.value = '');
        document.getElementById('servicesList').appendChild(clone);
        wireServiceRow(clone);
    });
</script>
@endsection
@endsection
