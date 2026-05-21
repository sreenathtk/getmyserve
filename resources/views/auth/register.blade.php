<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Register | GetMyServe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 32px 16px;
        }
        .register-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            padding: 40px;
            width: 100%;
            max-width: 520px;
        }
        .logo { font-size: 1.6rem; font-weight: 800; color: #2a3042; margin-bottom: 4px; }
        .logo span { color: #5664d2; }
        .subtitle { color: #74788d; font-size: 14px; margin-bottom: 28px; }
        .form-label { font-size: 13px; font-weight: 500; color: #495057; margin-bottom: 4px; }
        .form-control, .form-select { font-size: 14px; }
        .form-control:focus, .form-select:focus {
            border-color: #5664d2;
            box-shadow: 0 0 0 0.15rem rgba(86,100,210,.25);
        }
        .btn-primary { background: #5664d2; border-color: #5664d2; font-size: 15px; }
        .btn-primary:hover { background: #4a56b8; border-color: #4a56b8; }
        .section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #5664d2;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid #eef0f7;
        }
        .dial-code-badge {
            background: #f0f2ff;
            border: 1px solid #dee2e6;
            border-right: 0;
            color: #5664d2;
            font-weight: 600;
            font-size: 13px;
            min-width: 64px;
            text-align: center;
        }
        .password-toggle {
            cursor: pointer;
            border-left: 0;
            background: #fff;
            color: #74788d;
        }
        .password-toggle:hover { color: #5664d2; }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="text-center mb-4">
            <div class="logo">Get<span>My</span>Serve</div>
            <p class="subtitle">Create your account — it's free</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger py-2 mb-4" style="font-size:13px;">
            @foreach ($errors->all() as $error)
                <div><i class="ri-error-warning-line me-1"></i>{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            {{-- Personal Details --}}
            <p class="section-label">Personal Details</p>

            <div class="mb-3">
                <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                    id="name" name="name" placeholder="e.g. Ahmed Al Mansoori"
                    value="{{ old('name') }}" required autofocus>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                    id="email" name="email" placeholder="you@example.com"
                    value="{{ old('email') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="phone">Phone Number <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text dial-code-badge" id="dial-code-display">+971</span>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                        id="phone" name="phone"
                        placeholder="50 123 4567"
                        value="{{ old('phone') }}" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-text text-muted" style="font-size:12px;">Enter number without the country code prefix.</div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="whatsapp_number">
                    <i class="ri-whatsapp-line" style="color:#25d366;"></i> WhatsApp Number
                </label>
                <div class="input-group">
                    <span class="input-group-text dial-code-badge" id="wa-dial-code-display">+971</span>
                    <input type="tel" class="form-control @error('whatsapp_number') is-invalid @enderror"
                        id="whatsapp_number" name="whatsapp_number"
                        placeholder="50 123 4567"
                        value="{{ old('whatsapp_number') }}">
                    @error('whatsapp_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="wa_same_as_phone" checked>
                    <label class="form-check-label" for="wa_same_as_phone" style="font-size:12px;color:#74788d;">
                        Same as phone number
                    </label>
                </div>
            </div>

            {{-- Location --}}
            <p class="section-label mt-4">Location</p>

            <div class="mb-3">
                <label class="form-label" for="country">Country <span class="text-danger">*</span></label>
                <select class="form-select @error('country') is-invalid @enderror"
                    id="country" name="country" required>
                    <option value="">— Select Country —</option>
                    @foreach($countries as $code => $name)
                        <option value="{{ $code }}"
                            {{ old('country', 'AE') === $code ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="city">City <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('city') is-invalid @enderror"
                    id="city" name="city" placeholder="e.g. Dubai"
                    value="{{ old('city') }}" required>
                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="address">Address <span class="text-danger">*</span></label>
                <textarea class="form-control @error('address') is-invalid @enderror"
                    id="address" name="address" rows="2"
                    placeholder="Villa / flat number, building name, street, area"
                    required>{{ old('address') }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Security --}}
            <p class="section-label mt-4">Security</p>

            <div class="mb-3">
                <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" name="password" placeholder="Min. 8 characters" required>
                    <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1"
                        onclick="togglePassword('password', this)">
                        <i class="ri-eye-line"></i>
                    </button>
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" class="form-control"
                        id="password_confirmation" name="password_confirmation"
                        placeholder="Re-enter password" required>
                    <button type="button" class="btn btn-outline-secondary password-toggle" tabindex="-1"
                        onclick="togglePassword('password_confirmation', this)">
                        <i class="ri-eye-line"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="ri-user-add-line me-1"></i> Create Account
            </button>
        </form>

        <div class="text-center mt-4">
            <p style="font-size:13px;color:#74788d;">
                Already have an account?
                <a href="{{ route('login') }}" style="color:#5664d2;font-weight:500;">Sign in</a>
            </p>
        </div>
    </div>

    <script>
    // Country → dial code map
    const dialCodes = {
        AE: '+971', SA: '+966', KW: '+965',
        BH: '+973', QA: '+974', OM: '+968',
    };

    const countrySelect    = document.getElementById('country');
    const dialDisplay      = document.getElementById('dial-code-display');
    const waDialDisplay    = document.getElementById('wa-dial-code-display');
    const phoneInput       = document.getElementById('phone');
    const waInput          = document.getElementById('whatsapp_number');
    const waSameCheckbox   = document.getElementById('wa_same_as_phone');

    function updateDialCode() {
        const code = dialCodes[countrySelect.value] || '+—';
        dialDisplay.textContent   = code;
        waDialDisplay.textContent = code;
    }

    function syncWhatsApp() {
        if (waSameCheckbox.checked) {
            waInput.value    = phoneInput.value;
            waInput.readOnly = true;
            waInput.style.background = '#f8f9fa';
        } else {
            waInput.readOnly = false;
            waInput.style.background = '';
        }
    }

    countrySelect.addEventListener('change', updateDialCode);
    phoneInput.addEventListener('input', syncWhatsApp);
    waSameCheckbox.addEventListener('change', syncWhatsApp);
    updateDialCode();
    syncWhatsApp(); // apply on page load

    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ri-eye-off-line';
        } else {
            input.type = 'password';
            icon.className = 'ri-eye-line';
        }
    }
    </script>
</body>
</html>
