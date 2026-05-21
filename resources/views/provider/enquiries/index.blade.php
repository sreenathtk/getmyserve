@extends('provider.layouts.app')
@section('title', 'Service Requests')
@section('page-title', 'My Service Requests')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4">Assigned Service Requests</h4>

        @if($enquiries->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="ri-mail-send-line" style="font-size:3rem;opacity:.3;"></i>
                <p class="mt-2">No service requests have been assigned to you yet.</p>
            </div>
        @else
        <div class="table-responsive">
            <table id="enquiries-table" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enquiries as $enq)
                    @php
                        $badge = match($enq->status) {
                            'pending'          => 'badge-soft-warning',
                            'in_progress'      => 'badge-soft-info',
                            'under_processing' => 'badge-soft-primary',
                            'completed'        => 'badge-soft-teal',
                            'resolved'         => 'badge-soft-success',
                            default            => 'badge-soft-secondary',
                        };
                        $label = match($enq->status) {
                            'pending'          => 'Pending',
                            'in_progress'      => 'In Progress',
                            'under_processing' => 'Under Processing',
                            'completed'        => 'Completed',
                            'resolved'         => 'Resolved',
                            default            => ucfirst($enq->status),
                        };
                    @endphp
                    <tr>
                        <td class="text-muted small">{{ $enq->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $enq->first_name }} {{ $enq->last_name }}</div>
                            <div class="text-muted small">{{ $enq->email }}</div>
                        </td>
                        <td>{{ $enq->service->name ?? '—' }}</td>
                        <td><span class="badge {{ $badge }}">{{ $label }}</span></td>
                        <td class="text-muted small">{{ $enq->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('provider.enquiries.show', $enq) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="ri-eye-line me-1"></i>View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$('#enquiries-table').DataTable({ order: [[4,'desc']], pageLength: 25 });
</script>
@endsection
