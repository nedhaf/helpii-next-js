@extends('backend.layouts.administrator-app')

@section('head-title', 'Edit User')

{{-- @section('page-breadcrumbs')
<li class="breadcrumb-item">
    <a href="{{ route('backend_all_users') }}">Users Management</a>
</li>
<li class="breadcrumb-item active">Edit User</li>
@endsection --}}

@push('after-styles')
<!-- SweetAlerts -->
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />

<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/toastr/toastr.css') }}" />
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/animate-css/animate.css') }}" />
@endpush

@section('content')
<div class="row">
    <!-- User Sidebar -->
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <!-- User Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="user-avatar-section">
                    <div class=" d-flex align-items-center flex-column">
                        <img class="img-fluid rounded my-4" src="{{asset('storage/'.$editUser->avatar_location)}}" height="110" width="110" alt="{{$editUser->full_name}}" id="uploadedAvatar"/>
                        @error('avatar_location')
                            <span id="avatar_location-error" class="error invalid-feedback mb-3" style="display:block">{{ $message }}</span>
                        @enderror
                        <div class="button-wrapper ">

                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>

                            </label>
                            <button type="button" class="btn btn-label-secondary account-image-reset mb-4" disabled>
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                            </button>

                        </div>
                        <div id="defaultFormControlHelp" class="form-text text-center mb-3"><strong>Note:</strong> If you need to upload / change photo, after select photo then update account.</div>

                        <div class="user-info text-center">
                            <h4 class="mb-2">{{$editUser->name}}</h4>
                            <span class="badge bg-label-secondary">{{ $editUser->roles[0]->name }}</span>
                        </div>
                    </div>
                </div>

                <h5 class="pb-2 border-bottom mb-4">{{ __('Details') }}</h5>
                <div class="info-container">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <span class="fw-bold me-2">{{ __('Full Name') }}:</span>
                            <span>{{$editUser->full_name}}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-bold me-2">{{ __('Email') }}:</span>
                            <span><a href="mailto:{{$editUser->email}}">{{$editUser->email}}</a></span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-bold me-2">{{ __('Uuid') }}:</span>
                            <span>{{$editUser->uuid}}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-bold me-2">{{ __('Status') }}:</span>
                            @if($editUser->active == 1)
                                <span class="badge bg-label-success">Active</span>
                            @else
                                <span class="badge bg-label-danger">Inactive</span>
                            @endif
                        </li>
                        <li class="mb-3">
                            <span class="fw-bold me-2">{{ __('Confirmation') }}:</span>
                            @if($editUser->confirmed == 1)
                                <span class="badge bg-label-success">{{ __('Confirmed') }}</span>
                            @else
                                <span class="badge bg-label-danger">{{ __('Pending') }}</span>
                            @endif
                        </li>
                        <li class="mb-3">
                            <span class="fw-bold me-2">{{ __('Role:') }}</span>
                            <span class="text-truncate text-capitalize">{{$editUser->roles[0]->name}}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-bold me-2">{{ __('Contact') }}:</span>
                            <span>{{ !empty($editUser->profile->phone) ? $editUser->profile->phone : '' }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-bold me-2">{{ __('Experiance') }}:</span>
                            <span>{{ !empty($editUser->profile->experience) ? $editUser->profile->experience : '' }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-bold me-2">{{ __('About') }}:</span>
                            <span>{{ !empty($editUser->profile->about) ? $editUser->profile->about : '' }}</span>
                        </li>
                    </ul>
                    @if( $editUser->id != 1 )
                        <div class="d-flex justify-content-center pt-3">
                            <form action="{{ $editUser->trashed() ? route('administrator.backend_rise_user', ['id' => $editUser->uuid]) : route('administrator.backend_suspend_user', ['id' => $editUser->uuid]) }}" method="POST" id="suspendriseform">
                                @csrf
                                @if($editUser->trashed())
                                    <button type="button" class="btn btn-label-info show-alert-rise-box"> Rise </button>
                                @else
                                    <button type="button" class="btn btn-label-danger show-alert-suspend-box"> Suspend </button>
                                @endif
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    <!-- /User Card -->
    </div>
    <!--/ User Sidebar -->

    <!-- User Content -->
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
        <ul class="nav nav-pills flex-column flex-md-row mb-3">
             <li class="nav-item"><a class="nav-link" href="{{ route('administrator.backend_edit_user',['id' => $editUser->uuid]) }}"><i class="bx bx-user me-1"></i>{{ __('Account') }}</a></li>
            <li class="nav-item"><a class="nav-link active" href="{{ route('administrator.backend_edit_user_security',['id' => $editUser->uuid]) }}"><i class="bx bx-lock-alt me-1"></i>{{ __('Security') }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('administrator.backend_edit_user_connection', ['id' => $editUser->uuid]) }}"><i class="bx bx-link-alt me-1"></i>Connections</a></li>
            {{-- <li class="nav-item"><a class="nav-link" href="#"><i class="bx bx-detail me-1"></i>Billing & Plans</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-sliders me-1"></i>Preferences</a></li> --}}
        </ul>

        <!-- Security Details -->
        <div class="card mb-4">
            <h5 class="card-header">{{ __('Change Password') }}</h5>
            <form class="card-body" method="POST" action="{{ route('administrator.backend_update_user_security', ['id' => $editUser->uuid]) }}" enctype="multipart/form-data" id="createUser">
            @csrf
                <div class="row g-3">
                    <div class="alert alert-warning" role="alert">
                        <h6 class="alert-heading fw-bold mb-1">{{ __('Ensure that these requirements are met') }}</h6>
                        <span>{{ __('Minimum 6 characters long, uppercase & symbol') }}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="form-password-toggle">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password2" />
                                <span class="input-group-text cursor-pointer" id="password2"><i class="bx bx-hide"></i></span>
                                @error('password')
                                    <span id="password-error" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-password-toggle">
                            <label class="form-label" for="password_confirmation">Confirm Password</label>
                            <div class="input-group input-group-merge">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password_confirmation2" />
                                <span class="input-group-text cursor-pointer" id="password_confirmation2"><i class="bx bx-hide"></i></span>
                                @error('password_confirmation')
                                    <span id="password_confirmation-error" class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
                    <a href="#" type="reset" class="btn btn-label-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <!--/ User Pills -->
</div>
@endsection

@push('after-scripts')
<script src="{{ asset('Backend/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('Backend/assets/js/extended-ui-sweetalert2.js') }}"></script>
<script src="{{ asset('Backend/assets/vendor/libs/toastr/toastr.js') }}"></script>
<script src="{{ asset('js/backend/auth/user-management.js') }}"></script>

<script>
    $(document).ready(function(){
        window.Helpers.initPasswordToggle()
    });

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



</script>
@endpush