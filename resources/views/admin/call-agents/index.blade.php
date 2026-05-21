@extends('admin.layouts.app')
@section('title', 'Call Agents')
@section('page-title', 'Call Agents')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Call Agents</h4>
                    <form action="{{ route('admin.call-agents.sync') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ri-refresh-line me-1"></i>Sync from ZIWO
                        </button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Agent</th>
                                <th>ZIWO ID / Extension</th>
                                <th>Linked User</th>
                                <th>Status</th>
                                <th>Calls Today</th>
                                <th>Active</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($agents as $agent)
                                <tr>
                                    <td>
                                        <p class="fw-semibold mb-0 small">{{ $agent->display_name }}</p>
                                        <p class="text-muted mb-0" style="font-size:11px;">{{ $agent->ziwo_username }}</p>
                                    </td>
                                    <td class="font-monospace small text-muted">
                                        {{ $agent->ziwo_agent_id }}
                                        @if ($agent->ziwo_extension)
                                            &middot; ext {{ $agent->ziwo_extension }}
                                        @endif
                                    </td>
                                    <td class="small">{{ $agent->user?->name ?? '—' }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($agent->status) {
                                                'online'  => 'badge-soft-success',
                                                'on_call' => 'badge-soft-primary',
                                                'busy'    => 'badge-soft-warning',
                                                default   => 'badge-soft-info',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($agent->status) }}</span>
                                    </td>
                                    <td class="small text-muted">{{ $agent->total_calls_today }}</td>
                                    <td>
                                        @if ($agent->is_active)
                                            <span class="badge badge-soft-success">Active</span>
                                        @else
                                            <span class="badge badge-soft-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        No agents found. Click "Sync from ZIWO" to import agents.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
