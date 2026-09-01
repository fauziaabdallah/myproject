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
    {{-- VERIFY BUTTON --}}

@if(Auth::guard('web')->user()->role == "attendant")

    <button
        class="btn btn-success mb-3"
        data-bs-toggle="modal"
        data-bs-target="#verifyModal">

        <i class="bi bi-check-circle"></i>
        Verify Voucher

    </button>

@endif


{{-- VERIFY MODAL --}}

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


                {{-- ========================================= --}}
                {{-- CHOOSE METHOD --}}
                {{-- ========================================= --}}

                <div class="text-center mb-4">

                    <h6 class="mb-3">
                        Choose Verification Method
                    </h6>

                    <button
                        type="button"
                        class="btn btn-primary me-2"
                        id="referenceButton">

                        <i class="bi bi-search"></i>
                        Search by Reference Number

                    </button><br><br>


                    <button
                        type="button"
                        class="btn btn-dark"
                        id="scanButton">

                        <i class="bi bi-qr-code-scan"></i>
                        Scan QR Code

                    </button>

                </div>


                {{-- ========================================= --}}
                {{-- REFERENCE SEARCH --}}
                {{-- ========================================= --}}

                <div
                    id="referenceSection"
                    style="display:none;">

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
                                    required><br>

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

                </div>


                {{-- ========================================= --}}
                {{-- QR SCANNER --}}
                {{-- ========================================= --}}

                <div
                    id="scannerSection"
                    style="display:none;">

                    <div class="text-center">

                        <h6>
                            Scan Voucher QR Code
                        </h6>

                        <p class="text-muted">
                            Point the camera at the voucher QR Code.
                        </p>

                    </div>


                    <div
                        id="qr-reader"
                        style="width:100%; max-width:500px; margin:auto;">
                    </div>


                    <div
                        id="scanError"
                        class="alert alert-danger mt-3"
                        style="display:none;">
                    </div>

                </div>


                {{-- ========================================= --}}
                {{-- LOADING --}}
                {{-- ========================================= --}}

                <div
                    id="loading"
                    class="text-center mt-3"
                    style="display:none;">

                    <div
                        class="spinner-border text-primary">
                    </div>

                    <p class="mt-2">
                        Searching voucher...
                    </p>

                </div>


                {{-- ========================================= --}}
                {{-- ERROR --}}
                {{-- ========================================= --}}

                <div
                    id="searchError"
                    class="alert alert-danger mt-3"
                    style="display:none;">
                </div>


                {{-- ========================================= --}}
                {{-- VOUCHER RESULT --}}
                {{-- ========================================= --}}

                <div
                    id="voucherResult"
                    class="mt-4"
                    style="display:none;">

                    <div class="card shadow">

                        <div
                            class="card-header bg-primary text-white">

                            <h4 class="mb-0">
                                Voucher Details
                            </h4>

                        </div>


                        <div class="card-body">


                            {{-- DRIVER + ORGANIZATION --}}

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
                                    class="badge bg-success">

                                    Active

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


                            {{-- ================================= --}}
                            {{-- CONFIRM VERIFY --}}
                            {{-- ================================= --}}

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
                                Active
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
<script src="https://unpkg.com/html5-qrcode"></script>
<script>

let qrScanner = null;


/* ==========================================================
   CHOOSE SEARCH BY REFERENCE NUMBER
   ========================================================== */

document
    .getElementById('referenceButton')
    .addEventListener('click', function () {

        document.getElementById('referenceSection').style.display = 'block';

        document.getElementById('scannerSection').style.display = 'none';

        document.getElementById('scanError').style.display = 'none';

        stopScanner();

    });


/* ==========================================================
   CHOOSE SCAN QR CODE
   ========================================================== */

document
    .getElementById('scanButton')
    .addEventListener('click', function () {

        document.getElementById('referenceSection').style.display = 'none';

        document.getElementById('scannerSection').style.display = 'block';

        document.getElementById('searchError').style.display = 'none';

        startScanner();

    });


/* ==========================================================
   START QR SCANNER
   ========================================================== */

function startScanner() {

    if (qrScanner !== null) {
        return;
    }

    qrScanner = new Html5Qrcode("qr-reader");


    const config = {
        fps: 10,

        qrbox: {
            width: 250,
            height: 250
        }
    };


    qrScanner.start(

        {
            facingMode: "environment"
        },

        config,


        function (decodedText, decodedResult) {

            console.log(
                "Scanned Reference Number:",
                decodedText
            );


            /*
            |--------------------------------------------------------------------------
            | QR CODE IMESOMA REFERENCE NUMBER
            |--------------------------------------------------------------------------
            */

            searchVoucher(decodedText);


            /*
            |--------------------------------------------------------------------------
            | STOP CAMERA AFTER SUCCESSFUL SCAN
            |--------------------------------------------------------------------------
            */

            stopScanner();

        },


        function (errorMessage) {

            // Continuous scanning errors are ignored

        }

    )
    .catch(function (error) {

        console.log(error);


        let scanError =
            document.getElementById('scanError');


        scanError.innerHTML =
            "Unable to access camera. Please allow camera permission.";


        scanError.style.display =
            'block';

    });

}


/* ==========================================================
   STOP QR SCANNER
   ========================================================== */

function stopScanner() {

    if (qrScanner !== null) {

        qrScanner
            .stop()
            .then(function () {

                qrScanner.clear();

                qrScanner = null;

            })
            .catch(function () {

                qrScanner = null;

            });

    }

}


/* ==========================================================
   SEARCH VOUCHER
   THIS FUNCTION IS USED BY:
   1. Reference Number Search
   2. QR Code Scan
   ========================================================== */

function searchVoucher(referenceNumber) {

    let loading =
        document.getElementById('loading');


    let error =
        document.getElementById('searchError');


    let result =
        document.getElementById('voucherResult');


    /*
    |--------------------------------------------------------------------------
    | CLEAN OLD DATA
    |--------------------------------------------------------------------------
    */

    error.style.display = 'none';

    result.style.display = 'none';

    loading.style.display = 'block';


    /*
    |--------------------------------------------------------------------------
    | SEND REQUEST TO LARAVEL
    |--------------------------------------------------------------------------
    */

    fetch(
        "{{ route('voucher.search') }}",
        {

            method: "POST",

            headers: {

                "Content-Type": "application/json",

                "X-CSRF-TOKEN":
                    document
                        .querySelector(
                            '#searchVoucherForm input[name="_token"]'
                        )
                        .value,

                "Accept": "application/json"

            },


            body: JSON.stringify({

                reference_number:
                    referenceNumber

            })

        }
    )


    /*
    |--------------------------------------------------------------------------
    | GET RESPONSE
    |--------------------------------------------------------------------------
    */

    .then(function (response) {

        return response.json();

    })


    /*
    |--------------------------------------------------------------------------
    | PROCESS RESPONSE
    |--------------------------------------------------------------------------
    */

    .then(function (data) {

        loading.style.display = 'none';


        /*
        |--------------------------------------------------------------------------
        | IF VOUCHER NOT FOUND OR ALREADY USED
        |--------------------------------------------------------------------------
        */

        if (!data.success) {

            error.innerHTML =
                data.message;

            error.style.display =
                'block';

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | GET VOUCHER DATA
        |--------------------------------------------------------------------------
        */

        let voucher =
            data.voucher;


        /*
        |--------------------------------------------------------------------------
        | DRIVER
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'driverName'
        ).innerText =
            voucher.driver;


        /*
        |--------------------------------------------------------------------------
        | ORGANIZATION
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'organizationName'
        ).innerText =
            voucher.organization;


        /*
        |--------------------------------------------------------------------------
        | VOUCHER CODE
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'voucherCode'
        ).innerText =
            voucher.voucher_code;


        /*
        |--------------------------------------------------------------------------
        | AMOUNT
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'voucherAmount'
        ).innerText =
            voucher.amount;


        /*
        |--------------------------------------------------------------------------
        | FUEL LITRES
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'fuelLitres'
        ).innerText =
            voucher.fuel_litres;


        /*
        |--------------------------------------------------------------------------
        | REFERENCE NUMBER
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'voucherReference'
        ).innerText =
            voucher.reference_number;


        /*
        |--------------------------------------------------------------------------
        | HIDDEN REFERENCE FOR VERIFY
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'confirmReference'
        ).value =
            voucher.reference_number;


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        let status =
            document.getElementById(
                'voucherStatus'
            );


        status.innerText =
            'ACTIVE';


        if (
            voucher.status.toLowerCase()
            === 'pending'
        ) {

            status.className =
                'badge bg-success';

        }
        else {

            status.className =
                'badge bg-success';

        }


        /*
        |--------------------------------------------------------------------------
        | DISPLAY QR CODE
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            'voucherQrCode'
        ).innerHTML =
            voucher.qr_code;


        /*
        |--------------------------------------------------------------------------
        | SHOW VOUCHER RESULT
        |--------------------------------------------------------------------------
        */

        result.style.display =
            'block';

    })


    /*
    |--------------------------------------------------------------------------
    | ERROR
    |--------------------------------------------------------------------------
    */

    .catch(function (errorData) {

        loading.style.display =
            'none';


        console.log(errorData);


        error.innerHTML =
            'Something went wrong. Please try again.';


        error.style.display =
            'block';

    });

}


/* ==========================================================
   SEARCH FORM
   SEARCH BY REFERENCE NUMBER
   ========================================================== */

document
    .getElementById('searchVoucherForm')
    .addEventListener(
        'submit',
        function (e) {

            e.preventDefault();


            let referenceNumber =
                document
                    .getElementById(
                        'reference_number'
                    )
                    .value
                    .trim();


            /*
            |--------------------------------------------------------------------------
            | CHECK EMPTY
            |--------------------------------------------------------------------------
            */

            if (referenceNumber === '') {

                let error =
                    document.getElementById(
                        'searchError'
                    );


                error.innerHTML =
                    'Please enter reference number.';


                error.style.display =
                    'block';


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | SEARCH
            |--------------------------------------------------------------------------
            */

            searchVoucher(
                referenceNumber
            );

        }
    );


/* ==========================================================
   CLEAR MODAL WHEN CLOSED
   ========================================================== */

document
    .getElementById('verifyModal')
    .addEventListener(
        'hidden.bs.modal',
        function () {


            /*
            |--------------------------------------------------------------------------
            | STOP CAMERA
            |--------------------------------------------------------------------------
            */

            stopScanner();


            /*
            |--------------------------------------------------------------------------
            | CLEAR REFERENCE NUMBER
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'reference_number'
                )
                .value = '';


            /*
            |--------------------------------------------------------------------------
            | HIDE RESULT
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'voucherResult'
                )
                .style.display =
                'none';


            /*
            |--------------------------------------------------------------------------
            | HIDE ERRORS
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'searchError'
                )
                .style.display =
                'none';


            document
                .getElementById(
                    'scanError'
                )
                .style.display =
                'none';


            /*
            |--------------------------------------------------------------------------
            | HIDE LOADING
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'loading'
                )
                .style.display =
                'none';


            /*
            |--------------------------------------------------------------------------
            | HIDE SEARCH SECTION
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'referenceSection'
                )
                .style.display =
                'none';


            /*
            |--------------------------------------------------------------------------
            | HIDE SCANNER
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'scannerSection'
                )
                .style.display =
                'none';

        }
    );

</script>


@endsection