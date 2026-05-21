@extends('staff.layouts.app')
@section('title', $requestType === 'create' ? 'Request New Service Provider' : 'Request Provider Update')
@section('page-title', $requestType === 'create' ? 'Request New Service Provider' : 'Request Provider Update')

@section('content')

{{-- Request type selector (shown when no type is determined yet or type=update with no provider loaded) --}}
@if($requestType === 'update' && !$serviceProvider)
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Select Provider to Update</h5>
        <form method="GET" action="{{ route('staff.change-requests.create') }}" class="row g-3 align-items-end">
            <input type="hidden" name="type" value="update">
            <div class="col-md-8">
                <label class="form-label">Service Provider</label>
                <select class="form-select" name="provider" required>
                    <option value="">— Select a service provider —</option>
                    @foreach($providers as $p)
                        <option value="{{ $p->id }}">{{ $p->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Load Provider Data</button>
            </div>
        </form>
        <div class="mt-3">
            <a href="{{ route('staff.change-requests.create', ['type' => 'create']) }}" class="text-muted small">
                <i class="ri-arrow-left-line"></i> Switch to New Provider Request
            </a>
        </div>
    </div>
</div>
@else

<div class="mb-3">
    <div class="btn-group" role="group">
        <a href="{{ route('staff.change-requests.create', ['type' => 'create']) }}"
           class="btn btn-sm {{ $requestType === 'create' ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="ri-add-circle-line me-1"></i> New Provider
        </a>
        <a href="{{ route('staff.change-requests.create', ['type' => 'update']) }}"
           class="btn btn-sm {{ $requestType === 'update' ? 'btn-primary' : 'btn-outline-primary' }}">
            <i class="ri-edit-line me-1"></i> Update Existing Provider
        </a>
    </div>
    @if($requestType === 'update' && $serviceProvider)
        <span class="ms-3 text-muted small">
            Pre-filled with data from <strong>{{ $serviceProvider->company_name }}</strong>
            <a href="{{ route('staff.change-requests.create', ['type' => 'update']) }}" class="ms-2">Change</a>
        </span>
    @endif
</div>

<form action="{{ route('staff.change-requests.store') }}" method="POST" enctype="multipart/form-data" id="providerForm">
    @csrf
    <input type="hidden" name="request_type" value="{{ $requestType }}">
    @if($requestType === 'update' && $serviceProvider)
        <input type="hidden" name="service_provider_id" value="{{ $serviceProvider->id }}">
    @endif

    <div class="step-indicator">
        @if($requestType === 'create')
        <div class="step active" data-step="1"><i class="ri-building-line"></i> Company Profile</div>
        @else
        <div class="step active" data-step="1"><i class="ri-building-line"></i> Company Profile</div>
        @endif
        <div class="step" data-step="2"><i class="ri-contacts-line"></i> Contact Details</div>
        <div class="step" data-step="3"><i class="ri-map-pin-line"></i> Location</div>
        <div class="step" data-step="4"><i class="ri-stack-line"></i> Service Mgmt</div>
        <div class="step" data-step="5"><i class="ri-briefcase-line"></i> Business Ops</div>
        <div class="step" data-step="6"><i class="ri-bank-line"></i> Bank Details</div>
        <div class="step" data-step="7"><i class="ri-message-2-line"></i> Communication</div>
        <div class="step" data-step="8"><i class="ri-file-upload-line"></i> Documents</div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- Step 1: Company Profile --}}
    <div class="tab-pane active" id="step-1">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Company Profile</h4>
                @php $p = $serviceProvider; @endphp

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Company name <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="company_name" value="{{ old('company_name', $p->company_name ?? '') }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Company Logo</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="company_logo" accept="image/*">
                        @if($p && $p->company_logo)
                            <small class="text-muted">Current: {{ basename($p->company_logo) }}</small>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Trade license</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="trade_license" value="{{ old('trade_license', $p->trade_license ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">License expiry date</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="date" name="license_expiry_date" value="{{ old('license_expiry_date', $p->license_expiry_date ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Business activity</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="business_activity" value="{{ old('business_activity', $p->business_activity ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Company type</label>
                    <div class="col-sm-10">
                        <select class="form-select" name="company_type">
                            <option value="">Select company type</option>
                            @foreach(['LLC','Sole Proprietorship','Free Zone','Partnership'] as $ct)
                            <option value="{{ $ct }}" {{ old('company_type', $p->company_type ?? '') == $ct ? 'selected' : '' }}>{{ $ct }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Website</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="website" value="{{ old('website', $p->website ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Company email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" name="company_email" value="{{ old('company_email', $p->company_email ?? '') }}">
                    </div>
                </div>

                @if($requestType === 'create')
                <hr>
                <h5 class="font-size-14 mb-3">Login Credentials</h5>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Login Email <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Password <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="password" required>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Step 2: Contact Details --}}
    <div class="tab-pane" id="step-2">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Contact Details</h4>
                <h5 class="font-size-14 mb-4">Primary Contact</h5>
                @foreach([['primary','*'],['secondary','']] as [$prefix, $req])
                @if($prefix === 'secondary')<h5 class="font-size-14 mb-4 mt-4">Secondary Contact</h5>@endif
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Name {{ $req }}</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="{{ $prefix }}_contact_name"
                            value="{{ old($prefix.'_contact_name', $p->{$prefix.'_contact_name'} ?? '') }}" {{ $req ? 'required' : '' }}>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Mobile {{ $req }}</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="tel" name="{{ $prefix }}_contact_mobile"
                            value="{{ old($prefix.'_contact_mobile', $p->{$prefix.'_contact_mobile'} ?? '') }}" {{ $req ? 'required' : '' }}>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="email" name="{{ $prefix }}_contact_email"
                            value="{{ old($prefix.'_contact_email', $p->{$prefix.'_contact_email'} ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Language</label>
                    <div class="col-sm-10">
                        <select class="form-select" name="{{ $prefix }}_contact_language">
                            <option value="">Select language</option>
                            @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $lang)
                            <option value="{{ $lang }}" {{ old($prefix.'_contact_language', $p->{$prefix.'_contact_language'} ?? '') == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Designation</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="{{ $prefix }}_contact_designation"
                            value="{{ old($prefix.'_contact_designation', $p->{$prefix.'_contact_designation'} ?? '') }}">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Step 3: Location --}}
    <div class="tab-pane" id="step-3">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Location Details</h4>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Head office address</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="head_office_address" value="{{ old('head_office_address', $p->head_office_address ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Emirate/City</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="emirate_city" value="{{ old('emirate_city', $p->emirate_city ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Google map pin</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="google_map_pin" value="{{ old('google_map_pin', $p->google_map_pin ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Branch details</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="branch_details" value="{{ old('branch_details', $p->branch_details ?? '') }}">
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
                    @php
                        $existingServices = $serviceProvider ? $serviceProvider->services : collect();
                        $hasServices = $existingServices->isNotEmpty();
                    @endphp
                    @if($hasServices)
                        @foreach($existingServices as $svc)
                        <div class="service-row">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Service name</label>
                                <div class="col-sm-10">
                                    <select class="form-select" name="services[]">
                                        <option value="">Select service</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ $service->id == $svc->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Turnaround time</label>
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
                                        @foreach([5,10,15,20,25,30] as $mp)
                                        <option value="{{ $mp }}" {{ $svc->pivot->markup_percent == $mp ? 'selected' : '' }}>{{ $mp }}%</option>
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
                        @endforeach
                    @else
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
                            <label class="col-sm-2 col-form-label">Turnaround time</label>
                            <div class="col-sm-10"><input class="form-control" type="text" name="turnaround_times[]" placeholder="e.g. 2 hours"></div>
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
                                    @foreach([5,10,15,20,25,30] as $mp)
                                    <option value="{{ $mp }}">{{ $mp }}%</option>
                                    @endforeach
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
                    @endif
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
                            @foreach(['Mon-Fri' => 'Monday to Friday','Mon-Sat' => 'Monday to Saturday','Sun-Thu' => 'Sunday to Thursday','All Days' => 'All Days'] as $val => $label)
                            <option value="{{ $val }}" {{ old('working_days', $p->working_days ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Working hours (From)</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="time" name="working_hours_from" value="{{ old('working_hours_from', $p->working_hours_from ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Working hours (To)</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="time" name="working_hours_to" value="{{ old('working_hours_to', $p->working_hours_to ?? '') }}">
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
                @foreach([['bank_name','Bank Name'],['account_name','Account name'],['iban_account','IBAN/Account'],['bank_branch','Branch'],['swift_code','Swift code']] as [$field, $label])
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">{{ $label }}</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="{{ $field }}" value="{{ old($field, $p->{$field} ?? '') }}">
                    </div>
                </div>
                @endforeach
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
                        <input class="form-control" type="tel" name="comm_mobile" value="{{ old('comm_mobile', $p->comm_mobile ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Preferred language</label>
                    <div class="col-sm-10">
                        <select class="form-select" name="comm_language">
                            <option value="">Select language</option>
                            @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $lang)
                            <option value="{{ $lang }}" {{ old('comm_language', $p->comm_language ?? '') == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Step 8: Documents --}}
    <div class="tab-pane" id="step-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Document Upload</h4>
                <p class="text-muted small mb-4">Upload new files to replace existing documents. Leave blank to keep current files.</p>

                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Trade license copy</label>
                    <div class="col-sm-10"><input type="file" class="form-control" name="trade_license_copy"></div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Emirates ID</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="emirates_id_doc" value="{{ old('emirates_id_doc', $p->emirates_id_doc ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Passport</label>
                    <div class="col-sm-10"><input type="file" class="form-control" name="passport_copy"></div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Visa copy</label>
                    <div class="col-sm-10"><input type="file" class="form-control" name="visa_copy"></div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">VAT Certificate</label>
                    <div class="col-sm-10"><input type="file" class="form-control" name="vat_certificate"></div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Ejari/Tenancy contract</label>
                    <div class="col-sm-10"><input type="file" class="form-control" name="ejari_contract"></div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Owner Emirates ID</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="owner_emirates_id" value="{{ old('owner_emirates_id', $p->owner_emirates_id ?? '') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Company stamp</label>
                    <div class="col-sm-10"><input type="file" class="form-control" name="company_stamp"></div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Signboard image</label>
                    <div class="col-sm-10"><input type="file" class="form-control" name="signboard_image"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-3 mb-5">
        <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;">
            <i class="ri-arrow-left-line me-1"></i> Previous
        </button>
        <div class="ms-auto">
            <a href="{{ route('staff.change-requests.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
            <button type="button" class="btn btn-primary" id="nextBtn">
                Next <i class="ri-arrow-right-line ms-1"></i>
            </button>
            <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">
                <i class="ri-send-plane-line me-1"></i> Submit Request
            </button>
        </div>
    </div>
</form>

@endif
@endsection

@section('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 8;

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

    document.getElementById('nextBtn')?.addEventListener('click', () => {
        if (currentStep < totalSteps) { currentStep++; showStep(currentStep); }
    });
    document.getElementById('prevBtn')?.addEventListener('click', () => {
        if (currentStep > 1) { currentStep--; showStep(currentStep); }
    });
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
        const b2bInput = row.querySelector('[name="b2b_prices[]"]');
        const markupSel = row.querySelector('.svc-markup');
        if (b2bInput) b2bInput.addEventListener('input', () => calcFinalPrice(row));
        if (markupSel) markupSel.addEventListener('change', () => calcFinalPrice(row));
    }

    document.querySelectorAll('.service-row').forEach(wireServiceRow);

    const addServiceBtn = document.getElementById('addServiceBtn');
    if (addServiceBtn) {
        addServiceBtn.addEventListener('click', function() {
            const template = document.querySelector('.service-row');
            const clone = template.cloneNode(true);
            clone.removeAttribute('id');
            clone.querySelectorAll('input, select').forEach(el => el.value = '');
            document.getElementById('servicesList').appendChild(clone);
            wireServiceRow(clone);
        });
    }
</script>
@endsection
