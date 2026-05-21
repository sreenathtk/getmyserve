@extends('admin.layouts.app')
@section('title', 'Communication History')
@section('page-title', 'Communication History')

@section('styles')
<style>
    .timeline-line {
        position: absolute;
        left: 19px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #eff2f7;
    }
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        font-size: 16px;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-9">

        <p class="text-muted mb-4">
            {{ class_basename(get_class($entity)) }} #{{ $entity->id }}
            @if (isset($entity->name)) &mdash; {{ $entity->name }} @endif
        </p>

        {{-- Add note --}}
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('admin.communication.note', [$type, $entity->id]) }}" method="POST"
                      class="d-flex gap-2">
                    @csrf
                    <input name="note" type="text" placeholder="Add a note to timeline…"
                           class="form-control" required />
                    <button type="submit" class="btn btn-primary flex-shrink-0">Add Note</button>
                </form>
            </div>
        </div>

        @if ($history->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="ri-chat-history-line" style="font-size:3rem;"></i>
                <p class="mt-2 mb-1">No communication history yet.</p>
                <p class="small">Calls, WhatsApp messages, and notes will appear here.</p>
            </div>
        @else
            <div class="position-relative" style="padding-left:56px;">
                <div class="timeline-line"></div>

                @foreach ($history as $item)
                    <div class="d-flex gap-3 mb-4" style="margin-left:-56px;">
                        {{-- Channel icon --}}
                        <div class="timeline-icon
                            @if ($item->channel === 'call') bg-primary bg-opacity-10 text-primary
                            @elseif($item->channel === 'whatsapp') bg-success bg-opacity-10 text-success
                            @elseif($item->channel === 'note') bg-warning bg-opacity-10 text-warning
                            @else bg-secondary bg-opacity-10 text-secondary @endif">
                            @if ($item->channel === 'call')
                                <i class="ri-phone-fill"></i>
                            @elseif ($item->channel === 'whatsapp')
                                <i class="ri-whatsapp-fill"></i>
                            @elseif ($item->channel === 'note')
                                <i class="ri-sticky-note-fill"></i>
                            @else
                                <i class="ri-information-fill"></i>
                            @endif
                        </div>

                        {{-- Content card --}}
                        <div class="card flex-1 w-100">
                            <div class="card-body py-3">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                    <p class="fw-semibold mb-0 small">{{ $item->summary }}</p>
                                    <span class="text-muted text-nowrap flex-shrink-0" style="font-size:11px;">
                                        {{ $item->happened_at->format('d M Y, H:i') }}
                                    </span>
                                </div>

                                @if ($item->body)
                                    <p class="text-muted small mb-1">{{ $item->body }}</p>
                                @endif

                                {{-- Call-specific details --}}
                                @if ($item->channel === 'call' && $item->reference instanceof \App\Models\CallLog)
                                    @php $call = $item->reference; @endphp
                                    <div class="d-flex flex-wrap gap-2 align-items-center mt-2">
                                        <span class="badge {{ $call->status_badge_class }}">{{ ucfirst($call->status) }}</span>
                                        <span class="text-muted small"><i class="ri-time-line"></i> {{ $call->duration_formatted }}</span>
                                        @if ($call->agent)
                                            <span class="text-muted small"><i class="ri-user-line"></i> {{ $call->agent->display_name }}</span>
                                        @endif
                                        @if ($call->recording?->is_downloaded)
                                            <audio controls class="w-100 mt-1" style="height:32px;"
                                                   src="{{ route('admin.calls.recording.stream', $call) }}"></audio>
                                        @endif
                                        <a href="{{ route('admin.call-logs.show', $call) }}"
                                           class="btn btn-outline-primary btn-sm ms-auto" style="font-size:11px;">
                                            View call →
                                        </a>
                                    </div>
                                @endif

                                @if ($item->agent || $item->user)
                                    <p class="text-muted mb-0 mt-2" style="font-size:11px;">
                                        By {{ $item->agent?->display_name ?? $item->user?->name }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($history->hasPages())
                <div class="mt-3">{{ $history->links() }}</div>
            @endif
        @endif

    </div>
</div>
@endsection
