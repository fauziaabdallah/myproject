@extends("layout.app")

<style>
    .report-box {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .report-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .report-header h4 { margin-bottom: 2px; font-weight: bold; }
    .report-header small { color: #666; }
    .summary-card {
        background: #d1e7dd;
        border: 1px solid #badbcc;
        border-radius: 10px;
        padding: 15px 20px;
        color: #0f5132;
        font-weight: bold;
        text-align: center;
    }

    /* ==== PRINT ONLY REPORT SECTION ==== */
    @media print {
        body * { visibility: hidden; }
        #printArea, #printArea * { visibility: visible; }
        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print { display: none !important; }
    }
</style>

@section("content")
<div class="content">
    <div class="table-container">

        <form method="GET" action="{{ route('reports.fuel') }}" class="row mb-3">

    <div class="col-md-4">

        <label>Start Date</label>

        <input
            type="date"
            name="start_date"
            class="form-control"
            value="{{ request('start_date') }}">

    </div>

    <div class="col-md-4">

        <label>End Date</label>

        <input
            type="date"
            name="end_date"
            class="form-control"
            value="{{ request('end_date') }}">

    </div>

    <div class="col-md-2 d-flex align-items-end">

        <button class="btn btn-primary w-100">
            Filter
        </button>

    </div>

    <div class="col-md-2 d-flex align-items-end">

        <a href="{{ route('reports.fuel') }}" class="btn btn-secondary w-100">
            Reset
        </a>

    </div>

</form>

        <!-- ============ SEHEMU ITAKAYOPRINT (REPORT) ============ -->
        <div id="printArea" class="report-box">

            <div class="report-header">
                <h4>Fuel Consumption Report</h4>
                <small>
                    @if(request('start_date') && request('end_date'))
                        From: {{ request('start_date') }} To {{ request('end_date') }}
                    @else
                        Data available
                    @endif
                </small>
            </div>

            <table class="table table-bordered table-sm">
    

           

        </div>
        <!-- ============ MWISHO WA SEHEMU ITAKAYOPRINT ============ -->

    </div>
</div>
@endsection