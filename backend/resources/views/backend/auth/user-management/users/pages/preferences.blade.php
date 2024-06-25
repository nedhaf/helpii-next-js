@extends('layouts.admin-app')

@section('page-header', 'Edit User')
@section('page-title-header', 'Edit User')

@section('page-breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('backend_all_users') }}">Users Management</a>
</li>
<li class="breadcrumb-item active">Edit User</li>
@endsection

@section('css')
<!-- SweetAlerts -->
<link rel="stylesheet" href="{{ asset('backend/sneat/assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />

<link rel="stylesheet" href="{{ asset('backend/sneat/assets/vendor/libs/toastr/toastr.css') }}" />
<link rel="stylesheet" href="{{ asset('backend/sneat/assets/vendor/libs/animate-css/animate.css') }}" />
@endsection

@section('content')
<div class="row">
    <!-- User Sidebar -->
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <!-- User Card -->
        <div class="card mb-4">
        <div class="card-body">
            <div class="user-avatar-section">
                <div class=" d-flex align-items-center flex-column">
                    <img class="img-fluid rounded my-4" src="{{asset('storage/upload/dummy.png')}}" height="110" width="110" alt="{{$editUser->name}}" />
                    <div class="user-info text-center">
                        <h4 class="mb-2">{{$editUser->name}}</h4>
                        <span class="badge bg-label-secondary">{{ $editUser->roles[0]->name }}</span>
                    </div>
                </div>
            </div>

            <h5 class="pb-2 border-bottom mb-4">Details</h5>
            <div class="info-container">
                <ul class="list-unstyled">
                    <li class="mb-3">
                    <span class="fw-bold me-2">Name:</span>
                    <span>{{$editUser->name}}</span>
                </li>
                <li class="mb-3">
                    <span class="fw-bold me-2">Email:</span>
                    <span><a href="mailto:{{$editUser->email}}">{{$editUser->email}}</a></span>
                </li>
                <li class="mb-3">
                    <span class="fw-bold me-2">Status:</span>
                    @if($editUser->trashed())
                        <span class="badge bg-label-secondary">Inactive</span>
                    @else
                        <span class="badge bg-label-success">Active</span>
                    @endif
                </li>
                <li class="mb-3">
                    <span class="fw-bold me-2">Role:</span>
                    <span class="text-truncate text-capitalize">{{$editUser->roles[0]->name}}</span>
                </li>
            </ul>
            <div class="d-flex justify-content-center pt-3">
                @if($editUser->trashed())
                    <a href="{{ route('backend_rise_user', ['rise_id' => $editUser->id]) }}" class="btn btn-label-info">Rise</a>
                @else
                    <a href="{{ route('backend_suspend_user', ['suspend_id' => $editUser->id]) }}" class="btn btn-label-danger suspend-user">Suspend</a>
                @endif
            </div>
        </div>
      </div>
    </div>
    <!-- /User Card -->
    </div>
    <!--/ User Sidebar -->

    <!-- User Content -->
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
        <ul class="nav nav-pills flex-column flex-md-row mb-3">
            <li class="nav-item"><a class="nav-link" href="{{ route('backend_edit_user_account',['edit_id' => $editUser->id]) }}"><i class="bx bx-user me-1"></i>Account</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('backend_edit_user_security',['edit_id' => $editUser->id]) }}"><i class="bx bx-lock-alt me-1"></i>Security</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('backend_edit_user_billings_plans',['edit_id' => $editUser->id]) }}"><i class="bx bx-detail me-1"></i>Billing & Plans</a></li>
            <li class="nav-item active"><a class="nav-link active" href="{{ route('backend_edit_user_preferances',['edit_id' => $editUser->id]) }}"><i class="fas fa-sliders me-1"></i></i>Preferences</a></li>
        </ul>

        <!-- Preferences Details -->
        <div class="card mb-4">
            <form class="card-body" method="POST" action="{{ route('backend_update_user_preferences', ['update_id' => $editUser->id]) }}" enctype="multipart/form-data" id="createUser">
            @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="birthday">Birthday</label>
                        <input type="date" name="birthday" class="form-control @error('birthday') is-invalid @enderror" id="birthday" placeholder="dd-mm-YYYY" value="{{ !empty($editUser->preferences) ? $editUser->preferences->birthday : '' }}">
                        @error('birthday')
                            <span id="birthday-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="age_group">Age</label>
                        <select name="age_group" id="age_group" class="form-select text-capitalize @error('age_group') is-invalid @enderror">
                            <option value="18-24" {{ !empty($editUser->preferences) && $editUser->preferences->age_group == '18-24' ? 'selected' : '' }}>18-24</option>
                            <option value="25-34" {{ !empty($editUser->preferences) && $editUser->preferences->age_group == '25-34' ? 'selected' : '' }}>25-34</option>
                            <option value="35-54" {{ !empty($editUser->preferences) && $editUser->preferences->age_group == '35-54' ? 'selected' : '' }}>35-54</option>
                            <option value="55-64" {{ !empty($editUser->preferences) && $editUser->preferences->age_group == '55-64' ? 'selected' : '' }}>55-64</option>
                            <option value="65+" {{ !empty($editUser->preferences) && $editUser->preferences->age_group == '65+' ? 'selected' : '' }}>65+</option>
                        </select>
                        @error('age_group')
                            <span id="age_group-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="gender_identity">Gender Identity</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check @error('gender_identity') form-check-danger @enderror">
                                    <input name="gender_identity" class="form-check-input @error('gender_identity') is-invalid @enderror" type="radio" value="male" id="male" {{ !empty($editUser->preferences) && $editUser->preferences->gender_identity == 'male' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="male">
                                        Male
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check @error('gender_identity') form-check-danger @enderror">
                                    <input name="gender_identity" class="form-check-input @error('gender_identity') is-invalid @enderror" type="radio" value="female" id="defaultRadio2" {{ !empty($editUser->preferences) && $editUser->preferences->gender_identity == 'female' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="defaultRadio2">
                                        Female
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="form-check @error('gender_identity') form-check-danger @enderror">
                                    <input name="gender_identity" class="form-check-input @error('gender_identity') is-invalid @enderror" type="radio" value="non-binary" id="non-binary"  {{ !empty($editUser->preferences) && $editUser->preferences->gender_identity == 'non-binary' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="non-binary">
                                        Non-binary
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mt-3">
                                <div class="form-check @error('gender_identity') form-check-danger @enderror">
                                    <input name="gender_identity" class="form-check-input @error('gender_identity') is-invalid @enderror" type="radio" value="i prefer not to answer" id="i_prefer_not_to_answer" {{ !empty($editUser->preferences) && $editUser->preferences->gender_identity == 'i prefer not to answer' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="i_prefer_not_to_answer">
                                        I prefer not to answer
                                    </label>
                                </div>
                            </div>
                            @error('gender_identity')
                                <span id="gender_identity-error" class="error invalid-feedback mt-3">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="birthday">Favorite Locations</label>
                        <div class="mt-3">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCenter">
                                Add Location
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="modalCenter" tabindex="-1" style="display: none;" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalCenterTitle">Modal title</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col mb-3">
                                                    <label for="nameWithTitle" class="form-label">Location</label>
                                                    <input type="text" id="nameWithTitle" class="form-control" placeholder="">
                                                    <div id="nameWithTitlelHelp" class="form-text">Enter an address or a zip code, or use your current location.</div>
                                                </div>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col mb-0">
                                                    <label for="emailWithTitle" class="form-label">Loaction Name</label>
                                                    <div class="form-check @error('location_name') 'form-check-danger' @enderror">
                                                        <input name="location_name" class="form-check-input @error('location_name') 'is-invalid' @enderror" type="radio" value="home" id="home">
                                                        <label class="form-check-label" for="home">
                                                            Home
                                                        </label>
                                                    </div>
                                                    <div class="form-check @error('location_name') 'form-check-danger' @enderror mt-2">
                                                        <input name="location_name" class="form-check-input @error('location_name') 'is-invalid' @enderror" type="radio" value="work" id="work">
                                                        <label class="form-check-label" for="work">
                                                            Work
                                                        </label>
                                                    </div>
                                                    <div class="form-check @error('location_name') 'form-check-danger' @enderror mt-2">
                                                        <input name="location_name" class="form-check-input @error('location_name') 'is-invalid' @enderror" type="radio" value="favourite" id="favourite">
                                                        <label class="form-check-label" for="favourite">
                                                            Favorite
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
                    <a href="{{ route('backend_all_users') }}" type="reset" class="btn btn-label-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <!--/ User Pills -->
</div>
@endsection

@section('js')
<script src="{{ asset('backend/sneat/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('backend/sneat/assets/js/extended-ui-sweetalert2.js') }}"></script>
<script src="{{ asset('backend/sneat/assets/vendor/libs/toastr/toastr.js') }}"></script>
<script>
    // Display toaster msg
    @if(Session::has('success'))
        toastr.options = {
            "showEasing" : "swing",
            "showMethod" : "fadeIn",
            "hideEasing" : "linear",
            "hideMethod" : "fadeOut",
            "closeMethod" : "fadeOut",
            "closeEasing" : "linear",
            "closeButton" : true,
            "progressBar" : true ,
            "delay": 3000
        };
        toastr.success("{{ session('success') }}");
    @endif

    @if(Session::has('error'))
        toastr.options = {
            "showEasing" : "swing",
            "showMethod" : "fadeIn",
            "hideEasing" : "linear",
            "hideMethod" : "fadeOut",
            "closeMethod" : "fadeOut",
            "closeEasing" : "linear",
            "closeButton" : true,
            "progressBar" : true ,
            "delay": 3000
        };
        toastr.error("{{ session('error') }}");
    @endif

    $(document).ready(function () {

        $('#profile_img').change(function(){
            console.log(this.files);
            let reader = new FileReader();
            reader.onload = (e) => {
                $('.remove-image').show();
                $('#preview-image-before-upload').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        $(document).on('click', '.remove-image', function(){
            $('#profile_img').val(null);
            $('#preview-image-before-upload').attr('src','');
            $(this).hide();
        });
    });
</script>
@endsection