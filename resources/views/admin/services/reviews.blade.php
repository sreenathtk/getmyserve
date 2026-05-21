@extends('admin.layouts.app')
@section('title', 'Reviews — ' . $service->name)
@section('page-title', 'Service Reviews')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-0">{{ $service->name }}</h5>
        <small class="text-muted">
            {{ $service->subCategory?->category->name }}
            @if($service->subCategory) › {{ $service->subCategory->name }} @endif
        </small>
    </div>
    <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="ri-arrow-left-line me-1"></i> Back to Services
    </a>
</div>

{{-- Rating Summary --}}
<div class="row g-3 mb-4">

    {{-- Big average score --}}
    <div class="col-md-3">
        <div class="card h-100 text-center">
            <div class="card-body d-flex flex-column justify-content-center py-4">
                @if($avgRating !== null)
                    <div class="display-3 fw-bold text-primary lh-1">{{ number_format($avgRating, 1) }}</div>
                    <div class="text-muted small mt-1">out of 5</div>
                    <div class="mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            @php $filled = $i <= round($avgRating); @endphp
                            <i class="ri-star-{{ $filled ? 'fill' : 'line' }} text-warning fs-5"></i>
                        @endfor
                    </div>
                    <div class="text-muted small mt-2">{{ $activeCount }} {{ Str::plural('review', $activeCount) }}</div>
                @else
                    <div class="display-3 fw-bold text-muted lh-1">—</div>
                    <div class="text-muted small mt-1">No reviews yet</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Rating distribution --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body py-3">
                <h6 class="card-title mb-3">Rating Breakdown</h6>
                @for($star = 5; $star >= 1; $star--)
                    @php
                        $count = $distribution[$star];
                        $pct   = $activeCount > 0 ? round(($count / $activeCount) * 100) : 0;
                    @endphp
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="text-muted small" style="width:14px;">{{ $star }}</span>
                        <i class="ri-star-fill text-warning small"></i>
                        <div class="progress flex-grow-1" style="height:8px;">
                            <div class="progress-bar bg-warning" style="width:{{ $pct }}%"></div>
                        </div>
                        <span class="text-muted small" style="width:24px;">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Quick stats --}}
    <div class="col-md-5">
        <div class="row g-3 h-100">
            <div class="col-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="fs-2 fw-semibold text-success">{{ $reviews->where('is_active', true)->count() }}</div>
                        <div class="text-muted small">Active Reviews</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="fs-2 fw-semibold text-danger">{{ $reviews->where('is_active', false)->count() }}</div>
                        <div class="text-muted small">Disabled Reviews</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Reviews table --}}
<div class="card">
    <div class="card-body">
        <h5 class="card-title mb-4">All Reviews</h5>

        @if($reviews->isEmpty())
            <p class="text-muted text-center py-4 fst-italic">No reviews submitted for this service yet.</p>
        @else
        <table id="reviews-table" class="table table-hover table-bordered dt-responsive nowrap w-100 align-middle">
            <thead>
                <tr>
                    <th style="width:200px;">User</th>
                    <th style="width:130px;">Rating</th>
                    <th>Comment</th>
                    <th style="width:130px;">Submitted</th>
                    <th style="width:110px;">Status</th>
                    <th style="width:110px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                <tr class="{{ $review->is_active ? '' : 'table-light text-muted' }}">
                    <td>
                        <div class="fw-semibold">{{ $review->user->name }}</div>
                        <div class="text-muted small">{{ $review->user->email }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="ri-star-{{ $i <= $review->rating ? 'fill' : 'line' }} text-warning"></i>
                            @endfor
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis mt-1">{{ $review->rating }}/5</span>
                    </td>
                    <td>
                        @if($review->comment)
                            <span class="{{ $review->is_active ? '' : 'text-decoration-line-through text-muted' }}">
                                {{ $review->comment }}
                            </span>
                        @else
                            <span class="fst-italic text-muted">No comment</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $review->created_at->format('d M Y') }}<br>{{ $review->created_at->format('H:i') }}</td>
                    <td>
                        @if($review->is_active)
                            <span class="badge bg-success-subtle text-success-emphasis">
                                <i class="ri-checkbox-circle-line me-1"></i>Active
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger-emphasis">
                                <i class="ri-forbid-line me-1"></i>Disabled
                            </span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('admin.services.reviews.toggle', [$service, $review]) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="btn btn-sm {{ $review->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                title="{{ $review->is_active ? 'Disable this review' : 'Re-enable this review' }}"
                                onclick="return confirm('{{ $review->is_active ? 'Disable this review? It will no longer count toward the rating.' : 'Re-enable this review?' }}')">
                                <i class="ri-{{ $review->is_active ? 'forbid' : 'checkbox-circle' }}-line me-1"></i>
                                {{ $review->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    $('#reviews-table').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[3, 'desc']],
        columnDefs: [
            { orderable: false, targets: [1, 5] }
        ]
    });
</script>
@endsection
