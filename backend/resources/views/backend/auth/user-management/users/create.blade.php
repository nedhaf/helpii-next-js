@extends('backend.layouts.administrator-app')

@section('head-title', 'Create User')

@section('meta')
<meta name="description" content="Most Powerful &amp; Comprehensive Bootstrap 5 HTML Admin Dashboard Template built for developers!" />
<meta name="keywords" content="dashboard, bootstrap 5 dashboard, bootstrap 5 design, bootstrap 5">
@endsection

@section('content')
<div class="row">
    <section class="col-lg-12">
        <div class="card mb-4">
            <form class="card-body" method="post" action="{{ route('administrator.backend_store_user') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" placeholder="John" value="{{ old('first_name') }}"/>
                        @error('first_name')
                            <span id="first_name-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" placeholder="Doe" value="{{ old('last_name') }}"/>
                        @error('last_name')
                            <span id="last_name-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="email">Email</label>
                        <div class="input-group input-group-merge">
                            <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="john.doe" aria-label="john.doe" aria-describedby="email2" value="{{ old('email') }}" />
                            <span class="input-group-text" id="email2">@example.com</span>
                            @error('email')
                                <span id="email-error" class="error invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3">
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
                    <div class="col-md-1">
                        <label class="form-label" for="active">User Status</label>
                        <div class="mt-1 mb-0">
                            <label class="switch switch-lg">
                                <input type="checkbox" class="switch-input" type="checkbox" name="active" id="active" value="1" checked>
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
                    <div class="col-md-1">
                        <label class="form-label" for="confirmed">Is Confirmed?</label>
                        <div class="mt-1 mb-0">
                            <label class="switch switch-lg switch-success">
                                <input type="checkbox" class="switch-input" type="checkbox" name="confirmed" id="confirmed" value="1" checked>
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
                    <input type="hidden" name="is_sp" id="is_sp" value="">
                    <input type="hidden" name="avatar_type" id="avatar_type" value="gravatar">
                    <input type="hidden" name="avatar_location" id="avatar_location" value="avatars/dummy.png">
                </div>
                <div class="mt-5 d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <button type="reset" class="btn btn-label-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('after-scripts')
<script type = "text/javascript">
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
    });
</script>
@endpush