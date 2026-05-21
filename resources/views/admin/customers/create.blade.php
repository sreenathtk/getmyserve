@extends('admin.layouts.app')
@section('title', 'Add Customer')
@section('page-title', 'Add Customer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Add New Customer</h4>

                <form action="{{ route('admin.customers.store') }}" method="POST">
                    @csrf

                    @include('admin.customers._form')

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Save Customer
                        </button>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
