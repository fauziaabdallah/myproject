
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

    .report-header h4 {
        margin-bottom: 2px;
        font-weight: bold;
    }

    .report-header small {
        color: #666;
    }

    .summary-card {
        background: #d1e7dd;
        border: 1px solid #badbcc;
        border-radius: 10px;
        padding: 15px 20px;
        color: #0f5132;
        font-weight: bold;
        text-align: center;
    }

    .table th {
        background: #f1f1f1;
        text-align: center;
    }

    .table td {
        vertical-align: middle;
        text-align: center;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #printArea,
        #printArea * {
            visibility: visible;
        }

        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print {
            display: none !important;
        }
    }
</style>

@section("content")

<div class="content">

    <div class="table-container">

        <!-- ================= FILTER ================= -->

        <form method="GET"
              action="{{ route('reports.fuel') }}"
              class="row mb-3 no-print">

            <div class="col-md-4">

                <label>Start Date</label>

                <input
                    type="date"
                    name="start_date"
                    class="form-control"
                    value="{{ $startDate }}">

            </div>


            <div class="col-md-4">

                <label>End Date</label>

                <input
                    type="date"
                    name="end_date"
                    class="form-control"
                    value="{{ $endDate }}">

            </div>


            <div class="col-md-2 d-flex align-items-end">

                <button type="submit"
                        class="btn btn-primary w-100">

                    Filter

                </button>

            </div>


            <div class="col-md-2 d-flex align-items-end">

                <a href="{{ route('reports.fuel') }}"
                   class="btn btn-secondary w-100">

                    Reset

                </a>

            </div>

        </form>

    </div>


    <!-- ================= REPORT ================= -->

    <div id="printArea" class="report-box">

        <div class="report-header">

            <h4>Station Report</h4>

            <small>

                @if($startDate && $endDate)

                    From:
                    {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}

                    To:
                    {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}

                @elseif($startDate)

                    From:
                    {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}

                @elseif($endDate)

                    Up To:
                    {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}

                @else

                    All Available Data

                @endif

            </small>

        </div>


        <!-- ================= STATION TABLE ================= -->

        <div class="table-responsive">

            <table class="table table-bordered table-sm">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Station Name</th>

                        <th>Location</th>

                        <th>Organization</th>

                        <th>Created Date</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($stations as $station)

                        <tr>

                            <!-- NUMBER -->

                            <td>
                                {{ $loop->iteration }}
                            </td>


                            <!-- STATION NAME -->

                            <td>
                                {{ $station->station_name
                                    ?? $station->name
                                    ?? 'N/A' }}
                            </td>


                            <!-- LOCATION -->

                            <td>
                                {{ $station->location
                                    ?? 'N/A' }}
                            </td>


                            <!-- ORGANIZATION -->

                            <td>
                                {{ $station->organization->company_name
                                    ?? $station->organization->name
                                    ?? 'N/A' }}
                            </td>


                            <!-- CREATED DATE -->

                            <td>

                                @if($station->created_at)

                                    {{ $station->created_at->format('d-m-Y H:i') }}

                                @else

                                    N/A

                                @endif

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="5"
                                class="text-center">

                                No stations found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>



@endsection

