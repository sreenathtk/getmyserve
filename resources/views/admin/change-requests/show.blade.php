@extends('admin.layouts.app')
@section('title', 'Review Change Request')
@section('page-title', 'Review Change Request')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Header Info Card --}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="150">Request Type</td>
                                <td>
                                    @if($changeRequest->request_type === 'create')
                                        <span class="badge badge-soft-primary">New Service Provider</span>
                                    @else
                                        <span class="badge badge-soft-info">Update Existing Provider</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Submitted By</td>
                                <td>
                                    {{ $changeRequest->requestedBy->name ?? '—' }}
                                    @if($changeRequest->requestedBy?->role === 'staff')
                                        <span class="badge badge-soft-secondary ms-1">Staff</span>
                                    @elseif($changeRequest->requestedBy?->role === 'service_provider')
                                        <span class="badge badge-soft-info ms-1">Provider</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Submitted On</td>
                                <td>{{ $changeRequest->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @if($changeRequest->request_type === 'update')
                            <tr>
                                <td class="text-muted">Target Provider</td>
                                <td>
                                    @if($changeRequest->serviceProvider)
                                        <a href="{{ route('admin.service-providers.edit', $changeRequest->serviceProvider) }}">
                                            {{ $changeRequest->serviceProvider->company_name }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="150">Status</td>
                                <td>
                                    @if($changeRequest->status === 'approved')
                                        <span class="badge badge-soft-success">Approved</span>
                                    @elseif($changeRequest->status === 'rejected')
                                        <span class="badge badge-soft-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-soft-warning">Pending Review</span>
                                    @endif
                                </td>
                            </tr>
                            @if($changeRequest->reviewedBy)
                            <tr>
                                <td class="text-muted">Reviewed By</td>
                                <td>{{ $changeRequest->reviewedBy->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Reviewed On</td>
                                <td>{{ $changeRequest->reviewed_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            @endif
                            @if($changeRequest->rejection_reason)
                            <tr>
                                <td class="text-muted">Rejection Reason</td>
                                <td class="text-danger">{{ $changeRequest->rejection_reason }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($changeRequest->isPending())
                <div class="border-top mt-3 pt-3 d-flex gap-2">
                    <form action="{{ route('admin.change-requests.approve', $changeRequest) }}" method="POST"
                          onsubmit="return confirm('Approve and apply this change request?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="ri-checkbox-circle-line me-1"></i> Approve & Apply
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="ri-close-circle-line me-1"></i> Reject
                    </button>
                    <a href="{{ route('admin.change-requests.index') }}" class="btn btn-outline-secondary ms-auto">
                        <i class="ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>
                @else
                <div class="border-top mt-3 pt-3">
                    <a href="{{ route('admin.change-requests.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Back to List
                    </a>
                </div>
                @endif
            </div>
        </div>

        @php $payload = $changeRequest->payload; $p = $changeRequest->serviceProvider; @endphp

        {{-- Proposed Changes --}}
        <div class="row">
            <div class="{{ $p ? 'col-md-6' : 'col-12' }}">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ri-file-edit-line me-1 text-primary"></i>
                            {{ $changeRequest->request_type === 'create' ? 'New Provider Details' : 'Proposed Changes' }}
                        </h5>

                        @if($changeRequest->request_type === 'create' && !empty($payload['user']['email']))
                        <div class="mb-3 p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Login Credentials</h6>
                            <div><strong>Email:</strong> {{ $payload['user']['email'] }}</div>
                            <div><strong>Password:</strong> <em class="text-muted">(set on approval)</em></div>
                        </div>
                        @endif

                        @php $prov = $payload['provider'] ?? []; @endphp
                        <div class="row">
                            @foreach([
                                'company_name' => 'Company Name',
                                'trade_license' => 'Trade License',
                                'license_expiry_date' => 'License Expiry',
                                'business_activity' => 'Business Activity',
                                'company_type' => 'Company Type',
                                'website' => 'Website',
                                'company_email' => 'Company Email',
                                'primary_contact_name' => 'Primary Contact',
                                'primary_contact_mobile' => 'Primary Mobile',
                                'primary_contact_email' => 'Primary Email',
                                'secondary_contact_name' => 'Secondary Contact',
                                'secondary_contact_mobile' => 'Secondary Mobile',
                                'head_office_address' => 'Head Office',
                                'emirate_city' => 'Emirate/City',
                                'working_days' => 'Working Days',
                                'working_hours_from' => 'Hours From',
                                'working_hours_to' => 'Hours To',
                                'bank_name' => 'Bank',
                                'iban_account' => 'IBAN/Account',
                                'comm_mobile' => 'Comm. Mobile',
                                'comm_language' => 'Comm. Language',
                            ] as $key => $label)
                            @if(!empty($prov[$key]))
                            <div class="col-md-6 mb-2">
                                <div class="text-muted small">{{ $label }}</div>
                                <div class="fw-semibold">
                                    @php
                                        $newVal = $prov[$key];
                                        $oldVal = $p ? ($p->{$key} ?? null) : null;
                                        $changed = $p && $oldVal !== $newVal;
                                    @endphp
                                    @if($changed)
                                        <span class="text-success">{{ $newVal }}</span>
                                        <small class="text-decoration-line-through text-muted ms-1">{{ $oldVal }}</small>
                                    @else
                                        {{ $newVal }}
                                    @endif
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        @if(!empty($payload['services']))
                        <hr>
                        <h6 class="text-muted mb-2">Services</h6>
                        @foreach($payload['services'] as $svc)
                        @php $svcModel = $services->firstWhere('id', $svc['service_id']); @endphp
                        <div class="mb-2 p-2 bg-light rounded">
                            <div class="fw-semibold">{{ $svcModel->name ?? 'Service #'.$svc['service_id'] }}</div>
                            <div class="small text-muted">
                                TAT: {{ $svc['turnaround_time'] ?? '—' }} &nbsp;|&nbsp;
                                B2B: {{ $svc['b2b_price'] ?? '—' }} &nbsp;|&nbsp;
                                Markup: {{ $svc['markup_percent'] ? $svc['markup_percent'].'%' : '—' }}
                                @if(!empty($svc['remarks'])) &nbsp;| Remarks: {{ $svc['remarks'] }} @endif
                            </div>
                        </div>
                        @endforeach
                        @endif

                        @if(!empty($payload['documents']))
                        <hr>
                        <h6 class="text-muted mb-2">Documents Uploaded</h6>
                        <ul class="list-unstyled mb-0">
                            @foreach($payload['documents'] as $field => $path)
                            <li class="small"><i class="ri-file-line text-primary me-1"></i>
                                {{ ucwords(str_replace('_', ' ', $field)) }}: {{ basename($path) }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>

            @if($p)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ri-building-2-line me-1 text-muted"></i> Current Provider Data
                        </h5>
                        <div class="row">
                            @foreach([
                                'company_name' => 'Company Name',
                                'trade_license' => 'Trade License',
                                'license_expiry_date' => 'License Expiry',
                                'business_activity' => 'Business Activity',
                                'company_type' => 'Company Type',
                                'website' => 'Website',
                                'company_email' => 'Company Email',
                                'primary_contact_name' => 'Primary Contact',
                                'primary_contact_mobile' => 'Primary Mobile',
                                'primary_contact_email' => 'Primary Email',
                                'secondary_contact_name' => 'Secondary Contact',
                                'secondary_contact_mobile' => 'Secondary Mobile',
                                'head_office_address' => 'Head Office',
                                'emirate_city' => 'Emirate/City',
                                'working_days' => 'Working Days',
                                'working_hours_from' => 'Hours From',
                                'working_hours_to' => 'Hours To',
                                'bank_name' => 'Bank',
                                'iban_account' => 'IBAN/Account',
                                'comm_mobile' => 'Comm. Mobile',
                                'comm_language' => 'Comm. Language',
                            ] as $key => $label)
                            <div class="col-md-6 mb-2">
                                <div class="text-muted small">{{ $label }}</div>
                                <div class="fw-semibold">{{ $p->{$key} ?? '—' }}</div>
                            </div>
                            @endforeach
                        </div>

                        @if($p->services->isNotEmpty())
                        <hr>
                        <h6 class="text-muted mb-2">Current Services</h6>
                        @foreach($p->services as $svc)
                        <div class="mb-2 p-2 bg-light rounded">
                            <div class="fw-semibold">{{ $svc->name }}</div>
                            <div class="small text-muted">
                                TAT: {{ $svc->pivot->turnaround_time ?? '—' }} &nbsp;|&nbsp;
                                B2B: {{ $svc->pivot->b2b_price ?? '—' }} &nbsp;|&nbsp;
                                Markup: {{ $svc->pivot->markup_percent ? $svc->pivot->markup_percent.'%' : '—' }}
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Reject Modal --}}
@if($changeRequest->isPending())
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.change-requests.reject', $changeRequest) }}" method="POST">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Reject Change Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Reason (optional)</label>
                    <textarea class="form-control" name="rejection_reason" rows="3" placeholder="Provide a reason for rejection..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
