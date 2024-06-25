@extends('backend.layouts.administrator-app')

@section('head-title', 'Badges')

@push('after-styles')
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/pickr/pickr-themes.css') }}" />
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/tagify/tagify.css') }}" />
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/toastr/toastr.css') }}" />
@endpush

@section('content')
<div class="row">
    <section class="col-lg-12">
        <div class="card mb-4">
            <form class="card-body" method="post" action="{{ route('administrator.backend_store_basic_settings') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="John" value="{{ old('title') ?? $basicsettings['title'] }}"/>
                        @error('title')
                            <span id="title-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="title">Email</label>
                        <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="abc@mail.com" value="{{ old('email') ?? $basicsettings['email'] }}"/>
                        @error('email')
                            <span id="email-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="phone">Phone</label>
                        <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="0123456789" value="{{ old('phone') ?? $basicsettings['phone'] }}"/>
                        @error('phone')
                            <span id="phone-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label" for="facebookurl">Facebook Url</label>
                        <input type="url" name="facebookurl" id="facebookurl" class="form-control @error('facebookurl') is-invalid @enderror" placeholder="https://www.abc.com" value="{{ old('facebookurl') ?? $basicsettings['facebookurl'] }}"/>
                        @error('facebookurl')
                            <span id="facebookurl-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="twitterurl">Twitter Url</label>
                        <input type="url" name="twitterurl" id="twitterurl" class="form-control @error('twitterurl') is-invalid @enderror" placeholder="https://www.abc.com" value="{{ old('twitterurl') ?? $basicsettings['twitterurl'] }}"/>
                        @error('twitterurl')
                            <span id="twitterurl-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="linkdinurl">Linkdin Url</label>
                        <input type="url" name="linkdinurl" id="linkdinurl" class="form-control @error('linkdinurl') is-invalid @enderror" placeholder="https://www.abc.com" value="{{ old('linkdinurl') ?? $basicsettings['linkdinurl'] }}"/>
                        @error('linkdinurl')
                            <span id="linkdinurl-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="instagramurl">Instagram Url</label>
                        <input type="url" name="instagramurl" id="instagramurl" class="form-control @error('instagramurl') is-invalid @enderror" placeholder="https://www.abc.com" value="{{ old('instagramurl') ?? $basicsettings['instagramurl'] }}"/>
                        @error('instagramurl')
                            <span id="instagramurl-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="2">{{ old('description') ?? $basicsettings['description'] }}</textarea>
                        @error('description')
                            <span id="description-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="address">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" name="address" id="address" rows="2">{{ old('address') ?? $basicsettings['address'] }}</textarea>
                        @error('address')
                            <span id="address-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="tag" class="form-label">Tags</label>
                        <input id="tag" class="form-control" name="tag" value="{{ old('tag') ?? $basicsettings['tag'] }}" placeholder="abc, def, ghi" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="upload-site-logo">Site logo</label>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid rounded me-3 justify-content-center" src="{{ old('sitelogo') ?? !empty($basicsettings['sitelogo']) ? asset('storage/site-settings/'.$basicsettings['sitelogo']) : asset('storage/no_img.jpeg') }}" height="100" width="100" alt="no_img" id="uploadedSiteLogo"/>
                            <input type="file" name="sitelogo" id="upload-site-logo" class="logo-file-input" hidden accept="image/png, image/jpeg" />
                            @error('sitelogo')
                                <span id="sitelogo-error" class="error invalid-feedback mb-3" style="display:block">{{ $message }}</span>
                            @enderror
                            <div class="button-wrapper">
                                <label for="upload-site-logo" class="btn btn-primary me-2 mb-2" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new logo</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                </label>
                                <button type="button" class="btn btn-label-secondary logo-image-reset mb-2">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="upload-site-background">Site Background</label>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid rounded me-3 justify-content-center" src="{{ old('backgroudimage') ?? !empty($basicsettings['backgroudimage']) ? asset('storage/site-settings/'.$basicsettings['backgroudimage']) : asset('storage/no_img.jpeg') }}" height="100" width="100" alt="no_img" id="uploadedSiteBackground"/>
                            <input type="file" name="backgroudimage" id="upload-site-background" class="background-file-input" hidden accept="image/png, image/jpeg" />
                            @error('sitelogo')
                                <span id="sitelogo-error" class="error invalid-feedback mb-3" style="display:block">{{ $message }}</span>
                            @enderror
                            <div class="button-wrapper">
                                <label for="upload-site-background" class="btn btn-primary me-2 mb-2" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new logo</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                </label>
                                <button type="button" class="btn btn-label-secondary background-image-reset mb-2">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="backgroudcolor">Background Color</label>
                        <input type="color" name="backgroudcolor" id="backgroudcolor" class="form-control @error('backgroudcolor') is-invalid @enderror" value="{{ old('backgroudcolor') ?? $basicsettings['backgroudcolor'] }}"/>
                    </div>
                </div>
                <div class="mt-5 d-flex justify-content-center">
                    {{-- For get default image path --}}
                    <input type="hidden" id="default_image_path" value="{{asset('storage/no_img.jpeg')}}">
                    <input type="hidden" name="previous_backgroudimage" id="previous_backgroudimage" value="{{ $basicsettings['backgroudimage'] }}">
                    <input type="hidden" name="previous_logo" id="previous_logo" value="{{ $basicsettings['sitelogo'] }}">

                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <button type="reset" class="btn btn-label-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('after-scripts')
<script src="{{ asset('Backend/assets/vendor/libs/pickr/pickr.js') }}"></script>
<script src="{{ asset('js/backend/site-settings/basic-settings.js') }}"></script>
<script src="{{ asset('Backend/assets/vendor/libs/tagify/tagify.js') }}"></script>
<script src="{{ asset('Backend/assets/vendor/libs/toastr/toastr.js') }}"></script>
{{-- <script src="{{ asset('Backend/assets/js/forms-tagify.js') }}"></script> --}}
<script type="text/javascript">
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