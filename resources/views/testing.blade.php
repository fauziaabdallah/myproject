@extends("layout.app")
<style>
   
    .flash-message {
    background-color: #d1e7dd; /* Light green background */
    border-color: #badbcc; /* Darker green border */
    color: #0f5132; /* Dark green text */
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    animation: fadeIn 0.5s ease-in-out;
}

.flash-message .alert-heading {
    color: #0f5132;
    font-weight: bold;
}

.flash-message .btn-close {
    color: #0f5132;
    opacity: 0.8;
}

.flash-message .bi-check-circle-fill {
    font-size: 1.5rem;
    color: #28a745; 
}
</style>
@section("content")

<div class="content">
    
<div class="table-container">
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUser">
    + Add User
</button>

<!-- ADD MODAL -->
<div class="modal fade" id="addUser">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5>Add User</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                        </div>

                        <div class="col-md-6 mb-2">
                            <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="text" name="mobile" class="form-control" placeholder="Mobile" required>
                        </div>

                        <div class="col-md-6 mb-2">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <input type="password" name="password" class="form-control" placeholder="Password" value="12345" required>
                        </div>

                        <div class="col-md-6 mb-2">
                            <select name="role" class="form-control">
                                @if(Auth::guard('web')->user()->role=="accountant")
                                <option value="">Select Role</option>
                                <option value="driver">Driver</option>

                                @else
                                <option value="">Select Role</option>
                                <option value="manager">Manager</option>
                                <option value="station_manager">Fuel station Manager</option>
                                <option value="attendant">Fuel attendant</option>
                                @endif
                            </select>
                        </div>
                    </div>
                @if(Auth::guard('web')->user()->role=="accountant")
                <input type="hidden" name="organization_id" value="{{ Auth::guard('web')->user()->organization_id }}">
                @else
                  <div class="row">
                    <div class="col-md-6 mb-2">
                        <select name="organization_id" class="form-control">
                            <option value="">Select Organization</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org->id }}">
                                    {{ $org->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <select name="station_id" class="form-control">
                            <option value="">Select station</option>
                            @foreach($stations as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->station_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    @endif

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>
<div class="alert alert-dismissible fade show flash-message" role="alert">
  <div class="d-flex align-items-center">
    <i class="bi bi-check-circle-fill me-2"></i> <div class="flex-grow-1">
      <h6 class="alert-heading mb-1">Staff Information</h6>
      <p class="mb-0" style="color: green">{{ session('success') }}</p>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
</div>
 <div class="table-responsive">
    <table class="table table-bordered table-hover table-sm align-middle">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Role</th>
            
                <th width="100">Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    <td>
                        {{ $user->first_name }} {{ $user->last_name }}
                    </td>


                    <td>{{ $user->email }}</td>
                    <td>{{ $user->mobile }}</td>


                    <td>
                        <span class="badge bg-info text-dark">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>

                  

                    <td>
                        <!-- EDIT BUTTON -->
                        <button type="button"
                            class="btn btn-primary btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#edit{{ $user->id }}">

                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </td>
                </tr>

                <!-- EDIT MODAL -->
                <div class="modal fade"
                    id="edit{{ $user->id }}"
                    tabindex="-1"
                    aria-hidden="true">

                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">

                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">
                                    Edit User
                                </h5>

                                <button type="button"
                                    class="btn-close btn-close-white"
                                    data-bs-dismiss="modal">
                                </button>
                            </div>

                            <form action="{{ route('users.update', $user->id) }}"
                                method="POST">

                                @csrf
                                @method('PUT')

                                <div class="modal-body">

                                    <div class="row">

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                First Name
                                            </label>

                                            <input type="text"
                                                name="first_name"
                                                value="{{ $user->first_name }}"
                                                class="form-control"
                                                required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                Last Name
                                            </label>

                                            <input type="text"
                                                name="last_name"
                                                value="{{ $user->last_name }}"
                                                class="form-control"
                                                required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                Mobile
                                            </label>

                                            <input type="text"
                                                name="mobile"
                                                value="{{ $user->mobile }}"
                                                class="form-control">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                Email
                                            </label>

                                            <input type="email"
                                                name="email"
                                                value="{{ $user->email }}"
                                                class="form-control">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                Password
                                            </label>

                                            <input type="password"
                                                name="password"
                                                class="form-control"
                                                placeholder="Leave blank if no change">
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">
                                                Role
                                            </label>

                                            <select name="role"
                                                class="form-select"
                                                required>

                                                <option value="admin"
                                                    {{ $user->role == 'admin' ? 'selected' : '' }}>
                                                    Admin
                                                </option>

                                               

                                                <option value="driver"
                                                    {{ $user->role == 'driver' ? 'selected' : '' }}>
                                                    Driver
                                                </option>
                                            </select>
                                        </div>

                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">
                                                Organization
                                            </label>

                                            <select name="organization_id"
                                                class="form-select">

                                                <option value="">
                                                    Select Organization
                                                </option>

                                                @foreach($organizations as $org)
                                                    <option value="{{ $org->id }}"
                                                        {{ $user->organization_id == $org->id ? 'selected' : '' }}>

                                                        {{ $org->company_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button"
                                        class="btn btn-secondary"
                                        data-bs-dismiss="modal">

                                        Close
                                    </button>

                                    <button type="submit"
                                        class="btn btn-success">

                                        Update User
                                    </button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            @empty
                <tr>
                    <td colspan="7" class="text-center text-danger">
                        No Users Found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
</div>
</div>
@endsection