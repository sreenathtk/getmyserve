@extends('admin.layouts.app')
@section('title', 'Offers')
@section('page-title', 'Offers Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">All Offers</h4>
                    <a href="{{ route('admin.offers.create') }}" class="btn btn-primary btn-sm">
                        <i class="ri-add-line me-1"></i> Add Offer
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-centered mb-0" id="offers-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Service</th>
                                <th>Type</th>
                                <th>Price (AED)</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Featured</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offers as $index => $offer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $offer->title }}</strong></td>
                                <td>
                                    @if($offer->services->isNotEmpty())
                                        {{ $offer->services->pluck('name')->join(', ') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $typeClass = match($offer->offer_type) {
                                            'best_value'  => 'badge-soft-success',
                                            'flash_sale'  => 'badge-soft-danger',
                                            'seasonal'    => 'badge-soft-info',
                                            default       => 'badge-soft-warning',
                                        };
                                    @endphp
                                    <span class="badge {{ $typeClass }}">{{ $offer->offer_type_label }}</span>
                                </td>
                                <td>{{ number_format($offer->offer_price, 2) }}</td>
                                <td>{{ $offer->start_date->format('d M Y') }}</td>
                                <td>{{ $offer->end_date->format('d M Y') }}</td>
                                <td>
                                    @if($offer->is_featured)
                                        <span class="badge badge-soft-primary">Yes</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($offer->is_currently_active)
                                        <span class="badge badge-soft-success">Live</span>
                                    @elseif($offer->is_active && $offer->start_date > now())
                                        <span class="badge badge-soft-info">Scheduled</span>
                                    @elseif($offer->is_active && $offer->end_date < now())
                                        <span class="badge badge-soft-warning">Expired</span>
                                    @else
                                        <span class="badge badge-soft-danger">Disabled</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.offers.edit', $offer) }}" class="btn btn-info btn-sm" title="Edit">
                                        <i class="ri-edit-line"></i>
                                    </a>
                                    <form action="{{ route('admin.offers.toggle', $offer) }}" method="POST" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-{{ $offer->is_active ? 'warning' : 'success' }} btn-sm">
                                            <i class="ri-{{ $offer->is_active ? 'close' : 'check' }}-line"></i>
                                            {{ $offer->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
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
        $('#offers-table').DataTable({
            responsive: true,
            paging: true,
            searching: true,
            ordering: true,
            language: {
                emptyTable: "No offers found"
            }
        });
    });
</script>
@endsection
@endsection
