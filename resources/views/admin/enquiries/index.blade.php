@extends('admin.layouts.app')
@section('title', 'Service Requests')
@section('page-title', 'Service Requests')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Service Enquiries</h4>
                    <div class="d-flex gap-2 align-items-center">
                        @php
                            $counts = $enquiries->groupBy('status');
                        @endphp
                        <span class="badge bg-warning-subtle text-warning-emphasis">
                            Pending: {{ $counts->get('pending', collect())->count() }}
                        </span>
                        <span class="badge bg-info-subtle text-info-emphasis">
                            In Progress: {{ $counts->get('in_progress', collect())->count() }}
                        </span>
                        <span class="badge bg-primary-subtle text-primary-emphasis">
                            Under Processing: {{ $counts->get('under_processing', collect())->count() }}
                        </span>
                        <span class="badge badge-soft-teal">
                            Completed: {{ $counts->get('completed', collect())->count() }}
                        </span>
                        <span class="badge bg-success-subtle text-success-emphasis">
                            Resolved: {{ $counts->get('resolved', collect())->count() }}
                        </span>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <table id="enq-table" class="table table-bordered table-hover dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enquiries as $index => $enq)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $enq->full_name }}</td>
                            <td>{{ $enq->email }}</td>
                            <td>{{ $enq->phone }}</td>
                            <td>{{ $enq->service->name ?? '—' }}</td>
                            <td>
                                @if($enq->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning-emphasis">Pending</span>
                                @elseif($enq->status === 'in_progress')
                                    <span class="badge bg-info-subtle text-info-emphasis">In Progress</span>
                                @elseif($enq->status === 'under_processing')
                                    <span class="badge bg-primary-subtle text-primary-emphasis">Under Processing</span>
                                @elseif($enq->status === 'completed')
                                    <span class="badge badge-soft-teal">Completed</span>
                                @else
                                    <span class="badge bg-success-subtle text-success-emphasis">Resolved</span>
                                @endif
                            </td>
                            <td>{{ $enq->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <a href="{{ route('admin.enquiries.show', $enq) }}"
                                    class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No enquiries yet.</td>
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
    $('#enq-table').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[6, 'desc']],
        columnDefs: [{ orderable: false, targets: [7] }]
    });
</script>
@endsection
