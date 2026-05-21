{{--
    Shared form fields for customer create / edit.
    Expects: $countries array, optional $customer model.
--}}
@php $editing = isset($customer); @endphp

{{-- Personal Details --}}
<h6 class="text-muted fw-semibold mb-3 pb-1 border-bottom">Personal Details</h6>

<div class="row mb-3">
    <label for="name" class="col-sm-2 col-form-label">Full Name <span class="text-danger">*</span></label>
    <div class="col-sm-10">
        <input type="text" class="form-control @error('name') is-invalid @enderror"
            id="name" name="name"
            value="{{ old('name', $customer->name ?? '') }}"
            placeholder="e.g. Ahmed Al Mansoori" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="email" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
    <div class="col-sm-10">
        <input type="email" class="form-control @error('email') is-invalid @enderror"
            id="email" name="email"
            value="{{ old('email', $customer->email ?? '') }}"
            placeholder="you@example.com" required>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="phone" class="col-sm-2 col-form-label">Phone <span class="text-danger">*</span></label>
    <div class="col-sm-10">
        <div class="input-group" style="max-width:320px;">
            <span class="input-group-text" id="dial-code-display" style="min-width:64px;justify-content:center;font-weight:600;color:#5664d2;">+971</span>
            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                id="phone" name="phone"
                value="{{ old('phone', $customer->phone ?? '') }}"
                placeholder="50 123 4567" required>
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

{{-- Location --}}
<h6 class="text-muted fw-semibold mb-3 pb-1 border-bottom mt-4">Location</h6>

<div class="row mb-3">
    <label for="country" class="col-sm-2 col-form-label">Country <span class="text-danger">*</span></label>
    <div class="col-sm-10">
        <select class="form-select @error('country') is-invalid @enderror"
            id="country" name="country" style="max-width:320px;" required>
            <option value="">— Select Country —</option>
            @foreach($countries as $code => $label)
                <option value="{{ $code }}"
                    {{ old('country', $customer->country ?? 'AE') === $code ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="city" class="col-sm-2 col-form-label">City <span class="text-danger">*</span></label>
    <div class="col-sm-10">
        <input type="text" class="form-control @error('city') is-invalid @enderror"
            id="city" name="city" style="max-width:320px;"
            value="{{ old('city', $customer->city ?? '') }}"
            placeholder="e.g. Dubai" required>
        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row mb-3">
    <label for="address" class="col-sm-2 col-form-label">Address <span class="text-danger">*</span></label>
    <div class="col-sm-10">
        <textarea class="form-control @error('address') is-invalid @enderror"
            id="address" name="address" rows="2"
            placeholder="Villa / flat number, building, street, area"
            required>{{ old('address', $customer->address ?? '') }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

{{-- Security --}}
<h6 class="text-muted fw-semibold mb-3 pb-1 border-bottom mt-4">
    {{ $editing ? 'Change Password' : 'Password' }}
</h6>

<div class="row mb-3">
    <label for="password" class="col-sm-2 col-form-label">
        Password @if(!$editing)<span class="text-danger">*</span>@endif
    </label>
    <div class="col-sm-10">
        <input type="password" class="form-control @error('password') is-invalid @enderror"
            id="password" name="password"
            placeholder="{{ $editing ? 'Leave blank to keep current password' : 'Min. 8 characters' }}"
            {{ $editing ? '' : 'required' }}
            style="max-width:320px;">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @if($editing)
            <div class="form-text text-muted">Only fill this in if you want to reset the password.</div>
        @endif
    </div>
</div>

{{-- Status (edit only) --}}
@if($editing)
<h6 class="text-muted fw-semibold mb-3 pb-1 border-bottom mt-4">Account Status</h6>

<div class="row mb-0">
    <label class="col-sm-2 col-form-label">Status</label>
    <div class="col-sm-10 d-flex align-items-center">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch"
                name="is_active" id="is_active" value="1"
                {{ old('is_active', $customer->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
        <small class="text-muted ms-3">Inactive customers cannot log in.</small>
    </div>
</div>
@endif

<script>
(function () {
    const dialCodes = {
        AE: '+971', SA: '+966', KW: '+965',
        BH: '+973', QA: '+974', OM: '+968',
    };
    const sel     = document.getElementById('country');
    const display = document.getElementById('dial-code-display');

    function sync() { display.textContent = dialCodes[sel.value] || '+—'; }
    sel.addEventListener('change', sync);
    sync();
})();
</script>
