@extends('layouts.admin')
@section('title', 'My Profile')
@section('content')
<div class="col-lg-8 mx-auto py-4">
    
    <!-- SECTION 1 — PAGE HEADER -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
            <h1 class="h3 fw-bold text-dark mb-1">My Profile</h1>
            <p class="text-muted mb-0">Manage your account settings</p>
        </div>
        <div class="col-sm-6 text-center text-sm-end">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger px-4" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <!-- SECTION 2 — PROFILE AVATAR CARD -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center flex-column flex-sm-row gap-4">
                <!-- Avatar circle -->
                <div style="width: 80px; height: 80px; background-color: #2563eb; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; flex-shrink: 0;">
                    {{ strtoupper(substr($admin->name, 0, 2)) }}
                </div>
                <!-- Admin info -->
                <div class="text-center text-sm-start">
                    <h4 class="fw-bold mb-1">{{ $admin->name }}</h4>
                    <p class="text-muted small mb-2">{{ $admin->email }}</p>
                    <div class="mb-2">
                        <span class="badge bg-danger me-2">ADMIN</span>
                        @if($admin->is_active)
                            <span class="badge bg-success">Active</span>
                        @endif
                    </div>
                    <div class="text-muted small">
                        Member since {{ $admin->created_at->format('M Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 3 — UPDATE PROFILE INFO CARD -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 border-start border-primary border-4 py-3">
            <h5 class="mb-0 fw-semibold text-primary">
                <i class="fas fa-user-edit me-2"></i>Profile Information
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.profile.updateInfo') }}">
                @csrf
                @method('PATCH')
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold text-dark">Full Name</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $admin->name) }}" 
                               required 
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold text-dark">Email Address</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $admin->email) }}" 
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="phone" class="form-label fw-semibold text-dark">Phone Number (optional)</label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           class="form-control @error('phone') is-invalid @enderror" 
                           value="{{ old('phone', $admin->phone) }}" 
                           placeholder="+252-XX-XXXXXXX">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SECTION 4 — CHANGE PASSWORD CARD -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 border-start border-warning border-4 py-3">
            <h5 class="mb-0 fw-semibold text-warning">
                <i class="fas fa-lock me-2"></i>Change Password
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.profile.updatePassword') }}">
                @csrf
                @method('PATCH')

                <!-- Current Password -->
                <div class="mb-3">
                    <label for="current_password" class="form-label fw-semibold text-dark">Current Password</label>
                    <div class="input-group">
                        <input type="password"
                               id="current_password"
                               name="current_password"
                               class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                               placeholder="Enter current password"
                               required>
                        <button class="btn btn-outline-secondary"
                                type="button"
                                onclick="togglePassword('current_password', 'eye1')">
                            <i class="fas fa-eye" id="eye1"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="mb-3">
                    <label for="new_password" class="form-label fw-semibold text-dark">New Password</label>
                    <div class="input-group">
                        <input type="password"
                               id="new_password"
                               name="password"
                               class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               placeholder="Min. 8 characters"
                               required>
                        <button class="btn btn-outline-secondary"
                                type="button"
                                onclick="togglePassword('new_password', 'eye2')">
                            <i class="fas fa-eye" id="eye2"></i>
                        </button>
                    </div>
                    <!-- Password strength indicator -->
                    <div id="strengthBar" class="mt-1"
                         style="height:4px; border-radius:2px;
                                background:#e2e8f0; overflow:hidden">
                        <div id="strengthFill"
                             style="height:100%; width:0%;
                                    transition:all 0.3s;
                                    background:#dc3545;">
                        </div>
                    </div>
                    <small id="strengthText" class="text-muted"></small>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div class="mb-3">
                    <label for="confirm_password" class="form-label fw-semibold text-dark">Confirm New Password</label>
                    <div class="input-group">
                        <input type="password"
                               id="confirm_password"
                               name="password_confirmation"
                               class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                               placeholder="Repeat new password"
                               required>
                        <button class="btn btn-outline-secondary"
                                type="button"
                                onclick="togglePassword('confirm_password', 'eye3')">
                            <i class="fas fa-eye" id="eye3"></i>
                        </button>
                    </div>
                    <!-- Live match indicator -->
                    <small id="matchText" class="mt-1 d-block"></small>
                    @error('password_confirmation')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Requirements hint -->
                <div class="alert alert-light border mt-3 py-2 px-3" style="font-size:0.8rem">
                    <strong>Password must:</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        <li>Be at least 8 characters long</li>
                        <li>Match the confirmation field</li>
                    </ul>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="fas fa-key me-2"></i>Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- WhatsApp Notification Status Card -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white border-bottom py-3"
           style="border-left: 4px solid #25D366 !important;">
        <h5 class="mb-0 fw-semibold">
          <i class="fab fa-whatsapp me-2"
             style="color:#25D366"></i>
          WhatsApp Notifications
        </h5>
      </div>
      <div class="card-body">

        @if(config('services.whatsapp.enabled'))
          <!-- WhatsApp is enabled -->
          <div class="alert alert-success border-0 mb-3">
            <i class="fas fa-check-circle me-2"></i>
            <strong>WhatsApp notifications are ACTIVE</strong>
            <br>
            <small>Messages will be sent to the business phone at key order stages.</small>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600 text-muted small">
                PROVIDER
              </label>
              <div class="fw-bold text-capitalize">
                {{ config('services.whatsapp.provider', 'callmebot') }}
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600 text-muted small">
                BUSINESS PHONE
              </label>
              <div class="fw-bold">
                +{{ config('services.whatsapp.business_phone', 'Not configured') }}
              </div>
            </div>
          </div>

          <hr>
          <p class="text-muted small mb-3">
            WhatsApp messages are sent at these stages:
          </p>
          <div class="d-flex gap-2 flex-wrap">
            <span class="badge rounded-pill px-3 py-2"
                  style="background:#e8f5e9;
                         color:#2e7d32;
                         font-size:0.85rem">
              <i class="fas fa-box-open me-1"></i>
              Ready for Delivery
            </span>
            <span class="badge rounded-pill px-3 py-2"
                  style="background:#e8f5e9;
                         color:#2e7d32;
                         font-size:0.85rem">
              <i class="fas fa-check-circle me-1"></i>
              Order Delivered
            </span>
          </div>

          <div class="mt-3">
            <a href="{{ route('admin.whatsapp.test') }}"
               target="_blank"
               class="btn btn-outline-success btn-sm">
              <i class="fab fa-whatsapp me-1"></i>
              Send Test Message
            </a>
            <small class="text-muted ms-2">
              Opens in new tab — check for success/error
            </small>
          </div>

        @else
          <!-- WhatsApp is disabled -->
          <div class="alert alert-warning border-0 mb-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>WhatsApp notifications are DISABLED</strong>
          </div>

          <p class="text-muted mb-3">
            To enable WhatsApp notifications, add these environment variables in Railway dashboard:
          </p>

          <div class="bg-dark text-light p-3 rounded font-monospace small mb-3">
            WHATSAPP_ENABLED=true<br>
            WHATSAPP_PROVIDER=callmebot<br>
            WHATSAPP_BUSINESS_PHONE=252XXXXXXXXX<br>
            WHATSAPP_API_KEY=your_api_key_here
          </div>

          <div class="mt-3 p-3 rounded"
               style="background:#f0fdf4;
                      border:1px solid #bbf7d0">
            <p class="fw-bold mb-2" style="color:#15803d">
              <i class="fab fa-whatsapp me-2"></i>
              How to get your FREE CallMeBot API Key:
            </p>
            <ol class="mb-0 small" style="color:#166534">
              <li>Save <strong>+34 644 59 73 46</strong> to your phone contacts as "CallMeBot"</li>
              <li>Send this WhatsApp message to that number:
                  <br><code class="bg-white px-2 py-1 rounded">I allow callmebot to send me messages</code>
              </li>
              <li>You receive an API key — copy it</li>
              <li>Add it to Railway environment variables</li>
            </ol>
          </div>
        @endif

      </div>
    </div>

    <!-- SECTION 5 — DANGER ZONE CARD (LOGOUT) -->
    <div class="card mb-4" style="border: 1px solid #fee2e2; background: #fff5f5;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-sm-row align-items-center gap-3 text-center text-sm-start">
                <div class="text-danger flex-shrink-0">
                    <i class="fas fa-sign-out-alt fa-2x"></i>
                </div>
                <div>
                    <h5 class="text-danger mb-1 fw-bold">Sign Out</h5>
                    <p class="text-muted small mb-0">You will be logged out of the admin panel. All unsaved changes will be lost.</p>
                </div>
                <div class="ms-sm-auto mt-3 mt-sm-0 flex-shrink-0 w-100 w-sm-auto">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="btn btn-danger btn-lg px-5 w-100" 
                                onclick="return confirm('Are you sure you want to logout?')">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    function togglePassword(fieldId, eyeId) {
        const field = document.getElementById(fieldId);
        const eye   = document.getElementById(eyeId);
        if (field.type === 'password') {
            field.type = 'text';
            eye.classList.replace('fa-eye','fa-eye-slash');
        } else {
            field.type = 'password';
            eye.classList.replace('fa-eye-slash','fa-eye');
        }
    }

    // Password strength checker
    document.getElementById('new_password')
      .addEventListener('input', function() {
        const val      = this.value;
        const fill     = document.getElementById('strengthFill');
        const text     = document.getElementById('strengthText');
        let strength   = 0;

        if (val.length >= 8)       strength++;
        if (/[A-Z]/.test(val))     strength++;
        if (/[0-9]/.test(val))     strength++;
        if (/[^A-Za-z0-9]/.test(val)) strength++;

        const levels = [
            { pct: '25%', color: '#dc3545', label: 'Weak' },
            { pct: '50%', color: '#fd7e14', label: 'Fair' },
            { pct: '75%', color: '#ffc107', label: 'Good' },
            { pct: '100%',color: '#28a745', label: 'Strong'},
        ];

        if (val.length === 0) {
            fill.style.width = '0%';
            text.textContent = '';
            return;
        }

        const level = levels[strength - 1] || levels[0];
        fill.style.width           = level.pct;
        fill.style.background      = level.color;
        text.textContent           = level.label;
        text.style.color           = level.color;
    });

    // Password match checker
    function checkMatch() {
        const pw1   = document.getElementById('new_password').value;
        const pw2   = document.getElementById('confirm_password').value;
        const msg   = document.getElementById('matchText');
        if (pw2.length === 0) {
            msg.textContent = '';
            return;
        }
        if (pw1 === pw2) {
            msg.textContent = '✓ Passwords match';
            msg.style.color = '#28a745';
        } else {
            msg.textContent = '✗ Passwords do not match';
            msg.style.color = '#dc3545';
        }
    }

    document.getElementById('confirm_password').addEventListener('input', checkMatch);
    document.getElementById('new_password').addEventListener('input', checkMatch);

    // Auto-dismiss flash messages
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endsection
