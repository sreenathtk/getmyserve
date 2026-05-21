@extends('admin.layouts.app')
@section('title', 'Call Logs')
@section('page-title', 'Call Logs')

@section('content')
<div class="row">
    <div class="col-12">

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-12 col-md-3">
                        <input name="search" value="{{ request('search') }}" type="text"
                               class="form-control form-control-sm" placeholder="Search number or name…" />
                    </div>
                    <div class="col-6 col-md-2">
                        <select name="direction" class="form-select form-select-sm">
                            <option value="">All Directions</option>
                            <option value="inbound"  @selected(request('direction') === 'inbound')>Inbound</option>
                            <option value="outbound" @selected(request('direction') === 'outbound')>Outbound</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                            @foreach (['answered','missed','ended','transferred','failed'] as $s)
                                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <select name="agent_id" class="form-select form-select-sm">
                            <option value="">All Agents</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" @selected(request('agent_id') == $agent->id)>
                                    {{ $agent->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <input name="date_from" type="date" value="{{ request('date_from') }}"
                               class="form-control form-control-sm" />
                    </div>
                    <div class="col-12 col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2">
                    <h4 class="card-title mb-0">Call Logs</h4>
                    <a href="{{ route('admin.call-agents.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ri-headphone-line me-1"></i>Manage Agents
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Direction</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Agent</th>
                                <th>Status</th>
                                <th>Duration</th>
                                <th>Started</th>
                                <th>Rec.</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($callLogs as $call)
                                <tr>
                                    <td>
                                        <span class="badge {{ $call->direction === 'inbound' ? 'badge-soft-primary' : 'badge-soft-info' }}">
                                            {{ ucfirst($call->direction) }}
                                        </span>
                                    </td>
                                    <td class="font-monospace small">
                                        {{ $call->caller_name ? $call->caller_name . ' · ' : '' }}{{ $call->caller_number }}
                                    </td>
                                    <td class="font-monospace small">{{ $call->callee_number }}</td>
                                    <td class="small text-muted">{{ $call->agent?->display_name ?? '—' }}</td>
                                    <td>
                                        <span class="badge {{ $call->status_badge_class }}">
                                            {{ ucfirst($call->status) }}
                                        </span>
                                    </td>
                                    <td class="font-monospace small">{{ $call->duration_formatted }}</td>
                                    <td class="small text-muted">
                                        {{ $call->started_at?->format('d M, H:i') ?? '—' }}
                                    </td>
                                    <td>
                                        @if ($call->recording)
                                            <i class="ri-mic-fill text-success small"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.call-logs.show', $call) }}"
                                           class="btn btn-outline-primary btn-sm">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">No call logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($callLogs->hasPages())
                    <div class="px-3 py-2">
                        {{ $callLogs->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
