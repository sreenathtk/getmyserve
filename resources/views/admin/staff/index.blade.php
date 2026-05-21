@extends('admin.layouts.app')
@section('title', 'Staff')
@section('page-title', 'Staff Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Staff Members</h4>
                    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-user-add-line me-1"></i> Add Staff
                    </a>
                </div>

                <table id="staff-table" class="table table-bordered table-hover dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Assigned Categories</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Login</th>
                            <th>Last Logout</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staff as $index => $member)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>
                                @php
                                    $catIds = $member->staffAssignments->pluck('category_id')->unique();
                                @endphp
                                @if($catIds->isEmpty())
                                    <span class="text-muted fst-italic">None assigned</span>
                                @else
                                    <span class="badge bg-info-subtle text-info-emphasis">{{ $catIds->count() }} {{ Str::plural('category', $catIds->count()) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($member->is_active)
                                    <span class="badge bg-success-subtle text-success-emphasis">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger-emphasis">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $member->created_at->format('d M Y') }}</td>
                            <td>
                                @if($member->last_login_at)
                                    <span title="{{ $member->last_login_at->format('d M Y, h:i A') }}">
                                        {{ $member->last_login_at->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $member->last_login_at->format('h:i A') }}</small>
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">Never</span>
                                @endif
                            </td>
                            <td>
                                @if($member->last_logout_at)
                                    <span title="{{ $member->last_logout_at->format('d M Y, h:i A') }}">
                                        {{ $member->last_logout_at->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $member->last_logout_at->format('h:i A') }}</small>
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">Never</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.staff.edit', $member) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="ri-edit-line"></i>
                                </a>
                                <form action="{{ route('admin.staff.toggle', $member) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $member->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                        title="{{ $member->is_active ? 'Disable' : 'Enable' }}"
                                        onclick="return confirm('{{ $member->is_active ? 'Disable' : 'Enable' }} this staff member?')">
                                        <i class="ri-{{ $member->is_active ? 'forbid' : 'check' }}-line"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No staff members found.</td>
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
    $('#staff-table').DataTable({
        responsive: true,
        pageLength: 25,
        columnDefs: [{ orderable: false, targets: [3, 8] }]
    });
</script>
@endsection
