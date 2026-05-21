@extends('admin.layouts.app')
@section('title', 'Edit Customer — ' . $customer->name)
@section('page-title', 'Edit Customer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h4 class="card-title mb-1">{{ $customer->name }}</h4>
                        <small class="text-muted">
                            Customer since {{ $customer->created_at->format('d M Y') }}
                            &middot;
                            @if($customer->is_active)
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </small>
                    </div>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ri-arrow-left-line me-1"></i> Back
                    </a>
                </div>

                <form action="{{ route('admin.customers.update', $customer) }}" method="POST">
                    @csrf @method('PUT')

                    @include('admin.customers._form')

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Update Customer
                        </button>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
