@extends('admin.layouts.app')
@section('title', 'Service Providers')
@section('page-title', 'Service Providers')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title">All Service Providers</h4>
                    <a href="{{ route('admin.service-providers.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i> Add Service Provider
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0" id="providers-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Company</th>
                                <th>Primary Contact</th>
                                <th>Email</th>
                                <th>Services</th>
                                <th>Approval</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($providers as $index => $provider)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $provider->company_name }}</strong></td>
                                <td>{{ $provider->primary_contact_name }}<br><small class="text-muted">{{ $provider->primary_contact_mobile }}</small></td>
                                <td>{{ $provider->user->email ?? '-' }}</td>
                                <td>
                                    @foreach($provider->services->take(2) as $service)
                                        <span class="badge badge-soft-primary">{{ $service->name }}</span>
                                    @endforeach
                                    @if($provider->services->count() > 2)
                                        <span class="badge badge-soft-info">+{{ $provider->services->count() - 2 }} more</span>
                                    @endif
                                </td>
                                <td>
                                    @if($provider->approval_status === 'approved')
                                        <span class="badge badge-soft-success">Approved</span>
                                    @elseif($provider->approval_status === 'rejected')
                                        <span class="badge badge-soft-danger">Rejected</span>
                                    @else
                                        <span class="badge badge-soft-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($provider->is_active)
                                        <span class="badge badge-soft-success">Active</span>
                                    @else
                                        <span class="badge badge-soft-danger">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.service-providers.show', $provider) }}" class="btn btn-primary btn-sm" title="View">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="{{ route('admin.service-providers.edit', $provider) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.service-providers.toggle', $provider) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-{{ $provider->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $provider->is_active ? 'Disable' : 'Enable' }}">
                                            <i class="ri-{{ $provider->is_active ? 'close' : 'check' }}-line"></i>
                                        </button>
                                    </form>
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
        $('#providers-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            order: [[0, 'desc']],
            language: {
                emptyTable: 'No service providers found'
            }
        });
    });
</script>
@endsection
@endsection
