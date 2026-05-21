@extends('staff.layouts.app')
@section('title', 'Customer — ' . $customer->name)
@section('page-title', 'Customer Detail')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">

        {{-- Customer Info --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-1">{{ $customer->name }}</h5>
                        <small class="text-muted">Joined {{ $customer->created_at->format('d M Y') }}</small>
                    </div>
                    <div>
                        @if($customer->is_active)
                            <span class="badge bg-success-subtle text-success-emphasis fs-6">Active</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger-emphasis fs-6">Inactive</span>
                        @endif
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Email</div>
                            <div class="fw-semibold">{{ $customer->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Phone</div>
                            <div class="fw-semibold">{{ $customer->phone ?? '—' }}</div>
                        </div>
                    </div>
                    @if($customer->country)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Country</div>
                            <div class="fw-semibold">{{ $customer->country }}</div>
                        </div>
                    </div>
                    @endif
                    @if($customer->city)
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">City</div>
                            <div class="fw-semibold">{{ $customer->city }}</div>
                        </div>
                    </div>
                    @endif
                    @if($customer->address)
                    <div class="col-12">
                        <div class="p-3 bg-light rounded">
                            <div class="text-muted small mb-1">Address</div>
                            <div class="fw-semibold">{{ $customer->address }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Uploaded Files --}}
        @php
            $enquiriesWithFiles = $enquiries->filter(fn($e) => $e->files->isNotEmpty());
            $ordersWithFiles    = $orders->filter(fn($o) => $o->files->isNotEmpty());
            $allFilesCount      = $enquiries->sum(fn($e) => $e->files->count())
                                + $orders->sum(fn($o) => $o->files->count());
        @endphp

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-1 d-flex align-items-center gap-2">
                    <i class="ri-folder-open-line text-primary"></i>
                    Uploaded Files
                    @if($allFilesCount > 0)
                        <span class="badge bg-primary-subtle text-primary-emphasis ms-1">{{ $allFilesCount }}</span>
                    @endif
                </h5>
                <p class="text-muted small mb-4">All files across this customer's service requests and orders.</p>

                @if($enquiriesWithFiles->isEmpty() && $ordersWithFiles->isEmpty())
                <div class="text-muted text-center py-4" style="border:2px dashed #e4e6ef;border-radius:10px;">
                    <i class="ri-folder-open-line" style="font-size:2rem;opacity:.3;"></i>
                    <p class="mb-0 mt-1 small">No files uploaded for this customer yet.</p>
                </div>
                @else

                    {{-- Enquiry files --}}
                    @foreach($enquiriesWithFiles as $enquiry)
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2 pb-2" style="border-bottom:2px solid #e4e6ef;">
                            <i class="ri-customer-service-2-line text-primary"></i>
                            <span class="fw-semibold">{{ $enquiry->service->name ?? 'Unknown Service' }}</span>
                            <span class="text-muted small">· Enquiry #{{ $enquiry->id }}</span>
                            <span class="text-muted small">· {{ $enquiry->created_at->format('d M Y') }}</span>
                            @php
                                $eBadge = match($enquiry->status) {
                                    'pending'          => 'bg-warning-subtle text-warning-emphasis',
                                    'in_progress'      => 'bg-info-subtle text-info-emphasis',
                                    'under_processing' => 'bg-primary-subtle text-primary-emphasis',
                                    'completed'        => 'badge-soft-teal',
                                    default            => 'bg-success-subtle text-success-emphasis',
                                };
                                $eLabel = match($enquiry->status) {
                                    'pending'          => 'Pending',
                                    'in_progress'      => 'In Progress',
                                    'under_processing' => 'Under Processing',
                                    'completed'        => 'Completed',
                                    default            => 'Resolved',
                                };
                            @endphp
                            <span class="badge {{ $eBadge }} ms-auto">{{ $eLabel }}</span>
                            <a href="{{ route('staff.enquiries.show', $enquiry) }}"
                               class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2">
                                <i class="ri-external-link-line"></i>
                            </a>
                        </div>

                        @foreach($enquiry->files as $file)
                        <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded"
                             style="border:1px solid #e4e6ef;background:#fafafa;">
                            <i class="{{ $file->icon_class }}" style="font-size:1.5rem;flex-shrink:0;"></i>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $file->file_name }}</div>
                                <div class="text-muted small">
                                    {{ $file->original_name }}
                                    &nbsp;·&nbsp; {{ $file->formatted_size }}
                                    &nbsp;·&nbsp; {{ $file->created_at->format('d M Y, h:i A') }}
                                </div>
                            </div>
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary flex-shrink-0 link-file-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#linkFileModal"
                                    data-file-id="{{ $file->id }}"
                                    data-file-name="{{ $file->file_name }}"
                                    data-file-source-type="enquiry"
                                    data-link-base="{{ route('staff.customers.files.link', [$customer, '__FILE__']) }}">
                                <i class="ri-links-line me-1"></i>Link
                            </button>
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                               class="btn btn-sm btn-outline-primary flex-shrink-0">
                                <i class="ri-download-line"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endforeach

                    {{-- Order files --}}
                    @foreach($ordersWithFiles as $order)
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2 pb-2" style="border-bottom:2px solid #d1fae5;">
                            <i class="ri-shopping-bag-line text-success"></i>
                            <span class="fw-semibold">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <span class="text-muted small">· {{ $order->created_at->format('d M Y') }}</span>
                            <span class="badge {{ $order->getStatusBadgeClass() }} ms-auto">{{ ucfirst($order->status) }}</span>
                        </div>

                        @foreach($order->files as $file)
                        <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded"
                             style="border:1px solid #e4e6ef;background:#fafafa;">
                            <i class="{{ $file->icon_class }}" style="font-size:1.5rem;flex-shrink:0;"></i>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $file->file_name }}</div>
                                <div class="text-muted small">
                                    {{ $file->original_name }}
                                    &nbsp;·&nbsp; {{ $file->formatted_size }}
                                    &nbsp;·&nbsp; {{ $file->created_at->format('d M Y, h:i A') }}
                                </div>
                            </div>
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary flex-shrink-0 link-file-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#linkFileModal"
                                    data-file-id="{{ $file->id }}"
                                    data-file-name="{{ $file->file_name }}"
                                    data-file-source-type="order"
                                    data-link-base="{{ route('staff.customers.order-files.link', [$customer, '__FILE__']) }}">
                                <i class="ri-links-line me-1"></i>Link
                            </button>
                            <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                               class="btn btn-sm btn-outline-primary flex-shrink-0">
                                <i class="ri-download-line"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endforeach

                @endif
            </div>
        </div>

        {{-- All Enquiries --}}
        @if($enquiries->isNotEmpty())
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-mail-send-line text-primary"></i>
                    All Enquiries
                    <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1">{{ $enquiries->count() }}</span>
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Service</th>
                                <th>Status</th>
                                <th>Files</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enquiries as $enq)
                            @php
                                $sBadge = match($enq->status) {
                                    'pending'          => 'bg-warning-subtle text-warning-emphasis',
                                    'in_progress'      => 'bg-info-subtle text-info-emphasis',
                                    'under_processing' => 'bg-primary-subtle text-primary-emphasis',
                                    'completed'        => 'badge-soft-teal',
                                    default            => 'bg-success-subtle text-success-emphasis',
                                };
                                $sLabel = match($enq->status) {
                                    'pending'          => 'Pending',
                                    'in_progress'      => 'In Progress',
                                    'under_processing' => 'Under Processing',
                                    'completed'        => 'Completed',
                                    default            => 'Resolved',
                                };
                            @endphp
                            <tr>
                                <td class="text-muted small">{{ $enq->id }}</td>
                                <td class="fw-semibold">{{ $enq->service->name ?? '—' }}</td>
                                <td><span class="badge {{ $sBadge }}">{{ $sLabel }}</span></td>
                                <td>
                                    @if($enq->files->isNotEmpty())
                                        <span class="badge bg-primary-subtle text-primary-emphasis">
                                            {{ $enq->files->count() }} file(s)
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $enq->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('staff.enquiries.show', $enq) }}"
                                       class="btn btn-sm btn-outline-primary py-0 px-2">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- All Orders --}}
        @if($orders->isNotEmpty())
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3 d-flex align-items-center gap-2">
                    <i class="ri-shopping-bag-line text-primary"></i>
                    All Orders
                    <span class="badge bg-secondary-subtle text-secondary-emphasis ms-1">{{ $orders->count() }}</span>
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Items</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Files</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $ord)
                            <tr>
                                <td class="fw-semibold">#{{ str_pad($ord->id, 6, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-muted small">{{ $ord->orderItems->count() }} item(s)</td>
                                <td class="fw-semibold">AED {{ number_format($ord->amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $ord->getStatusBadgeClass() }}">
                                        {{ ucfirst($ord->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($ord->files->isNotEmpty())
                                        <span class="badge bg-primary-subtle text-primary-emphasis">
                                            {{ $ord->files->count() }} file(s)
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $ord->created_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <a href="{{ route('staff.customers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Back to Customers
        </a>

    </div>
</div>

{{-- Link File Modal --}}
<div class="modal fade" id="linkFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-links-line me-1 text-primary"></i>Link File
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Linking: <strong id="linkModalFileName"></strong>
                </p>

                <div class="mb-3">
                    <label class="form-label small fw-semibold mb-1">Link to</label>
                    <select id="linkTargetTypeSelect" class="form-select form-select-sm">
                        <option value="enquiry">Service Request</option>
                        <option value="order">Order</option>
                    </select>
                </div>

                <div id="linkEnquiryList">
                    @if($enquiries->isEmpty())
                        <p class="text-muted text-center py-3 small">No service requests for this customer.</p>
                    @else
                        <div class="list-group list-group-flush" style="max-height:280px;overflow-y:auto;">
                            @foreach($enquiries as $enq)
                            <button type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center link-target-btn"
                                    data-target-type="enquiry"
                                    data-target-id="{{ $enq->id }}">
                                <span>
                                    <span class="fw-semibold">{{ $enq->service->name ?? 'Unknown Service' }}</span>
                                    <span class="text-muted small ms-1">#{{ $enq->id }}</span>
                                </span>
                                <span class="text-muted small">{{ $enq->created_at->format('d M Y') }}</span>
                            </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div id="linkOrderList" style="display:none;">
                    @if($orders->isEmpty())
                        <p class="text-muted text-center py-3 small">No orders for this customer.</p>
                    @else
                        <div class="list-group list-group-flush" style="max-height:280px;overflow-y:auto;">
                            @foreach($orders as $ord)
                            <button type="button"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center link-target-btn"
                                    data-target-type="order"
                                    data-target-id="{{ $ord->id }}">
                                <span>
                                    <span class="fw-semibold">Order #{{ str_pad($ord->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    <span class="badge {{ $ord->getStatusBadgeClass() }} ms-2">{{ ucfirst($ord->status) }}</span>
                                </span>
                                <span class="text-muted small">AED {{ number_format($ord->amount, 2) }}</span>
                            </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<form id="linkTargetForm" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="target_type" id="ltType">
    <input type="hidden" name="target_id" id="ltId">
</form>

@section('scripts')
<script>
(function () {
    var modal       = document.getElementById('linkFileModal');
    var typeSelect  = document.getElementById('linkTargetTypeSelect');
    var enquiryList = document.getElementById('linkEnquiryList');
    var orderList   = document.getElementById('linkOrderList');
    var form        = document.getElementById('linkTargetForm');
    var ltType      = document.getElementById('ltType');
    var ltId        = document.getElementById('ltId');
    var state       = { fileId: null, linkBase: '' };

    modal.addEventListener('show.bs.modal', function (e) {
        var btn = e.relatedTarget;
        state.fileId   = btn.dataset.fileId;
        state.linkBase = btn.dataset.linkBase;
        document.getElementById('linkModalFileName').textContent = btn.dataset.fileName;
        typeSelect.value          = 'enquiry';
        enquiryList.style.display = '';
        orderList.style.display   = 'none';
    });

    typeSelect.addEventListener('change', function () {
        var isEnquiry = this.value === 'enquiry';
        enquiryList.style.display = isEnquiry ? '' : 'none';
        orderList.style.display   = isEnquiry ? 'none' : '';
    });

    document.querySelectorAll('.link-target-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            ltType.value = this.dataset.targetType;
            ltId.value   = this.dataset.targetId;
            form.action  = state.linkBase.replace('__FILE__', state.fileId);
            form.submit();
        });
    });
}());
</script>
@endsection
@endsection
