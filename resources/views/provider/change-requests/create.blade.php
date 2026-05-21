<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Request Profile Update | GetMyServe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f5f6f8; }
        .navbar { background: #2a3042; }
        .navbar-brand { color: #fff !important; font-weight: 700; }
        .navbar-brand span { color: #5664d2; }
        .card { border: none; box-shadow: 0 0.75rem 1.5rem rgba(18,38,63,.05); border-radius: .5rem; margin-bottom: 1.5rem; }
        .step-indicator { display: flex; justify-content: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 4px; }
        .step-indicator .step {
            display: flex; align-items: center; padding: 8px 14px; background: #f5f6f8;
            border-radius: 4px; font-size: 13px; color: #74788d; cursor: pointer; transition: all 0.2s;
        }
        .step-indicator .step.active { background: #5664d2; color: #fff; }
        .step-indicator .step.completed { background: #34c38f; color: #fff; }
        .step-indicator .step i { margin-right: 6px; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
        .service-row { background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('provider.dashboard') }}">
                Get<span>My</span>Serve <small class="text-muted ms-2" style="font-size:12px;">Provider Portal</small>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm"><i class="ri-logout-box-line me-1"></i>Logout</button>
            </form>
        </div>
    </nav>

    <div class="container py-4">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('provider.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
                <i class="ri-arrow-left-line"></i>
            </a>
            <h4 class="mb-0">Request Profile Update</h4>
        </div>

        <div class="alert alert-info">
            <i class="ri-information-line me-1"></i>
            Your changes will be submitted to the admin for review. They will take effect once approved.
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ route('provider.change-requests.store') }}" method="POST" enctype="multipart/form-data" id="providerForm">
            @csrf
            @php $p = $serviceProvider; @endphp

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

            {{-- Step 1 --}}
            <div class="tab-pane active" id="step-1">
                <div class="card"><div class="card-body">
                    <h4 class="card-title">Company Profile</h4>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Company name <span class="text-danger">*</span></label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="company_name" value="{{ old('company_name', $p->company_name) }}" required></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Company Logo</label>
                        <div class="col-sm-10"><input type="file" class="form-control" name="company_logo" accept="image/*"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Trade license</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="trade_license" value="{{ old('trade_license', $p->trade_license) }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">License expiry date</label>
                        <div class="col-sm-10"><input class="form-control" type="date" name="license_expiry_date" value="{{ old('license_expiry_date', $p->license_expiry_date) }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Business activity</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="business_activity" value="{{ old('business_activity', $p->business_activity) }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Company type</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="company_type">
                                <option value="">Select company type</option>
                                @foreach(['LLC','Sole Proprietorship','Free Zone','Partnership'] as $ct)
                                <option value="{{ $ct }}" {{ old('company_type', $p->company_type) == $ct ? 'selected' : '' }}>{{ $ct }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Website</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="website" value="{{ old('website', $p->website) }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Company email</label>
                        <div class="col-sm-10"><input class="form-control" type="email" name="company_email" value="{{ old('company_email', $p->company_email) }}"></div>
                    </div>
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
                        <div class="col-sm-10"><input class="form-control" type="text" name="{{ $prefix }}_contact_name" value="{{ old($prefix.'_contact_name', $p->{$prefix.'_contact_name'}) }}" {{ $req ? 'required' : '' }}></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Mobile {{ $req }}</label>
                        <div class="col-sm-10"><input class="form-control" type="tel" name="{{ $prefix }}_contact_mobile" value="{{ old($prefix.'_contact_mobile', $p->{$prefix.'_contact_mobile'}) }}" {{ $req ? 'required' : '' }}></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10"><input class="form-control" type="email" name="{{ $prefix }}_contact_email" value="{{ old($prefix.'_contact_email', $p->{$prefix.'_contact_email'}) }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Language</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="{{ $prefix }}_contact_language">
                                <option value="">Select</option>
                                @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $lang)
                                <option value="{{ $lang }}" {{ old($prefix.'_contact_language', $p->{$prefix.'_contact_language'}) == $lang ? 'selected' : '' }}>{{ $lang }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Designation</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="{{ $prefix }}_contact_designation" value="{{ old($prefix.'_contact_designation', $p->{$prefix.'_contact_designation'}) }}"></div>
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
                        <div class="col-sm-10"><input class="form-control" type="text" name="{{ $field }}" value="{{ old($field, $p->{$field}) }}"></div>
                    </div>
                    @endforeach
                </div></div>
            </div>

            {{-- Step 4 --}}
            <div class="tab-pane" id="step-4">
                <div class="card"><div class="card-body">
                    <h4 class="card-title mb-4">Service Management</h4>
                    <div id="servicesList">
                        @foreach($serviceProvider->services as $svc)
                        <div class="service-row">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Service</label>
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
                                <label class="col-sm-2 col-form-label">Remarks</label>
                                <div class="col-sm-10"><input class="form-control" type="text" name="service_remarks[]" value="{{ $svc->pivot->remarks }}"></div>
                            </div>
                        </div>
                        @endforeach
                        @if($serviceProvider->services->isEmpty())
                        <div class="service-row">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Service</label>
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
                                <div class="col-sm-10"><input class="form-control" type="text" name="turnaround_times[]"></div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">B2B Price</label>
                                <div class="col-sm-10"><input class="form-control" type="number" step="0.01" name="b2b_prices[]"></div>
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
                                <option value="{{ $val }}" {{ old('working_days', $p->working_days) == $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Hours From</label>
                        <div class="col-sm-10"><input class="form-control" type="time" name="working_hours_from" value="{{ old('working_hours_from', $p->working_hours_from) }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Hours To</label>
                        <div class="col-sm-10"><input class="form-control" type="time" name="working_hours_to" value="{{ old('working_hours_to', $p->working_hours_to) }}"></div>
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
                        <div class="col-sm-10"><input class="form-control" type="text" name="{{ $field }}" value="{{ old($field, $p->{$field}) }}"></div>
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
                        <div class="col-sm-10"><input class="form-control" type="tel" name="comm_mobile" value="{{ old('comm_mobile', $p->comm_mobile) }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Preferred language</label>
                        <div class="col-sm-10">
                            <select class="form-select" name="comm_language">
                                <option value="">Select</option>
                                @foreach(['English','Arabic','Hindi','Urdu','Malayalam','Tamil'] as $lang)
                                <option value="{{ $lang }}" {{ old('comm_language', $p->comm_language) == $lang ? 'selected' : '' }}>{{ $lang }}</option>
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
                    <p class="text-muted small mb-3">Leave blank to keep your current documents.</p>
                    @foreach([['trade_license_copy','Trade license copy'],['passport_copy','Passport'],['visa_copy','Visa copy'],['vat_certificate','VAT Certificate'],['ejari_contract','Ejari/Tenancy contract'],['company_stamp','Company stamp'],['signboard_image','Signboard image'],['company_logo','Company Logo']] as [$field,$label])
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">{{ $label }}</label>
                        <div class="col-sm-10"><input type="file" class="form-control" name="{{ $field }}"></div>
                    </div>
                    @endforeach
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Emirates ID</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="emirates_id_doc" value="{{ old('emirates_id_doc', $p->emirates_id_doc) }}"></div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Owner Emirates ID</label>
                        <div class="col-sm-10"><input class="form-control" type="text" name="owner_emirates_id" value="{{ old('owner_emirates_id', $p->owner_emirates_id) }}"></div>
                    </div>
                </div></div>
            </div>

            <div class="d-flex justify-content-between mt-3 mb-5">
                <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;">
                    <i class="ri-arrow-left-line me-1"></i> Previous
                </button>
                <div class="ms-auto">
                    <a href="{{ route('provider.dashboard') }}" class="btn btn-outline-secondary me-2">Cancel</a>
                    <button type="button" class="btn btn-primary" id="nextBtn">Next <i class="ri-arrow-right-line ms-1"></i></button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display:none;">
                        <i class="ri-send-plane-line me-1"></i> Submit for Approval
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>
