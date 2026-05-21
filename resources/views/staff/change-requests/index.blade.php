@extends('staff.layouts.app')
@section('title', 'My Change Requests')
@section('page-title', 'My Change Requests')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">All My Requests</h4>
                    <div>
                        <a href="{{ route('staff.change-requests.create', ['type' => 'create']) }}" class="btn btn-primary btn-sm me-2">
                            <i class="ri-add-line me-1"></i> New Provider
                        </a>
                        <a href="{{ route('staff.change-requests.create', ['type' => 'update']) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ri-edit-line me-1"></i> Update Provider
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0" id="requests-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Company Name</th>
                                <th>Provider</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Reviewed By</th>
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
                                <td>{{ $req->serviceProvider->company_name ?? '—' }}</td>
                                <td>
                                    @if($req->status === 'approved')
                                        <span class="badge badge-soft-success">Approved</span>
                                    @elseif($req->status === 'rejected')
                                        <span class="badge badge-soft-danger">Rejected</span>
                                        @if($req->rejection_reason)
                                            <i class="ri-information-line text-danger ms-1" title="{{ $req->rejection_reason }}" data-bs-toggle="tooltip"></i>
                                        @endif
                                    @else
                                        <span class="badge badge-soft-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $req->created_at->format('d M Y') }}</td>
                                <td>{{ $req->reviewedBy->name ?? '—' }}</td>
                                <td>
                                    @if($req->isPending())
                                        <a href="{{ route('staff.change-requests.edit', $req) }}" class="btn btn-info btn-sm" title="Edit">
                                            <i class="ri-edit-line"></i>
                                        </a>
                                    @endif
                                    @if($req->status === 'rejected' && $req->rejection_reason)
                                        <button type="button" class="btn btn-secondary btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#reasonModal{{ $req->id }}" title="Rejection Reason">
                                            <i class="ri-information-line"></i>
                                        </button>
                                        <div class="modal fade" id="reasonModal{{ $req->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Rejection Reason</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">{{ $req->rejection_reason }}</div>
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
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endsection
@endsection
