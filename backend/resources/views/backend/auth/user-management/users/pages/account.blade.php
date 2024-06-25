@extends('backend.layouts.administrator-app')

@section('head-title', 'Edit User')

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
            <li class="nav-item"><a class="nav-link active" href="{{ route('administrator.backend_edit_user', ['id' => $editUser->uuid]) }}"><i class="bx bx-user me-1"></i>Account</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('administrator.backend_edit_user_security', ['id' => $editUser->uuid]) }}"><i class="bx bx-lock-alt me-1"></i>Security</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('administrator.backend_edit_user_connection', ['id' => $editUser->uuid]) }}"><i class="bx bx-link-alt me-1"></i>Connections</a></li>
            {{-- <li class="nav-item"><a class="nav-link" href="#"><i class="bx bx-detail me-1"></i>Billing & Plans</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="#"><i class="fas fa-sliders me-1"></i>Preferences</a></li> --}}
        </ul>

        <!-- Account Details -->
        <div class="card mb-4">
            <h5 class="card-header">{{ __('Basic Details') }}</h5>
            <form class="card-body" method="POST" action="{{ route('administrator.backend_update_user_account', ['id' => $editUser->uuid]) }}" enctype="multipart/form-data" id="editUser">
            @csrf
                <div class="row g-3">
                    <input type="file" name="avatar_location" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" />
                    <div class="col-md-4">
                        <label class="form-label" for="first_name">{{ __('First Name') }}</label>
                        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="John" value="{{ old('first_name') ?? $editUser->first_name }}"/>
                        @error('first_name')
                            <span id="first_name-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="last_name">{{ __('Last Name') }}</label>
                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Doe" value="{{ old('last_name') ?? $editUser->last_name }}"/>
                        @error('last_name')
                            <span id="last_name-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="email">{{ __('Email') }}</label>
                        <div class="input-group input-group-merge">
                            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="john.doe" aria-label="john.doe" aria-describedby="email2" value="{{ old('email') ?? $editUser->email }}" />
                            @error('email')
                                <span id="email-error" class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="active">{{ __('User Status') }}</label>
                        <div class="mt-1 mb-0">
                            <label class="switch switch-lg">
                                {{-- <input type="checkbox" class="switch-input" type="checkbox" name="active" id="active" value="1" checked> --}}
                                <input type="checkbox" class="switch-input" type="checkbox" name="active" id="active" value="{{ $editUser->active == 1 ? '1' : '0' }}" {{ $editUser->active == 1 ? 'checked' : '' }}>
                                {{-- <input type="hidden" name="active" value="0"> --}}
                                <span class="switch-toggle-slider">
                                  <span class="switch-on">
                                    <i class="bx bx-check"></i>
                                  </span>
                                  <span class="switch-off">
                                    <i class="bx bx-x"></i>
                                  </span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="confirmed">{{ __('Is Confirmed?') }}</label>
                        <div class="mt-1 mb-0">
                            <label class="switch switch-lg switch-success">
                                <input type="checkbox" class="switch-input" type="checkbox" name="confirmed" id="confirmed" value="{{ $editUser->confirmed == 1 ? '1' : '0' }}" {{ $editUser->confirmed == 1 ? 'checked' : '' }}>
                                {{-- <input type="hidden" name="confirmed" value="0"> --}}
                                <span class="switch-toggle-slider">
                                  <span class="switch-on">
                                    <i class="bx bx-check"></i>
                                  </span>
                                  <span class="switch-off">
                                    <i class="bx bx-x"></i>
                                  </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Update') }}</button>
                    <a href="#" type="reset" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
        <!-- Account Details End -->

        <!-- Profile Details -->
        <div class="card mb-4">
            <h5 class="card-header">{{ __('Profile Details') }}</h5>
            <form class="card-body" method="POST" action="{{ route('administrator.backend_update_user_profile', ['id' => $editUser->uuid]) }}" enctype="multipart/form-data" id="editUser">
            @csrf
                <div class="row g-3">
                    <input type="file" name="avatar_location" id="upload" class="account-file-input" hidden accept="image/png, image/jpeg" />
                    <div class="col-md-12">
                        <label class="form-label" for="address">{{ __('Address') }}</label>
                        <input type="text" name="address" id="address" class="form-control map-input @error('address') is-invalid @enderror" placeholder="Write address" value="{{ old('address') ?? !empty($editUser->profile->address) ? $editUser->profile->address : '' }}"/>
                        <input type="hidden" name="latitude" id="latitude" value="0" />
                        <input type="hidden" name="longitude" id="longitude" value="0" />
                        @error('address')
                            <span id="address-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="phone">{{ __('Phone no') }}</label>
                        <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') ?? !empty($editUser->profile->phone) ? $editUser->profile->phone : ''  }}" placeholder="+91 12345 67890">
                        @error('phone')
                            <span id="phone-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="experience">{{ __('Experiance') }}</label>
                        <input type="number" name="experience" id="experience" class="form-control @error('experience') is-invalid @enderror" value="{{ old('experience') ?? !empty($editUser->profile->experience) ? $editUser->profile->experience : '' }}" placeholder="0">
                        @error('experience')
                            <span id="experience-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="about">{{ __('About') }}</label>
                        <textarea class="form-control @error('about') is-invalid @enderror" name="about" id="about" rows="3" value="{{ old('about') ?? !empty($editUser->profile->about) ? $editUser->profile->about : '' }}">{{ old('about') ?? !empty($editUser->profile->about) ? $editUser->profile->about : '' }}</textarea>
                        @error('about')
                            <span id="about-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">{{ __('Update') }}</button>
                    <a href="#" type="reset" class="btn btn-label-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
        <!-- Profile Details End -->
    </div>
    <!--/ User Pills -->
</div>
@endsection

@push('after-scripts')
<script src="{{ asset('Backend/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('Backend/assets/js/extended-ui-sweetalert2.js') }}"></script>
<script src="{{ asset('Backend/assets/vendor/libs/toastr/toastr.js') }}"></script>
{{-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyANGQiDmKPOHX5H5fUJQiuVsjhsL1Q3MtU&libraries=places&language=sv&region=SE" async="" defer=""></script> --}}
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places"></script>
<script src="{{ asset('js/backend/auth/user-management.js') }}"></script>
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

    $(document).ready(function(){
        // Active user switch
        $(document).on( 'click', '#active', function(e) {
            let isCheked = this.checked;
            let val = isCheked ? 1 : 0;
            $(this).val(val);
            $(this).attr('checked', isCheked);
        });

        // Cofirm user switch
        $(document).on( 'click', '#confirmed', function(e) {
            let isCheked = this.checked;
            let val = isCheked ? 1 : 0;
            $(this).val(val);
            $(this).attr('checked', isCheked);
        });

        let e = document.getElementById("uploadedAvatar");
        const l = document.querySelector(".account-file-input"),
            c = document.querySelector(".account-image-reset");
        if (e) {
            const r = e.src;
            l.onchange = () => {
                l.files[0] && (e.src = window.URL.createObjectURL(l.files[0]))
                c.disabled = false;
            }, c.onclick = () => {
                l.value = "", e.src = r, c.disabled = true;
            }
        }
    });
</script>
@endpush