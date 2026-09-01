@extends("layout.app")
<style>
       

        .voucher-card {
            max-width: 750px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .voucher-header {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
            padding: 20px;
            border-radius: 10px;
        }

        .info-box {
            border: 1px solid #eee;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            color: #555;
        }

        .value {
            color: #000;
        }

        .qr-box {
            text-align: center;
            margin-top: 20px;
        }

        .status {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
@section("content")
<div class="content">
<div class="table-container">
  @forelse ($voucher as $v)
  <div class="row" style="display: inline-block;gap: 10px">
    <div class="col-md-4">
      <div class="voucher-card" style="width:400px">

    <!-- HEADER -->
    <div class="voucher-header text-center">
        <h3>FUEL VOUCHER</h3>
        <p class="mb-0">Expired Vouchar</p>
    </div>

    <hr>

    <!-- INFO -->
  <div class="row">




<div class="voucher-card p-4" style="border:1px solid #ddd; border-radius:12px; background:#fff; box-shadow:0 5px 15px rgba(0,0,0,0.08);">

    <!-- HEADER -->
    {{-- <div class="text-center mb-3">
        <h4 style="margin:0;">VOUCHER REFERENCE NUMBER</h4>
        <small class="text-muted">{{ $v->reference_number ?? '' }}
</small>
    </div> --}}

    <hr>

    <!-- ROW 1 -->
     <div class="row mb-2">
        <div class="col-md-6">
            <div class="info-box">
                <b>Driver Name</b><br>
                {{ $v->driver->first_name }}
                {{ $v->driver->last_name }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="info-box">
                <b>Organization</b><br>
                {{ $v->driver->organization->company_name ?? 'N/A' }}
            </div>
        </div>
    </div> 

    <!-- ROW 2 -->
  <div class="row mb-2">
        {{-- <div class="col-md-6">
            <div class="info-box">
                <b>Voucher Code</b><br>
                {{ $voucher->voucher_code }}
            </div>
        </div> --}}

        <div class="col-md-6">
            <div class="info-box">
                <b>Amount Paid</b><br>
                {{ number_format($v->amount) }} TZS
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-box">
                <b>Verify By</b><br>
                {{ $v->voucher_verify->first_name }} {{ $v->voucher_verify->last_name }}
            </div>
        </div>
    </div>

    <!-- ROW 3 -->
    <div class="row mb-2">
        <div class="col-md-6">
            <div class="info-box">
                <b>Fuel Litres</b><br>
                {{ round($v->amount / 3000, 2) }} L
            </div>
        </div>

        <div class="col-md-6">
            <div class="info-box">
                <b>Status</b><br>
            <span class="badge bg-danger">Used</span>
    
            </div>
        </div>
    </div>
     <div class="row mb-2">
        <div class="col-md-6">
            <div class="info-box">
                <b>Station</b><br>
                {{ $v->voucher_verify->station->station_name }}
            </div>
        </div>

        <div class="col-md-6">
            <div class="info-box">
                <b>Location</b><br>
                {{ $v->voucher_verify->station->location }}
            
    
            </div>
        </div>
    </div>

    <!-- ROW 4 -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="info-box">
                <b>Created Date</b><br>
                {{ $v->created_at->format('j F Y') }}
            </div>
        </div>
    </div> 

    <hr>

    <!-- QR CODE BIG -->
    {{-- <div class="text-center">
        <div style="background:#fff; padding:15px; display:inline-block; border-radius:10px;">
            @if($voucher)
    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($voucher->reference_number) !!}
@else
    <p class="text-danger">No voucher generated</p>
@endif
        </div>
        <p class="mt-2 text-muted">Scan QR to verify voucher</p>
    </div> --}}

</div>

</div>



</div>
</div>
</div>
  @empty
 <p class="text-danger text-center">No expired Vouchar</p>
      
  @endforelse
    



<hr>
<p>
    @if(session('error'))
    <span>{{ session('error') }}</span>
    @endif
</p>
<p class="text-center text-muted">
    This voucher is valid for fuel redemption only. Do not share it.
</p>

</div>
</div>
@endsection