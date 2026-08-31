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

<!-- Button -->
@if(Auth::guard('web')->user()->role == "accountant")
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addRequest">
    + Add Request
</button>
@endif

<!-- ADD MODAL -->
<div class="modal fade" id="addRequest">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5>Add Request</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <table class="table table-bordered mt-3" style="width: 400px;margin-left: 10px">
                <tr>
                    <td>1 liter</td>
                    <td>3000 Tzs</td>
                    <td id="amount1"></td>
                </tr>
            </table>

            <form action="{{ route('userRequest.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label>Number of liter</label>
                            <input type="number" id="litre" name="number_of_litre" class="form-control" placeholder="Enter litres" required>
                        </div>

                        <div class="col-md-12 mb-2">
                            <label>Amount</label>
                            <input type="text" id="amount" name="request_amount" class="form-control" placeholder="Amount" readonly>
                        </div>
                        <div class="col-md-12 mb-2">
                            <label>Fuel Company</label>
                            <select name="organization_id" class="form-select">
                                <option value="">Select fuel company</option>
                                @foreach ($organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->company_name }}</option>
                                    
                                @endforeach
                            </select>
                        </div>
                    </div>

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
      <h6 class="alert-heading mb-1">All request Informations</h6>
      <p class="mb-0" style="color: green">{{ session('success') }}</p>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
</div>

<!-- TABLE -->
<table class="table table-bordered table-sm align-middle">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            @if(Auth::guard('web')->user()->role == "accountant")
             <th>Requested To</th>
            @endif
            @if(Auth::guard('web')->user()->role != "accountant")
            <th>Organization</th>
            @endif
            <th>Amount</th>
            <th>Litre(s)</th>
            <th>Requested Date</th>
             @if(Auth::guard('web')->user()->role == "admin")
             <th>Requested To</th>
            @endif
            <th>Request Status</th>
            <th>Payment Status</th>
            
             @if(Auth::guard('web')->user()->role == "subadmin" || Auth::guard('web')->user()->role == "accountant")
                <th>Action</th>
                @endif
            
        </tr>
    </thead>

    <tbody>

        @forelse($requests as $index => $req)

        <tr>
            

            <td>{{ $index + 1 }}</td>
             @if(Auth::guard('web')->user()->role == "accountant")
             <td>{{ $req->fuel_request->company_name }}</td>
            @endif
            @if(Auth::guard('web')->user()->role != "accountant")

            <td>
                {{ $req->user->organization->company_name ?? 'N/A' }}
            </td>
            @endif

            <td class="text-end">
                {{ number_format($req->request_amount) }}
            </td>

            <td class="text-end">
                {{ number_format($req->number_of_litre) }}
            </td>

            <td>
                {{ date('d M Y H:i', strtotime($req->created_at)) }}
            </td>
             @if(Auth::guard('web')->user()->role == "admin")
             <td>{{ $req->fuel_request->company_name }}</td>
            @endif

            <td class="text-center">
                @if($req->status == 'pending')
                    <span class="badge bg-warning">Pending</span>
                @elseif($req->status == 'approved')
                    <span class="badge bg-success">Approved</span>
                @else
                    <span class="badge bg-danger">Rejected</span>
                @endif
            </td>

            <td class="text-center">
                @if(
                    Auth::guard('web')->user()->role == "accountant"
                    && $req->status == 'approved'
                )

                    

                @endif
                
                @if($req->payment)

                    @if($req->payment->status == 'pending')
                        <span class="badge bg-danger">
                            Not paid
                        </span>
                    @else
                        <span class="badge bg-success">
                            Paid
                        </span>
                    @endif

                @else

                    <span class="badge bg-danger">
                        Not Paid
                    </span>

                @endif
            </td>
            @if(Auth::guard('web')->user()->role == "accountant")

            <td class="text-center">

               @if($req->status == "pending")

    <button class="btn btn-success btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#payment{{ $req->id }}" disabled>
        Pay Now
    </button>

@elseif(optional($req->payment)->status == "confirmed")

    <button class="btn btn-success btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#payment{{ $req->id }}" disabled>
        Pay Now
    </button>

@else

    <button class="btn btn-success btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#payment{{ $req->id }}">
        Pay Now
    </button>

@endif
            </td>
            @endif
            @if(Auth::guard('web')->user()->role == "subadmin")
<td class="text-center">

    {{-- APPROVE / DISAPPROVE REQUEST --}}
    <button class="btn btn-sm
        {{ $req->status == 'approved' ? 'btn-warning' : 'btn-success' }}"
        data-bs-toggle="modal"
        data-bs-target="#toggleStatus{{ $req->id }}">

        @if($req->status == 'approved')
            <i class="bi bi-arrow-counterclockwise"></i>
        @else
            <i class="bi bi-check-circle"></i>
        @endif

    </button>
@endif

            @if(Auth::guard('web')->user()->role == "subadmin")

                @if($req->payment && $req->payment->status == 'pending')

                    <button class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#verify{{ $req->payment->id }}">
                        Verify
                    </button>

                @endif

            </td>
            @endif

        </tr>
        <!-- Verify Payment Modal -->
           @if($req->payment)
<div class="modal fade" id="verify{{ $req->payment->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Verify Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
          
            <form action="{{ route('payments.verify', $req->payment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Mobile Number</label>
                        <input type="text"
                               name="referrence_number"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Amount Paid</label>
                        <input type="number"
                               name="amount_paid"
                               class="form-control"
                               value="{{ $req->request_amount }}"
                               required readonly>
                    </div>

                    <div class="alert alert-warning">
                        <strong>Confirmation</strong><br>
                        Are you sure you want to verify this payment?
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-success">
                        Confirm Verification
                    </button>
                </div>

            </form>
            @endif

        </div>
    </div>
</div>
         <div class="modal fade" id="payment{{ $req->id }}" tabindex="-1">

            <div class="modal-dialog">

                <div class="modal-content">

                    <form action="{{ route('payments.store') }}" method="POST">

                        @csrf

                        <div class="modal-header bg-success text-white">

                            <h5 class="modal-title">
                                Make Payment
                            </h5>

                            <button type="button"
                                class="btn-close btn-close-white"
                                data-bs-dismiss="modal">
                            </button>

                        </div>

                        <div class="modal-body">

                            <input type="hidden"
                                name="request_id"
                                value="{{ $req->id }}">

                            <div class="mb-3">

                                <label class="form-label">
                                    Amount to be Paid
                                </label>

                                <input type="text"
                                    name="amount_paid"
                                    class="form-control"
                                    value="{{ $req->request_amount }}"
                                    required readonly>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Mobile Number
                                </label>

                                <input type="number"
                                    name="referrence_number"
                                    class="form-control"
                                    required>

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

                                Submit Payment

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <!-- TOGGLE STATUS MODAL -->
        <div class="modal fade"
            id="toggleStatus{{ $req->id }}"
            tabindex="-1">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Confirm Action
                        </h5>

                        <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        @if($req->status == 'approved')

                            Are you sure you want to disapprove this request?

                        @else

                            Are you sure you want to approve this request?

                        @endif

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            Cancel

                        </button>

                        <form method="POST"
                            action="{{ route('request.toggleStatus', $req->id) }}">

                            @csrf
                            @method('PUT')

                            <button type="submit"
                                class="btn
                                {{ $req->status == 'approved'
                                    ? 'btn-warning'
                                    : 'btn-success' }}">

                                Yes Confirm

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

        <!-- EDIT MODAL -->
        <div class="modal fade"
            id="edit{{ $req->id }}"
            tabindex="-1">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">

                        <h5 class="modal-title">
                            Edit Request
                        </h5>

                        <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal">
                        </button>

                    </div>

                    <form action="{{ route('userRequest.update', $req->id) }}"
                        method="POST">

                        @csrf
                        @method('PUT')

                        <div class="modal-body">

                            <div class="mb-3">

                                <label class="form-label">
                                    Request Amount
                                </label>

                                <input type="number"
                                    name="request_amount"
                                    value="{{ $req->request_amount }}"
                                    class="form-control">

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Status
                                </label>

                                <select name="status"
                                    class="form-select">

                                    <option value="pending"
                                        {{ $req->status == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>

                                    <option value="approved"
                                        {{ $req->status == 'approved' ? 'selected' : '' }}>
                                        Approved
                                    </option>

                                    <option value="rejected"
                                        {{ $req->status == 'rejected' ? 'selected' : '' }}>
                                        Rejected
                                    </option>

                                </select>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                                Close

                            </button>

                            <button type="submit"
                                class="btn btn-primary">

                                Update Request

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>
        @empty
<tr>
    <td colspan="10" class="text-center">
        No Requests Found
    </td>
</tr>
@endforelse

        

    </tbody>
</table>



</div>
</div>
<script>
    const litreInput = document.getElementById('litre');
    const amountInput = document.getElementById('amount');
    const amount1 = document.getElementById('amount1');

    litreInput.addEventListener('input', function () {
        let litre = parseFloat(this.value);

        if (!isNaN(litre)) {
            let amount = litre * 3000;
            amountInput.value = amount;
            amount1.value=amount;
        } else {
            amountInput.value = '';
        }
    });
</script>
@endsection