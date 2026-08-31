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
    <input
        type="text"
        name="mobile"
        class="form-control"
        maxlength="10"
        minlength="10"
        pattern="[0-9]{10}"
        placeholder="Mobile"
        required
    >

    @error('mobile')
        <span class="text-danger">{{ $message }}</span>
    @enderror
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
                            <select name="role" id="role" class="form-control">
                                @if(Auth::guard('web')->user()->role=="manager")
                                <option value="">Select Role</option>
                                <option value="accountant">Accountant</option>
                                <option value="driver">Driver</option>

                                @else
                                <option value="">Select Role</option>
                                <option value="subadmin">Sub Admin</option>
                                <option value="accountant">Accountant</option>
                                <!-- <option value="station_manager">Fuel station Manager</option>
                                <option value="attendant">Fuel attendant</option> -->
                                @endif
                            </select>
                        </div>
                    </div>
                @if(Auth::guard('web')->user()->role=="manager")
                <input type="hidden" name="organization_id" value="{{ Auth::guard('web')->user()->organization_id }}">
                @else
                  <div class="row">
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

    <div class="col-md-6 mb-2" id="stationDiv">
        <select name="station_id" class="form-control">
            <option value="">Select Station</option>
            @foreach($stations as $st)
                <option value="{{ $st->id }}">
                    {{ $st->station_name }}
                </option>
            @endforeach
        </select>
    </div>
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

<!-- SUCCESS MESSAGE -->
<div class="alert alert-dismissible fade show flash-message" role="alert">
  <div class="d-flex align-items-center">
    <i class="bi bi-check-circle-fill me-2"></i> <div class="flex-grow-1">
      <h6 class="alert-heading mb-1">Staff Information</h6>
      <p class="mb-0" style="color: green">{{ session('success') }}</p>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
</div>

<!-- TABLE -->
<!-- FILTER FORM -->

<form method="GET" action="{{ route('users.index') }}">

    <div class="row mb-3">

        <!-- ROLE -->
        <div class="col-md-3">

            <label class="form-label">
                Filter By Role
            </label>

            <select name="role" class="form-select">

                <option value="">
                    -- Select Role --
                </option>
                 <option value="subadmin"
                    {{ request('role') == 'subadmin' ? 'selected' : '' }}>
                    Subadmin
                </option>
                <option value="station_manager"
                    {{ request('role') == 'station_manager' ? 'selected' : '' }}>
                    Station Manager
                </option>
                <option value="attendant"
                    {{ request('role') == 'attendant' ? 'selected' : '' }}>
                    Fuel Attendant
                </option>

                <!-- <option value="manager"
                    {{ request('role') == 'manager' ? 'selected' : '' }}>
                    Organization Manager
                </option> -->

                <option value="accountant"
                    {{ request('role') == 'accountant' ? 'selected' : '' }}>
                    Accountant
                </option>

                <option value="driver"
                    {{ request('role') == 'driver' ? 'selected' : '' }}>
                    Driver
                </option>

            </select>

        </div>

        <!-- ORGANIZATION -->
        <div class="col-md-4">

            <label class="form-label">
                Filter By Organization
            </label>

            <select name="organization_id" class="form-select">

                <option value="">
                    -- Select Organization --
                </option>

                @foreach($organizations as $org)

                    <option value="{{ $org->id }}"
                        {{ request('organization_id') == $org->id ? 'selected' : '' }}>

                        {{ $org->company_name }}

                    </option>

                @endforeach

            </select>

        </div>

        <!-- STATION -->
        <div class="col-md-3">

            <label class="form-label">
                Filter By Station
            </label>

            <select name="station_id" class="form-select">

                <option value="">
                    -- Select Station --
                </option>

                @foreach($stations as $station)

                    <option value="{{ $station->id }}"
                        {{ request('station_id') == $station->id ? 'selected' : '' }}>

                        {{ $station->station_name }}

                    </option>

                @endforeach

            </select>

        </div>

        <!-- BUTTON -->
        <div class="col-md-2 d-flex align-items-end">

            <button class="btn btn-primary w-100">

                <i class="bi bi-search"></i>

                Filter

            </button>

        </div>

    </div>

</form>

<table class="table table-bordered table-sm">

    <thead class="table-dark">

        <tr>

            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Organization</th>
            <th>Role</th>
            <th>User Status</th>
            <th>Station</th>
            <th>Action</th>

        </tr>

    </thead>

    <tbody>

        @forelse($users as $index => $user)

        <tr>

            <td>{{ $index + 1 }}</td>

            <td>
                {{ $user->first_name }}
                {{ $user->last_name }}
            </td>


            <td style="text-align: center">{{ $user->email }}</td>
            <td style="text-align: center">{{ $user->mobile }}</td>


            

            <td style="text-align: center">
                {{ $user->organization->company_name ?? 'N/A' }}
            </td>
            <td style="text-align: center">
                <span class="badge bg-primary">
                    {{ ucfirst($user->role) }}
                </span>
            </td>
            <td style="text-align: center">
                @if ($user->status == "Active")
                    <span class="badge bg-success">{{ $user->status }}</span>
                @else
                    <span class="badge bg-danger">{{ $user->status }}</span>

                @endif
                
            </td>

            <td style="text-align: center">
                {{ $user->station->station_name ?? '-' }}
            </td>

            <td>

    <button class="btn btn-primary btn-sm"
        data-bs-toggle="modal"
        data-bs-target="#edit{{ $user->id }}">
        <i class="bi bi-pencil-square"></i>
    </button>

    @if($user->status == 'active')
        <a href="{{ route('users.toggle-status',$user->id) }}"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Block this user?')">

            <i class="bi bi-slash-circle"></i>
        </a>
    @else
        <a href="{{ route('users.toggle-status',$user->id) }}"
           class="btn btn-success btn-sm"
           onclick="return confirm('Block/Unblock this user?')">

            <i class="bi bi-check-circle"></i>
        </a>
    @endif

</td>
<div class="modal fade" id="edit{{ $user->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="{{ route('users.update',$user->id) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">
                        Edit User
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">

                    <div class="mb-2">
                        <label>First Name</label>
                        <input type="text"
                               name="first_name"
                               class="form-control"
                               value="{{ $user->first_name }}">
                    </div>

                    <div class="mb-2">
                        <label>Last Name</label>
                        <input type="text"
                               name="last_name"
                               class="form-control"
                               value="{{ $user->last_name }}">
                    </div>

                    <div class="mb-2">
                        <label>Email</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ $user->email }}">
                    </div>

                    <div class="mb-2">
                        <label>Phone</label>
                        <input type="text"
                               name="mobile"
                               class="form-control"
                               maxlength="10"
                                minlength="10"
                                pattern="[0-9]{10}"
                               value="{{ $user->mobile }}">
                    </div>

                    <div class="mb-2">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="user" {{ $user->role=='manager'?'selected':'' }}>Organization Manager</option>
                            <option value="subadmin" {{ $user->role=='subadmin'?'selected':'' }}>Sub Admin</option>
                            <option value="station_manager" {{ $user->role=='station_manager'?'selected':'' }}>Station Manager</option>
                            <option value="accountant" {{ $user->role=='accountant'?'selected':'' }}>Accountant</option>
                            <option value="admin" {{ $user->role=='admin'?'selected':'' }}>Admin</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">
                        Update User
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
        </tr>

        @empty

        <tr>

            <td colspan="8" class="text-center text-danger">

                Please filter data first

            </td>

        </tr>

        @endforelse

    </tbody>

</table>

</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const role = document.getElementById('role');
    const stationDiv = document.getElementById('stationDiv');

    function toggleStation() {

        if (role.value === 'subadmin' || role.value === 'accountant') {
            stationDiv.style.display = 'none';
            stationDiv.querySelector('select').value = '';
        } else {
            stationDiv.style.display = 'block';
        }
    }

    toggleStation(); // wakati page inafunguka

    role.addEventListener('change', toggleStation);

});
</script>
@endsection