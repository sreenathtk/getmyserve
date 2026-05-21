@extends('admin.layouts.app')

@section('title', 'WhatsApp Templates')
@section('page-title', 'WhatsApp Templates')

@section('content')

{{-- Page header --}}
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <p class="text-muted mb-0" style="font-size:13px;">
            Templates are managed in your
            <a href="https://app.wati.io" target="_blank" rel="noopener" class="text-primary fw-semibold">
                WATI dashboard <i class="ri-external-link-line"></i>
            </a>
            and approved by Meta. Use <strong>Sync from WATI</strong> to refresh the status shown here.
        </p>
    </div>
    <div class="d-flex align-items-center gap-3">
        @if($lastSyncedAt)
            <span class="text-muted" style="font-size:12px;">
                <i class="ri-refresh-line me-1"></i>Last synced: {{ $lastSyncedAt->diffForHumans() }}
            </span>
        @endif
        <form method="POST" action="{{ route('admin.whatsapp.templates.sync') }}" id="syncForm">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm" id="syncBtn">
                <i class="ri-refresh-line me-1"></i> Sync from WATI
            </button>
        </form>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($templates->whereNull('last_synced_at')->count() > 0)
    <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
        <i class="ri-information-line fs-5 flex-shrink-0"></i>
        <span>Some templates have never been synced. Click <strong>Sync from WATI</strong> to load the latest status from your WATI account.</span>
    </div>
@endif

{{-- Status summary cards --}}
@php
    $approved = $templates->where('status', 'APPROVED')->count();
    $pending  = $templates->where('status', 'PENDING')->count();
    $rejected = $templates->where('status', 'REJECTED')->count();
    $missing  = $templates->whereNull('status')->count();
@endphp
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card mb-0 border-0 shadow-sm">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;background:#d1fae5;">
                    <i class="ri-checkbox-circle-line text-success fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">{{ $approved }}</div>
                    <div class="text-muted" style="font-size:12px;">Approved</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mb-0 border-0 shadow-sm">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;background:#fef9c3;">
                    <i class="ri-time-line text-warning fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">{{ $pending }}</div>
                    <div class="text-muted" style="font-size:12px;">Pending</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mb-0 border-0 shadow-sm">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;background:#fee2e2;">
                    <i class="ri-close-circle-line text-danger fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">{{ $rejected }}</div>
                    <div class="text-muted" style="font-size:12px;">Rejected</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card mb-0 border-0 shadow-sm">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px;height:42px;background:#f1f5f9;">
                    <i class="ri-question-line text-secondary fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">{{ $missing }}</div>
                    <div class="text-muted" style="font-size:12px;">Not Found</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Customer templates --}}
@foreach([['customer', 'Customer Notifications', 'ri-user-3-line'], ['provider', 'Service Provider Notifications', 'ri-building-2-line']] as [$type, $label, $icon])
@php $group = $templates->where('recipient_type', $type); @endphp
@if($group->count())
<div class="card mb-4">
    <div class="card-header py-3 d-flex align-items-center gap-2">
        <i class="{{ $icon }} text-primary"></i>
        <h6 class="mb-0 fw-semibold">{{ $label }}</h6>
        <span class="badge badge-soft-primary ms-1">{{ $group->count() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:200px;">Template Name</th>
                        <th>Description</th>
                        <th style="width:140px;">WATI Element Name</th>
                        <th style="width:115px;">Status</th>
                        <th>Body Preview</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group as $template)
                    <tr>
                        {{-- Template key --}}
                        <td>
                            <code class="text-primary" style="font-size:11px;">{{ $template->template_key }}</code>
                        </td>

                        {{-- Description --}}
                        <td class="text-muted" style="font-size:13px;">
                            {{ $template->description }}
                        </td>

                        {{-- Element name --}}
                        <td>
                            <code style="font-size:11px;">{{ $template->element_name }}</code>
                        </td>

                        {{-- Status badge --}}
                        <td>
                            @if($template->status)
                                <span class="badge {{ $template->statusBadgeClass() }} d-inline-flex align-items-center gap-1">
                                    <i class="{{ $template->statusIcon() }}"></i>
                                    {{ $template->status }}
                                </span>
                                @if($template->status === 'REJECTED' && $template->rejection_reason)
                                    <div class="text-danger mt-1" style="font-size:11px;" title="{{ $template->rejection_reason }}">
                                        <i class="ri-information-line"></i>
                                        {{ Str::limit($template->rejection_reason, 40) }}
                                    </div>
                                @endif
                            @else
                                <span class="badge badge-soft-secondary d-inline-flex align-items-center gap-1">
                                    <i class="ri-question-line"></i> Not Found
                                </span>
                                <div class="text-warning mt-1" style="font-size:11px;">
                                    Not yet created in WATI
                                </div>
                            @endif
                        </td>

                        {{-- Body preview --}}
                        <td>
                            @if($template->body)
                                <span class="text-muted" style="font-size:12px;line-height:1.4;">
                                    {{ Str::limit($template->body, 90) }}
                                </span>
                                @if(strlen($template->body) > 90)
                                    <a href="#" class="text-primary small"
                                       data-bs-toggle="modal"
                                       data-bs-target="#bodyModal"
                                       data-body="{{ e($template->body) }}"
                                       data-name="{{ $template->element_name }}">
                                        view full
                                    </a>
                                @endif
                            @else
                                <span class="text-muted fst-italic" style="font-size:12px;">
                                    Sync to load body from WATI
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endforeach

{{-- Full body modal --}}
<div class="modal fade" id="bodyModal" tabindex="-1" aria-labelledby="bodyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold" id="bodyModalLabel">
                    <i class="ri-whatsapp-line me-1" style="color:#25d366;"></i>
                    Template Body — <code id="modalTemplateName" style="font-size:13px;"></code>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:14px;line-height:1.7;white-space:pre-wrap;word-break:break-word;" id="modalBody"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    // Populate modal with template body on trigger
    const bodyModal = document.getElementById('bodyModal');
    if (bodyModal) {
        bodyModal.addEventListener('show.bs.modal', function (e) {
            const trigger = e.relatedTarget;
            document.getElementById('modalBody').textContent         = trigger.dataset.body;
            document.getElementById('modalTemplateName').textContent = trigger.dataset.name;
        });
    }

    // Show spinner on sync button click
    const syncForm = document.getElementById('syncForm');
    const syncBtn  = document.getElementById('syncBtn');
    if (syncForm && syncBtn) {
        syncForm.addEventListener('submit', function () {
            syncBtn.disabled   = true;
            syncBtn.innerHTML  = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Syncing…';
        });
    }
}());
</script>
@endpush
