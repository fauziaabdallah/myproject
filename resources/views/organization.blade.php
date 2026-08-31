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

<!-- BUTTON -->
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
    + Add Company
</button>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5>Add Company</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('gapcos.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="">Organization Name</label>
                            <input type="text" name="company_name" class="form-control" placeholder="Company Name" required>
                        </div>

                        <div class="col-md-6 mb-2">
    <label for="type">Organization Type</label>

    <select name="type" id="type" class="form-control" required>
        <option value="">-- Select Organization Type --</option>
        <option value="	FUEL COMPANY">	FUEL COMPANY</option>
        <option value="other">Other</option>
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

<!-- SUCCESS -->
<div class="alert alert-dismissible fade show flash-message" role="alert">
  <div class="d-flex align-items-center">
    <i class="bi bi-check-circle-fill me-2"></i> <div class="flex-grow-1">
      <h6 class="alert-heading mb-1">Company Information</h6>
      <p class="mb-0" style="color: green">{{ session('success') }}</p>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
</div>

<!-- TABLE -->
<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>#</th>
            <th>Company Name</th>
            <th>Type</th>
            <th style="text-align: center">Action</th>
        </tr>
    </thead>

    <tbody>
        @forelse($gapcos as $index => $gapco)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $gapco->company_name }}</td>
            <td style="text-transform: uppercase">{{ $gapco->type }}</td>

            <td style="text-align: center">
    <div class="d-flex justify-content-center gap-2">

        <!-- EDIT BUTTON -->
        <button class="btn btn-primary btn-sm d-flex align-items-center justify-content-center"
                data-bs-toggle="modal"
                data-bs-target="#edit{{ $gapco->id }}"
                style="width: 38px; height: 38px;">
            <i class="bi bi-pencil-square"></i>
        </button>

        <!-- DELETE BUTTON -->
        <form action="{{ route('gapcos.destroy', $gapco->id) }}"
              method="POST"
              onsubmit="return confirm('Una uhakika unataka kufuta kampuni hii?')"
              class="m-0">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="btn btn-danger btn-sm d-flex align-items-center justify-content-center"
                    style="width: 38px; height: 38px;">
                <i class="bi bi-trash"></i>
            </button>
        </form>

    </div>
</td>
        </tr>

        <!-- EDIT MODAL -->
        <div class="modal fade" id="edit{{ $gapco->id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5>Edit Company</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('gapcos.update', $gapco->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <input type="text" name="company_name"
                                        value="{{ $gapco->company_name }}"
                                        class="form-control">
                                </div>

                                <div class="col-md-6 mb-2">
                                     <select name="type" id="type" class="form-control" required>
                                <option value="">-- Select Organization Type --</option>
                                <option value="	FUEL COMPANY">	FUEL COMPANY</option>
                                <option value="other">OTHER</option>
                            </select>
                                    
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-success">Update</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>

        @empty
        <tr>
            <td colspan="4" class="text-center">No Data Found</td>
        </tr>
        @endforelse
    </tbody>
</table>

</div>
</div>

@endsection