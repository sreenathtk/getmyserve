@extends('staff.layouts.app')
@section('title', 'Assistance Requests')
@section('page-title', 'Assistance Requests')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">My Assistance Requests</h4>
                    <div class="d-flex gap-2">
                        @php $counts = $requests->groupBy('status'); @endphp
                        <span class="badge bg-warning-subtle text-warning-emphasis">
                            Pending: {{ $counts->get('pending', collect())->count() }}
                        </span>
                        <span class="badge bg-info-subtle text-info-emphasis">
                            Contacted: {{ $counts->get('contacted', collect())->count() }}
                        </span>
                        <span class="badge bg-success-subtle text-success-emphasis">
                            Completed: {{ $counts->get('completed', collect())->count() }}
                        </span>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <table id="ar-table" class="table table-hover table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $i => $req)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $req->full_name }}</td>
                            <td>{{ $req->phone }}</td>
                            <td>{{ $req->service->name ?? '—' }}</td>
                            <td>
                                @if($req->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Pending</span>
                                @elseif($req->status === 'contacted')
                                    <span class="badge bg-info-subtle text-info-emphasis">Contacted</span>
                                @else
                                    <span class="badge bg-success-subtle text-success-emphasis">Completed</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $req->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <a href="{{ route('staff.assistance-requests.show', $req) }}"
                                   class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                No assistance requests assigned to you.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('#ar-table').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[5, 'desc']],
        columnDefs: [{ orderable: false, targets: [6] }]
    });
</script>
@endsection
