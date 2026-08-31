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

        <!-- FILTER FORM -->
        <form method="GET" action="{{ route('reports.payments') }}" class="no-print">
            <div class="row mb-3">
                
                <!-- DROPDOWN YA AINA YA REPORT -->
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Select Report Type</label>
                    <select name="report_type" class="form-select" onchange="this.form.submit()">
                        <option value="payments" {{ request('report_type') == 'payments' ? 'selected' : '' }}>Payments Report</option>
                        <option value="organizations" {{ request('report_type') == 'organizations' ? 'selected' : '' }}>Registered Organizations</option>
                        <option value="stations" {{ request('report_type') == 'stations' ? 'selected' : '' }}>Fuel Stations</option>
                        <option value="driver_fuel" {{ request('report_type') == 'driver_fuel' ? 'selected' : '' }}>Driver Fuel Vouchers</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-eye"></i> View
                    </button>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-success w-100" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
        </form>

        <!-- ============ SEHEMU ITAKAYOPRINT (REPORT) ============ -->
        <div id="printArea" class="report-box">

            <div class="report-header">
                <h4>
                    @if($reportType == 'payments') Payment Report
                    @elseif($reportType == 'organizations') Registered Organizations Report
                    @elseif($reportType == 'stations') Stations Report
                    @elseif($reportType == 'driver_fuel') Driver Fuel Vouchers Report
                    @endif
                </h4>
                <small>
                    @if(request('start_date') && request('end_date'))
                        From: {{ request('start_date') }} To: {{ request('end_date') }}
                    @else
                        All Available Data
                    @endif
                </small>
            </div>

            <!-- 1. TABLE YA PAYMENTS -->
            @if($reportType == 'payments')
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        @if(Auth::guard('web')->user()->role =="admin")
                        <th>Organization</th>
                        @endif
                        <th>Requested By</th>
                        <th>Amount (TZS)</th>
                        <th>Verified By</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $index => $payment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @if(Auth::guard('web')->user()->role =="admin")
                            <td>{{ $payment->request->fuel_request->company_name ?? 'N/A' }}</td>
                            @endif
                            <td>
                                {{ $payment->request->user->first_name ?? '-' }}
                                {{ $payment->request->user->last_name ?? '' }}
                            </td>
                            <td style="text-align:right">
                                {{ number_format($payment->amount_paid) }}
                            </td>
                            <td>
                                {{ $payment->verifier->first_name ?? '-' }}
                                {{ $payment->verifier->last_name ?? '' }}
                            </td>
                            <td>{{ $payment->created_at->format('d-m-Y H:i') }}</td>
                            <td style="text-align:center">
                                @if($payment->status == 'confirmed')
                                    <span class="badge bg-success">{{ ucfirst($payment->status) }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-danger">No data found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="row mt-3">
                <div class="col-md-4 offset-md-8">
                    <div class="summary-card">
                        Total amount (Confirmed): TZS {{ number_format($totalRevenue) }}
                    </div>
                </div>
            </div>

            <!-- 2. TABLE YA ORGANIZATIONS -->
            @elseif($reportType == 'organizations')
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Company Name</th>
                        <th>Registered Date</th>
                        <th>Type</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($organizations as $index => $org)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $org->company_name }}</td>
                            <td>{{ $org->created_at ? $org->created_at->format('d-m-Y H:i') : '-' }}</td>
                            <td><span class="badge bg-info text-dark">{{ strtoupper($org->type) }}</span></td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-danger">No organizations found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- 3. TABLE YA STATIONS -->
            @elseif($reportType == 'stations')
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Station Name</th>
                        <th>Location</th>
                        <th>Organization</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stations as $index => $station)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $station->station_name }}</td>
                            <td>{{ $station->location }}</td>
                            <td>{{ $station->organization->company_name ?? 'N/A' }}</td>
                            <td>{{ $station->created_at ? $station->created_at->format('d-m-Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-danger">No stations found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- 4. TABLE YA DRIVER FUEL VOUCHERS -->
            @elseif($reportType == 'driver_fuel')
            <table class="table table-bordered table-sm">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Vouchar Ref No</th>
                        <th>Driver Name</th>
                        <th>Amount (TZS)</th>
                        <th>Status</th>
                        <th>Verified By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fuelAssignments as $index => $assignment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $assignment->reference_number }}</td>
                            <td>{{ $assignment->driver->first_name ?? '' }} {{ $assignment->driver->last_name ?? '' }}</td>
                            <td style="text-align:right">{{ number_format($assignment->amount, 2) }}</td>
                            <td style="text-align:center">
                                @if($assignment->status == 'approved' || $assignment->status == 'used')
                                    <span class="badge bg-success">{{ ucfirst($assignment->status) }}</span>
                                @elseif($assignment->status == 'expired')
                                    <span class="badge bg-danger">{{ ucfirst($assignment->status) }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ ucfirst($assignment->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $assignment->voucher_verify->first_name ?? '-' }} {{ $assignment->voucher_verify->last_name ?? '' }}</td>
                            <td>{{ $assignment->created_at ? $assignment->created_at->format('d-m-Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-danger">No fuel voucher assignments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="row mt-3">
                <div class="col-md-4 offset-md-8">
                    <div class="summary-card">
                        Total Amount Assigned: TZS {{ number_format($totalRevenue, 2) }}
                    </div>
                </div>
            </div>
            @endif

        </div>
        <!-- ============ MWISHO WA SEHEMU ITAKAYOPRINT ============ -->

    </div>
</div>
@endsection