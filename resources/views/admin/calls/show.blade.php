@extends('admin.layouts.app')
@section('title', 'Call Detail')
@section('page-title', 'Call Detail')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center gap-3 mb-3">
            <a href="{{ route('admin.call-logs.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ri-arrow-left-line me-1"></i>Back
            </a>
            <span class="badge {{ $callLog->status_badge_class }} fs-6">
                {{ ucfirst($callLog->status) }}
            </span>
        </div>
    </div>
</div>

<div class="row">
    {{-- Call Info --}}
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-uppercase fw-semibold mb-4" style="font-size:12px;letter-spacing:.05em;">
                    Call Information
                </h5>
                <dl class="row mb-0" style="font-size:14px;">
                    <dt class="col-5 text-muted fw-normal">Direction</dt>
                    <dd class="col-7 text-capitalize fw-semibold">{{ $callLog->direction }}</dd>

                    <dt class="col-5 text-muted fw-normal">From</dt>
                    <dd class="col-7 font-monospace">
                        {{ $callLog->caller_name ? $callLog->caller_name . ' · ' : '' }}{{ $callLog->caller_number }}
                    </dd>

                    <dt class="col-5 text-muted fw-normal">To</dt>
                    <dd class="col-7 font-monospace">
                        {{ $callLog->callee_name ? $callLog->callee_name . ' · ' : '' }}{{ $callLog->callee_number }}
                    </dd>

                    <dt class="col-5 text-muted fw-normal">Agent</dt>
                    <dd class="col-7">{{ $callLog->agent?->display_name ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Queue</dt>
                    <dd class="col-7">{{ $callLog->queue_name ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Started</dt>
                    <dd class="col-7">{{ $callLog->started_at?->format('d M Y, H:i:s') ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Answered</dt>
                    <dd class="col-7">{{ $callLog->answered_at?->format('d M Y, H:i:s') ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Ended</dt>
                    <dd class="col-7">{{ $callLog->ended_at?->format('d M Y, H:i:s') ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Duration</dt>
                    <dd class="col-7 font-monospace fw-semibold">{{ $callLog->duration_formatted }}</dd>

                    <dt class="col-5 text-muted fw-normal">Talk Time</dt>
                    <dd class="col-7 font-monospace">{{ gmdate('H:i:s', $callLog->talk_duration_seconds) }}</dd>

                    <dt class="col-5 text-muted fw-normal">Hold Time</dt>
                    <dd class="col-7 font-monospace">{{ gmdate('H:i:s', $callLog->hold_duration_seconds) }}</dd>

                    @if ($callLog->hangup_cause)
                        <dt class="col-5 text-muted fw-normal">Hangup Cause</dt>
                        <dd class="col-7 font-monospace small text-muted">{{ $callLog->hangup_cause }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6">
        {{-- Recording --}}
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-uppercase fw-semibold mb-3" style="font-size:12px;letter-spacing:.05em;">
                    Recording
                </h5>
                @if ($callLog->recording)
                    @if ($callLog->recording->is_downloaded)
                        <audio controls class="w-100"
                               src="{{ route('admin.calls.recording.stream', $callLog) }}"></audio>
                        <p class="text-muted small mt-2 mb-0">
                            {{ gmdate('H:i:s', $callLog->recording->duration_seconds ?? 0) }}
                            &middot; {{ strtoupper($callLog->recording->format) }}
                            &middot; {{ number_format(($callLog->recording->file_size_bytes ?? 0) / 1024, 0) }} KB
                        </p>
                    @else
                        <p class="text-warning mb-0">Recording is being downloaded…</p>
                    @endif
                @else
                    <p class="text-muted mb-0">No recording available.</p>
                @endif
            </div>
        </div>

        {{-- Transfers --}}
        @if ($callLog->transfers->count())
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase fw-semibold mb-3" style="font-size:12px;letter-spacing:.05em;">
                        Transfers
                    </h5>
                    @foreach ($callLog->transfers as $transfer)
                        <div class="small text-muted mb-2">
                            {{ $transfer->fromAgent?->display_name ?? '—' }}
                            &rarr; {{ $transfer->toAgent?->display_name ?? $transfer->to_number ?? '—' }}
                            <span class="text-muted">({{ $transfer->transfer_type }})</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<div class="row">
    {{-- Notes --}}
    <div class="col-12 col-md-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-uppercase fw-semibold mb-4" style="font-size:12px;letter-spacing:.05em;">
                    Notes
                </h5>

                @forelse ($callLog->notes as $note)
                    <div class="mb-3 pb-3 border-bottom">
                        <p class="mb-1 small">{{ $note->note }}</p>
                        <p class="mb-0 text-muted" style="font-size:11px;">
                            {{ $note->user->name }} &middot; {{ $note->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                @empty
                    <p class="text-muted small mb-3">No notes yet.</p>
                @endforelse

                <form x-data="{ note: '', loading: false }"
                      @submit.prevent="
                        loading = true;
                        fetch('{{ route('admin.calls.notes.store', $callLog) }}', {
                            method: 'POST',
                            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
                            body: JSON.stringify({ note }),
                        }).then(r => r.json()).then(d => {
                            if (d.success) { note = ''; window.location.reload(); }
                        }).finally(() => loading = false);
                      "
                      class="d-flex gap-2 mt-3">
                    <input x-model="note" type="text" placeholder="Add a note…" required
                           class="form-control form-control-sm" />
                    <button type="submit" :disabled="loading" class="btn btn-primary btn-sm flex-shrink-0">Add</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Link to entity --}}
    <div class="col-12 col-md-5">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title text-uppercase fw-semibold mb-4" style="font-size:12px;letter-spacing:.05em;">
                    Link to Entity
                </h5>
                <form x-data="{ type: '', id: '', loading: false }"
                      @submit.prevent="
                        loading = true;
                        fetch('{{ route('admin.calls.link', $callLog) }}', {
                            method: 'POST',
                            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
                            body: JSON.stringify({ linkable_type: type, linkable_id: id }),
                        }).then(r => r.json()).then(d => {
                            if (d.success) alert('Linked successfully.');
                            else alert(d.error);
                        }).finally(() => loading = false);
                      ">
                    <div class="mb-2">
                        <select x-model="type" class="form-select form-select-sm">
                            <option value="">Select type…</option>
                            <option value="order">Order</option>
                            <option value="enquiry">Service Request</option>
                            <option value="assistance_request">Assistance Request</option>
                            <option value="customer">Customer</option>
                            <option value="provider">Service Provider</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <input x-model="id" type="number" placeholder="Entity ID" min="1"
                               class="form-control form-control-sm" />
                    </div>
                    <button type="submit" :disabled="loading || !type || !id"
                            class="btn btn-dark btn-sm w-100">Link</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
