@extends('admin.layouts.app')
@section('title', 'Change Requests')
@section('page-title', 'Service Provider Change Requests')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">All Change Requests</h4>
                    <div>
                        <span class="badge badge-soft-warning me-2">
                            {{ $requests->where('status','pending')->count() }} Pending
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0" id="requests-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Company Name</th>
                                <th>Requested By</th>
                                <th>Role</th>
                                <th>Target Provider</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $index => $req)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($req->request_type === 'create')
                                        <span class="badge badge-soft-primary">New Provider</span>
                                    @else
                                        <span class="badge badge-soft-info">Update</span>
                                    @endif
                                </td>
                                <td><strong>{{ $req->payload['provider']['company_name'] ?? '—' }}</strong></td>
                                <td>{{ $req->requestedBy->name ?? '—' }}</td>
                                <td>
                                    @if($req->requestedBy?->role === 'staff')
                                        <span class="badge badge-soft-secondary">Staff</span>
                                    @elseif($req->requestedBy?->role === 'service_provider')
                                        <span class="badge badge-soft-info">Provider</span>
                                    @else
                                        <span class="badge badge-soft-secondary">{{ $req->requestedBy?->role ?? '—' }}</span>
                                    @endif
                                </td>
                                <td>{{ $req->serviceProvider->company_name ?? '—' }}</td>
                                <td>
                                    @if($req->status === 'approved')
                                        <span class="badge badge-soft-success">Approved</span>
                                    @elseif($req->status === 'rejected')
                                        <span class="badge badge-soft-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-soft-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $req->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.change-requests.show', $req) }}" class="btn btn-primary btn-sm" title="Review">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    @if($req->isPending())
                                    <form action="{{ route('admin.change-requests.approve', $req) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Approve this change request and apply changes?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                            <i class="ri-checkbox-circle-line"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm" title="Reject"
                                        data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                        <i class="ri-close-circle-line"></i>
                                    </button>

                                    {{-- Reject Modal --}}
                                    <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.change-requests.reject', $req) }}" method="POST">
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
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    $('#requests-table').DataTable({
        responsive: true, paging: true, searching: true, ordering: true,
        order: [[0, 'desc']],
        language: { emptyTable: 'No change requests found' }
    });
});
</script>
@endsection
@endsection
