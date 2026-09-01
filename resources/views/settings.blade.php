@extends("layout.app")

<style>
    .settings-card {
        background: #fff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 10px 20px;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 2px solid #0d6efd;
        background: transparent;
        font-weight: bold;
    }
    .profile-info-box {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        border: 1px solid #e9ecef;
    }
</style>

@section("content")
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <!-- ALERTS KWA AJILI YA MESSAGES -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="settings-card">
                <h4 class="fw-bold mb-4"><i class="bi bi-person me-2"></i>Account Profile</h4>

                <!-- NAV TABS HEADER -->
                <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="true">
                            <i class="bi bi-person-circle me-1"></i> Profile Information
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane" type="button" role="tab" aria-controls="password-tab-pane" aria-selected="false">
                            <i class="bi bi-shield-lock me-1"></i> Change Password
                        </button>
                    </li>
                </ul>

                <!-- NAV TABS CONTENT -->
                <div class="tab-content" id="settingsTabContent">
                    
                    <!-- TAB 1: PROFILE DETAILS & EDIT -->
                    <div class="tab-pane fade show active" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row mb-4">
                                <div class="col-md-12 mb-3">
                                    <div class="profile-info-box d-flex align-items-center justify-content-between">
                                        <div>
                                            <h5 class="mb-1 text-primary">
                                                {{ Auth::user()->first_name ?? '' }} {{ Auth::user()->last_name ?? '' }}
                                            </h5>
                                            <p class="mb-0 text-muted small">
                                                Role: <span class="badge bg-secondary">{{ ucfirst(Auth::guard('web')->user()->role ?? 'User') }}</span>
                                            </p>
                                        </div>
                                        
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', Auth::guard('web')->user()->first_name ?? '') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', Auth::guard('web')->user()->last_name ?? '') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', Auth::guard('web')->user()->email ?? '') }}" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label font-weight-bold">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', Auth::guard('web')->user()->mobile ?? '') }}">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: CHANGE PASSWORD -->
                    <div class="tab-pane fade" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                        <form method="POST" action="{{ route('password.update1') }}">
                            @csrf
                            @method('PUT')

                            <div class="row justify-content-start">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">Current Password</label>
                                        <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                                        @error('current_password')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold">New Password</label>
                                        <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>
                                        @error('new_password')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold">Confirm New Password</label>
                                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Confirm new password" required>
                                    </div>

                                    <div class="d-flex justify-content-start">
                                        <button type="submit" class="btn btn-danger px-4">
                                            <i class="bi bi-key me-1"></i> Update Password
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection