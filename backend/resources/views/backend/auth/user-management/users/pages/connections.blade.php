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
                        <img class="img-fluid rounded my-4" src="{{asset('storage/'.$editUser->avatar_location)}}" height="110" width="110" alt="{{$editUser->full_name}}" />
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
                        <span class="fw-bold me-2">Role:</span>
                        <span class="text-truncate text-capitalize">{{$editUser->roles[0]->name}}</span>
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
            <li class="nav-item"><a class="nav-link" href="{{ route('administrator.backend_edit_user_security',['id' => $editUser->uuid]) }}"><i class="bx bx-lock-alt me-1"></i>{{ __('Security') }}</a></li>
            <li class="nav-item"><a class="nav-link active" href="{{ route('administrator.backend_edit_user_connection', ['id' => $editUser->uuid]) }}"><i class="bx bx-link-alt me-1"></i>Connections</a></li>
            {{-- <li class="nav-item"><a class="nav-link" href="#"><i class="bx bx-detail me-1"></i>Billing & Plans</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-sliders me-1"></i>Preferences</a></li> --}}
        </ul>

        <!-- Social Accounts -->
        <div class="card mb-4">
            <h5 class="card-header">Social Accounts</h5>
            <div class="card-body">
                <p>Display content from social accounts on your site</p>
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('Backend/assets/img/icons/brands/facebook.png') }}" alt="facebook" class="me-3" height="30">
                    </div>
                    <div class="flex-grow-1 row">
                        <div class="col-8 col-sm-7 mb-sm-0 mb-2">
                            <h6 class="mb-0">Facebook</h6>
                            <small class="text-muted">Not Connected</small>
                        </div>
                        <div class="col-4 col-sm-5 text-end">
                            <button type="button" class="btn btn-icon btn-label-secondary">
                            <i class='bx bx-link-alt'></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('Backend/assets/img/icons/brands/twitter.png') }}" alt="twitter" class="me-3" height="30">
                    </div>
                    <div class="flex-grow-1 row">
                        <div class="col-8 col-sm-7 mb-sm-0 mb-2">
                            <h6 class="mb-0">Twitter</h6>
                            <a href="https://twitter.com/Theme_Selection" target="_blank">@ThemeSelection</a>
                        </div>
                        <div class="col-4 col-sm-5 text-end">
                            <button type="button" class="btn btn-icon btn-label-danger">
                            <i class='bx bx-trash-alt'></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('Backend/assets/img/icons/brands/instagram.png') }}" alt="instagram" class="me-3" height="30">
                    </div>
                    <div class="flex-grow-1 row">
                        <div class="col-8 col-sm-7 mb-sm-0 mb-2">
                            <h6 class="mb-0">instagram</h6>
                            <a href="https://www.instagram.com/themeselection/" target="_blank">@ThemeSelection</a>
                        </div>
                        <div class="col-4 col-sm-5 text-end">
                            <button type="button" class="btn btn-icon btn-label-danger">
                            <i class='bx bx-trash-alt'></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
    window.Helpers.initPasswordToggle();
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