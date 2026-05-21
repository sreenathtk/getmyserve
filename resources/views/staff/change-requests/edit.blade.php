@extends('staff.layouts.app')
@section('title', 'Edit Change Request')
@section('page-title', $changeRequest->request_type === 'create' ? 'Edit New Provider Request' : 'Edit Update Request')

@section('content')
<div class="mb-3">
    <a href="{{ route('staff.change-requests.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="ri-arrow-left-line me-1"></i> Back to My Requests
    </a>
</div>

<div class="alert alert-info">
    <i class="ri-information-line me-1"></i>
    You are editing a pending request. Changes will overwrite the previous submission.
</div>

@php
    $p = null;
    $requestType = $changeRequest->request_type;
    $prov = $changeRequest->payload['provider'] ?? [];
    $existingServices = collect($changeRequest->payload['services'] ?? []);
@endphp

<form action="{{ route('staff.change-requests.update', $changeRequest) }}" method="POST" enctype="multipart/form-data" id="providerForm">
    @csrf @method('PUT')

    <div class="step-indicator">
        <div class="step active" data-step="1"><i class="ri-building-line"></i> Company Profile</div>
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

    {{-- Step 1 --}}
    <div class="tab-pane active" id="step-1">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Company Profile</h4>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Company name <span class="text-danger">*</span></label>
                <div class="col-sm-10"><input class="form-control" type="text" name="company_name" value="{{ old('company_name', $prov['company_name'] ?? '') }}" required></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Company Logo</label>
                <div class="col-sm-10">
                    <input type="file" class="form-control" name="company_logo" accept="image/*">
                    @if(!empty($changeRequest->payload['documents']['company_logo']))
                        <small class="text-muted">Current: {{ basename($changeRequest->payload['documents']['company_logo']) }}</small>
                    @endif
                </div>
            </div>
            @foreach([['trade_license','Trade license','text'],['license_expiry_date','License expiry','date'],['business_activity','Business activity','text'],['website','Website','text'],['company_email','Company email','email']] as [$field,$label,$type])
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">{{ $label }}</label>
                <div class="col-sm-10"><input class="form-control" type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $prov[$field] ?? '') }}"></div>
            </div>
            @endforeach
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Company type</label>
                <div class="col-sm-10">
                    <select class="form-select" name="company_type">
                        <option value="">Select</option>
                        @foreach(['LLC','Sole Proprietorship','Free Zone','Partnership'] as $ct)
                        <option value="{{ $ct }}" {{ old('company_type', $prov['company_type'] ?? '') == $ct ? 'selected' : '' }}>{{ $ct }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if($requestType === 'create')
            <hr>
            <h5 class="mb-3">Login Credentials</h5>
            <div class="alert alert-warning small">Updating credentials will replace the ones in the original request.</div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Login Email <span class="text-danger">*</span></label>
                <div class="col-sm-10"><input class="form-control" type="email" name="email" value="{{ old('email', $changeRequest->payload['user']['email'] ?? '') }}" required></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Password <span class="text-danger">*</span></label>
                <div class="col-sm-10"><input class="form-control" type="password" name="password" required></div>
            </div>
            @endif
        </div></div>
    </div>

    {{-- Step 2 --}}
    <div class="tab-pane" id="step-2">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Contact Details</h4>
            @foreach([['primary','*'],['secondary','']] as [$prefix, $req])
            @if($prefix === 'secondary')<h5 class="mt-4 mb-3">Secondary Contact</h5>@else<h5 class="mb-3">Primary Contact</h5>@endif
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Name {{ $req }}</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="{{ $prefix }}_contact_name" value="{{ old($prefix.'_contact_name', $prov[$prefix.'_contact_name'] ?? '') }}" {{ $req ? 'required' : '' }}></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Mobile {{ $req }}</label>
                <div class="col-sm-10"><input class="form-control" type="tel" name="{{ $prefix }}_contact_mobile" value="{{ old($prefix.'_contact_mobile', $prov[$prefix.'_contact_mobile'] ?? '') }}" {{ $req ? 'required' : '' }}></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Email</label>
                <div class="col-sm-10"><input class="form-control" type="email" name="{{ $prefix }}_contact_email" value="{{ old($prefix.'_contact_email', $prov[$prefix.'_contact_email'] ?? '') }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Language</label>
                <div class="col-sm-10">
                    <select class="form-select" name="{{ $prefix }}_contact_language">
                        <option value="">Select</option>
                        @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $lang)
                        <option value="{{ $lang }}" {{ old($prefix.'_contact_language', $prov[$prefix.'_contact_language'] ?? '') == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Designation</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="{{ $prefix }}_contact_designation" value="{{ old($prefix.'_contact_designation', $prov[$prefix.'_contact_designation'] ?? '') }}"></div>
            </div>
            @endforeach
        </div></div>
    </div>

    {{-- Step 3 --}}
    <div class="tab-pane" id="step-3">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Location Details</h4>
            @foreach([['head_office_address','Head office address'],['emirate_city','Emirate/City'],['google_map_pin','Google map pin'],['branch_details','Branch details']] as [$field,$label])
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">{{ $label }}</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="{{ $field }}" value="{{ old($field, $prov[$field] ?? '') }}"></div>
            </div>
            @endforeach
        </div></div>
    </div>

    {{-- Step 4 --}}
    <div class="tab-pane" id="step-4">
        <div class="card"><div class="card-body">
            <h4 class="card-title mb-4">Service Management</h4>
            <div id="servicesList">
                @if($existingServices->isNotEmpty())
                    @foreach($existingServices as $svc)
                    <div class="service-row">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Service</label>
                            <div class="col-sm-10">
                                <select class="form-select" name="services[]">
                                    <option value="">Select</option>
                                    @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ $service->id == ($svc['service_id'] ?? null) ? 'selected' : '' }}>{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Turnaround time</label>
                            <div class="col-sm-10"><input class="form-control" type="text" name="turnaround_times[]" value="{{ $svc['turnaround_time'] ?? '' }}"></div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">B2B Price</label>
                            <div class="col-sm-10"><input class="form-control" type="number" step="0.01" name="b2b_prices[]" value="{{ $svc['b2b_price'] ?? '' }}"></div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Markup%</label>
                            <div class="col-sm-10">
                                <select class="form-select" name="markup_percents[]">
                                    <option value="">Select</option>
                                    @foreach([5,10,15,20,25,30] as $mp)
                                    <option value="{{ $mp }}" {{ ($svc['markup_percent'] ?? '') == $mp ? 'selected' : '' }}>{{ $mp }}%</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Remarks</label>
                            <div class="col-sm-10"><input class="form-control" type="text" name="service_remarks[]" value="{{ $svc['remarks'] ?? '' }}"></div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="service-row">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Service</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="services[]">
                                <option value="">Select</option>
                                @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Turnaround time</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="turnaround_times[]"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">B2B Price</label>
                        <div class="col-sm-10"><input class="form-control" type="number" step="0.01" name="b2b_prices[]"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Markup%</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="markup_percents[]">
                                <option value="">Select</option>
                                @foreach([5,10,15,20,25,30] as $mp)<option value="{{ $mp }}">{{ $mp }}%</option>@endforeach
                            </select>
                        </div>
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
        </div></div>
    </div>

    {{-- Step 5 --}}
    <div class="tab-pane" id="step-5">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Business Operations</h4>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Working days</label>
                <div class="col-sm-10">
                    <select class="form-select" name="working_days">
                        <option value="">Select</option>
                        @foreach(['Mon-Fri' => 'Monday to Friday','Mon-Sat' => 'Monday to Saturday','Sun-Thu' => 'Sunday to Thursday','All Days' => 'All Days'] as $val => $label)
                        <option value="{{ $val }}" {{ old('working_days', $prov['working_days'] ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Hours From</label>
                <div class="col-sm-10"><input class="form-control" type="time" name="working_hours_from" value="{{ old('working_hours_from', $prov['working_hours_from'] ?? '') }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Hours To</label>
                <div class="col-sm-10"><input class="form-control" type="time" name="working_hours_to" value="{{ old('working_hours_to', $prov['working_hours_to'] ?? '') }}"></div>
            </div>
        </div></div>
    </div>

    {{-- Step 6 --}}
    <div class="tab-pane" id="step-6">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Bank Details</h4>
            @foreach([['bank_name','Bank Name'],['account_name','Account name'],['iban_account','IBAN/Account'],['bank_branch','Branch'],['swift_code','Swift code']] as [$field,$label])
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">{{ $label }}</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="{{ $field }}" value="{{ old($field, $prov[$field] ?? '') }}"></div>
            </div>
            @endforeach
        </div></div>
    </div>

    {{-- Step 7 --}}
    <div class="tab-pane" id="step-7">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Communication Details</h4>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Mobile number</label>
                <div class="col-sm-10"><input class="form-control" type="tel" name="comm_mobile" value="{{ old('comm_mobile', $prov['comm_mobile'] ?? '') }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Preferred language</label>
                <div class="col-sm-10">
                    <select class="form-select" name="comm_language">
                        <option value="">Select</option>
                        @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $lang)
                        <option value="{{ $lang }}" {{ old('comm_language', $prov['comm_language'] ?? '') == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div></div>
    </div>

    {{-- Step 8 --}}
    <div class="tab-pane" id="step-8">
        <div class="card"><div class="card-body">
            <h4 class="card-title">Document Upload</h4>
            <p class="text-muted small mb-3">Upload new files to replace existing ones. Leave blank to keep current files.</p>
            @if(!empty($changeRequest->payload['documents']))
            <div class="alert alert-secondary small mb-3">
                <strong>Currently uploaded:</strong>
                @foreach($changeRequest->payload['documents'] as $field => $path)
                    {{ ucwords(str_replace('_', ' ', $field)) }}: {{ basename($path) }};
                @endforeach
            </div>
            @endif
            @foreach([['trade_license_copy','Trade license copy'],['passport_copy','Passport'],['visa_copy','Visa copy'],['vat_certificate','VAT Certificate'],['ejari_contract','Ejari/Tenancy contract'],['company_stamp','Company stamp'],['signboard_image','Signboard image'],['company_logo','Company Logo']] as [$field,$label])
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">{{ $label }}</label>
                <div class="col-sm-10"><input type="file" class="form-control" name="{{ $field }}"></div>
            </div>
            @endforeach
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Emirates ID</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="emirates_id_doc" value="{{ old('emirates_id_doc', $prov['emirates_id_doc'] ?? '') }}"></div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Owner Emirates ID</label>
                <div class="col-sm-10"><input class="form-control" type="text" name="owner_emirates_id" value="{{ old('owner_emirates_id', $prov['owner_emirates_id'] ?? '') }}"></div>
            </div>
        </div></div>
    </div>

    <div class="d-flex justify-content-between mt-3 mb-5">
        <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;">
            <i class="ri-arrow-left-line me-1"></i> Previous
        </button>
        <div class="ms-auto">
            <a href="{{ route('staff.change-requests.index') }}" class="btn btn-outline-secondary me-2">Cancel</a>
            <button type="button" class="btn btn-primary" id="nextBtn">Next <i class="ri-arrow-right-line ms-1"></i></button>
            <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">
                <i class="ri-save-line me-1"></i> Save Changes
            </button>
        </div>
    </div>
</form>

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
    document.getElementById('nextBtn').addEventListener('click', () => { if (currentStep < totalSteps) { currentStep++; showStep(currentStep); } });
    document.getElementById('prevBtn').addEventListener('click', () => { if (currentStep > 1) { currentStep--; showStep(currentStep); } });
    document.querySelectorAll('.step-indicator .step').forEach(s => {
        s.addEventListener('click', () => { currentStep = parseInt(s.getAttribute('data-step')); showStep(currentStep); });
    });
    document.getElementById('addServiceBtn').addEventListener('click', function() {
        const template = document.querySelector('.service-row');
        const clone = template.cloneNode(true);
        clone.querySelectorAll('input, select').forEach(el => el.value = '');
        document.getElementById('servicesList').appendChild(clone);
    });
</script>
@endsection
