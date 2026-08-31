@extends("layout.app")

<style>

.flash-message {
    background-color: #d1e7dd;
    border-color: #badbcc;
    color: #0f5132;
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

.voucher-result {
    display: none;
}

.loading {
    display: none;
}

</style>


@section("content")

<div class="content">

<div class="table-container">

    {{-- ============================= --}}
    {{-- VERIFY BUTTON --}}
    {{-- ============================= --}}

    @if(Auth::guard('web')->user()->role == "attendant")

        <button
            class="btn btn-success mb-3"
            data-bs-toggle="modal"
            data-bs-target="#verifyModal">

            <i class="bi bi-check-circle"></i>
            Verify Voucher

        </button>

    @endif


    {{-- ============================= --}}
    {{-- VERIFY MODAL --}}
    {{-- ============================= --}}

    <div
        class="modal fade"
        id="verifyModal"
        tabindex="-1">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                {{-- HEADER --}}
                <div class="modal-header bg-success text-white">

                    <h5 class="modal-title">
                        <i class="bi bi-check-circle"></i>
                        Verify Voucher
                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                {{-- BODY --}}
                <div class="modal-body">

                    {{-- SEARCH FORM --}}
                    <form id="searchVoucherForm">

                        @csrf

                        <div class="row">

                            <div class="col-md-9">

                                <label class="form-label">
                                    Reference Number
                                </label>

                                <input
                                    type="text"
                                    name="reference_number"
                                    id="reference_number"
                                    class="form-control"
                                    placeholder="Enter voucher reference number"
                                    required>

                            </div>


                            <div class="col-md-3 d-flex align-items-end">

                                <button
                                    type="submit"
                                    class="btn btn-primary w-100">

                                    <i class="bi bi-search"></i>
                                    Search

                                </button>

                            </div>

                        </div>

                    </form>


                    {{-- LOADING --}}
                    <div
                        class="loading text-center mt-3"
                        id="loading">

                        <div
                            class="spinner-border text-primary"
                            role="status">
                        </div>

                        <p class="mt-2">
                            Searching voucher...
                        </p>

                    </div>


                    {{-- ERROR MESSAGE --}}
                    <div
                        id="searchError"
                        class="alert alert-danger mt-3"
                        style="display:none;">
                    </div>


                    {{-- ============================= --}}
                    {{-- VOUCHER DETAILS --}}
                    {{-- ============================= --}}

                    <div
                        id="voucherResult"
                        class="voucher-result mt-4">

                        <div class="card shadow">

                            <div class="card-header bg-primary text-white">

                                <h4 class="mb-0">
                                    Voucher Details
                                </h4>

                            </div>


                            <div class="card-body">

                                {{-- CUSTOMER + ORGANIZATION --}}
                                <div class="row mb-3">

                                    <div class="col-md-6">

                                        <strong>
                                            Customer / Driver:
                                        </strong>

                                        <br>

                                        <span id="driverName">
                                            -
                                        </span>

                                    </div>


                                    <div class="col-md-6">

                                        <strong>
                                            Organization:
                                        </strong>

                                        <br>

                                        <span id="organizationName">
                                            -
                                        </span>

                                    </div>

                                </div>


                                {{-- VOUCHER CODE + AMOUNT --}}
                                <div class="row mb-3">

                                    <div class="col-md-6">

                                        <strong>
                                            Voucher Code:
                                        </strong>

                                        <br>

                                        <span id="voucherCode">
                                            -
                                        </span>

                                    </div>


                                    <div class="col-md-6">

                                        <strong>
                                            Amount:
                                        </strong>

                                        <br>

                                        <span id="voucherAmount">
                                            -
                                        </span>

                                        TZS

                                    </div>

                                </div>


                                {{-- FUEL + REFERENCE --}}
                                <div class="row mb-3">

                                    <div class="col-md-6">

                                        <strong>
                                            Fuel Litres:
                                        </strong>

                                        <br>

                                        <span id="fuelLitres">
                                            -
                                        </span>

                                        L

                                    </div>


                                    <div class="col-md-6">

                                        <strong>
                                            Reference Number:
                                        </strong>

                                        <br>

                                        <span id="voucherReference">
                                            -
                                        </span>

                                    </div>

                                </div>


                                {{-- STATUS --}}
                                <div class="mb-3">

                                    <strong>
                                        Status:
                                    </strong>

                                    <br>

                                    <span
                                        id="voucherStatus"
                                        class="badge bg-warning">

                                        PENDING

                                    </span>

                                </div>


                                {{-- QR CODE --}}
                                <div class="text-center mt-4">

                                    <strong>
                                        Voucher QR Code
                                    </strong>

                                    <div
                                        id="voucherQrCode"
                                        class="mt-2">
                                    </div>

                                </div>


                                {{-- HIDDEN REFERENCE --}}
                                <input
                                    type="hidden"
                                    id="verifyReference"
                                    name="reference_number">


                                {{-- VERIFY BUTTON --}}
                                <div
                                    class="text-center mt-4">

                                    <form
                                        action="{{ route('voucher.verify') }}"
                                        method="POST">

                                        @csrf

                                        <input
                                            type="hidden"
                                            name="reference_number"
                                            id="confirmReference">

                                        <button
                                            type="submit"
                                            class="btn btn-success btn-lg">

                                            <i class="bi bi-check-circle"></i>

                                            Confirm & Verify Voucher

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Close

                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================= --}}
    {{-- SUCCESS MESSAGE --}}
    {{-- ============================= --}}

    @if(session('success'))

        <div
            class="alert alert-success alert-dismissible fade show"
            role="alert">

            <i class="bi bi-check-circle-fill"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- ============================= --}}
    {{-- ERROR MESSAGE --}}
    {{-- ============================= --}}

    @if(session('error'))

        <div
            class="alert alert-danger alert-dismissible fade show"
            role="alert">

            <i class="bi bi-exclamation-triangle-fill"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- ============================= --}}
    {{-- TABLE --}}
    {{-- ============================= --}}

    <table class="table table-bordered table-sm align-middle">

        <thead class="table-dark">

            <tr>

                <th>#</th>

                <th>Driver</th>

                <th>Institution Name</th>

                <th>Amount</th>

                <th>Reference</th>

                <th>Date</th>

                @if(Auth::guard('web')->user()->role == "station_manager")

                    <th>Verified By</th>

                @endif

                <th>Status</th>

            </tr>

        </thead>


        <tbody>

            @forelse($vouchers as $index => $v)

                <tr>

                    <td>
                        {{ $index + 1 }}
                    </td>


                    <td>

                        {{ $v->driver->first_name ?? 'N/A' }}

                        {{ $v->driver->last_name ?? 'N/A' }}

                    </td>


                    <td>

                        {{ $v->driver->organization->company_name ?? 'N/A' }}

                    </td>


                    <td>

                        {{ number_format($v->amount) }} TZS

                    </td>


                    <td>

                        {{ $v->reference_number }}

                    </td>


                    <td>

                        {{ $v->created_at->format('j F Y') }}

                    </td>


                    @if(Auth::guard('web')->user()->role == "station_manager")

                        <td>

                            {{ $v->voucher_verify->first_name ?? 'N/A' }}

                            {{ $v->voucher_verify->last_name ?? '' }}

                        </td>

                    @endif


                    <td>

                        @if($v->status == 'pending')

                            <span class="badge bg-warning">
                                Pending
                            </span>

                        @else

                            <span class="badge bg-success">
                                Used
                            </span>

                        @endif

                    </td>

                </tr>


            @empty

                <tr>

                    <td
                        colspan="8"
                        class="text-center">

                        No Voucher Found

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


</div>

</div>


{{-- ============================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================= --}}

<script>

document
    .getElementById('searchVoucherForm')
    .addEventListener('submit', function(e) {

        e.preventDefault();

        let referenceNumber =
            document.getElementById('reference_number').value;

        let loading =
            document.getElementById('loading');

        let error =
            document.getElementById('searchError');

        let result =
            document.getElementById('voucherResult');


        // Hide old messages
        error.style.display = 'none';

        result.style.display = 'none';

        // Show loading
        loading.style.display = 'block';


        fetch("{{ route('voucher.search') }}", {

            method: "POST",

            headers: {

                "Content-Type":
                    "application/json",

                "X-CSRF-TOKEN":
                    document
                        .querySelector('input[name="_token"]')
                        .value,

                "Accept":
                    "application/json"

            },

            body: JSON.stringify({

                reference_number:
                    referenceNumber

            })

        })

        .then(response => {

            return response.json()
                .then(data => ({

                    status: response.status,

                    data: data

                }));

        })

        .then(resultData => {

            loading.style.display = 'none';


            if (!resultData.data.success) {

                error.innerHTML =
                    resultData.data.message;

                error.style.display = 'block';

                return;

            }


            let voucher =
                resultData.data.voucher;


            // ==========================
            // DISPLAY VOUCHER DETAILS
            // ==========================

            document.getElementById(
                'driverName'
            ).innerText =
                voucher.driver;


            document.getElementById(
                'organizationName'
            ).innerText =
                voucher.organization;


            document.getElementById(
                'voucherCode'
            ).innerText =
                voucher.voucher_code;


            document.getElementById(
                'voucherAmount'
            ).innerText =
                voucher.amount;


            document.getElementById(
                'fuelLitres'
            ).innerText =
                voucher.fuel_litres;


            document.getElementById(
                'voucherReference'
            ).innerText =
                voucher.reference_number;


            document.getElementById(
                'confirmReference'
            ).value =
                voucher.reference_number;


            // ==========================
            // STATUS
            // ==========================

            let status =
                document.getElementById(
                    'voucherStatus'
                );

            status.innerText =
                voucher.status;


            status.className =
                'badge bg-warning';


            // ==========================
            // QR CODE
            // ==========================

            document.getElementById(
                'voucherQrCode'
            ).innerHTML =
                voucher.qr_code;


            // Show result
            result.style.display =
                'block';

        })

        .catch(errorData => {

            loading.style.display = 'none';

            error.innerHTML =
                'Something went wrong. Please try again.';

            error.style.display =
                'block';

            console.log(errorData);

        });

    });


/*
|--------------------------------------------------------------------------
| CLEAR MODAL WHEN CLOSED
|--------------------------------------------------------------------------
*/

document
    .getElementById('verifyModal')
    .addEventListener(
        'hidden.bs.modal',
        function() {

            document
                .getElementById('reference_number')
                .value = '';

            document
                .getElementById('voucherResult')
                .style.display = 'none';

            document
                .getElementById('searchError')
                .style.display = 'none';

            document
                .getElementById('loading')
                .style.display = 'none';

        }
    );

</script>


@endsection