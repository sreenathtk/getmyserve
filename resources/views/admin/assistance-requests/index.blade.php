@extends('admin.layouts.app')
@section('title', 'Assistance Requests')
@section('page-title', 'Assistance Requests')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Assistance Requests</h4>
                    <div class="d-flex gap-2 align-items-center">
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
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <table id="ar-table" class="table table-bordered table-hover dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Service</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $index => $req)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $req->full_name }}</td>
                            <td>{{ $req->email }}</td>
                            <td>{{ $req->phone }}</td>
                            <td>{{ $req->service->name ?? '—' }}</td>
                            <td>
                                @if($req->assignedStaff)
                                    <span class="badge bg-primary-subtle text-primary-emphasis">
                                        {{ $req->assignedStaff->name }}
                                    </span>
                                @else
                                    <span class="text-muted small">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @if($req->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Pending</span>
                                @elseif($req->status === 'contacted')
                                    <span class="badge bg-info-subtle text-info-emphasis">Contacted</span>
                                @else
                                    <span class="badge bg-success-subtle text-success-emphasis">Completed</span>
                                @endif
                            </td>
                            <td>{{ $req->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <a href="{{ route('admin.assistance-requests.show', $req) }}"
                                    class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No assistance requests yet.</td>
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
        order: [[7, 'desc']],
        columnDefs: [{ orderable: false, targets: [8] }]
    });
</script>
@endsection
