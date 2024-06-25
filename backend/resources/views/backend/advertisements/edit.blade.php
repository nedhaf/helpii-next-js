@extends('backend.layouts.administrator-app')

@section('head-title', 'Edit Advertisement')

@push('after-styles')
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/select2/select2.css') }}">
{{-- <link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}" /> --}}
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}" />
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/pickr/pickr-themes.css') }}" />
@endpush

@section('content')
<div class="row">
    <section class="col-lg-12">
        <div class="card mb-4">
            <form class="card-body" method="post" action="{{ route('administrator.backend_advertisement_update', ['id' => $editAdvertisement->id]) }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" placeholder="John" value="{{ old('title') ?? $editAdvertisement->title }}"/>
                        @error('title')
                            <span id="title-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="skill-lists" class="form-label">Skill</label>
                        <select id="skill-lists" class="skill-lists form-select @error('skill_id') is-invalid @enderror" name="skill_id" data-allow-clear="true">
                            @if( !empty( $getSkills ) )
                                @foreach( $getSkills as $k => $skill )
                                    <option value="{{ $skill->id }}" {{ $editAdvertisement->skill_id == $skill->id ? 'selected' : '' }}>{{ $skill->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('skill_id')
                            <span id="skill_id-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="phone">{{ __('Phone no') }}</label>
                        <input type="tel" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') ?? $editAdvertisement->phone }}" placeholder="12345 67890">
                        @error('phone')
                            <span id="phone-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="city">City</label>
                        <input type="text" name="city" id="city" class="form-control @error('city') is-invalid @enderror" placeholder="abcd" value="{{ old('city') ?? $editAdvertisement->city  }}"/>
                        @error('city')
                            <span id="city-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="link">Link</label>
                        <input type="text" name="link" id="link" class="form-control @error('link') is-invalid @enderror" placeholder="https://www.google.com/" value="{{ old('link') ?? $editAdvertisement->link  }}"/>
                        @error('link')
                            <span id="link-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="text" name="start_date" id="start_date" placeholder="YYYY/MM/DD" class="form-control start_date @error('start_date') is-invalid @enderror" value="{{ old('start_date') ?? $editAdvertisement->start_date  }}" />
                        @error('start_date')
                            <span id="start_date-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="text" name="end_date" id="end_date" placeholder="YYYY/MM/DD" class="form-control end_date @error('end_date') is-invalid @enderror" value="{{ old('end_date') ?? $editAdvertisement->end_date }}"/>
                        @error('end_date')
                            <span id="end_date-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="cost">Cost</label>
                        <input type="text" name="cost" id="cost" class="form-control @error('cost') is-invalid @enderror" placeholder="0.00" value="{{ old('cost') ?? $editAdvertisement->cost }}"/>
                        @error('cost')
                            <span id="cost-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="position">Position</label>
                        <select class="form-select @error('city') is-invalid @enderror" name="position" id="position">
                            <option value="top" {{ $editAdvertisement->position == 'top' ? 'selected' : '' }}>Top</option>
                            <option value="bottom" {{ $editAdvertisement->position == 'bottom' ? 'selected' : '' }}>Bottom</option>
                            <option value="after_5" {{ $editAdvertisement->position == 'after_5' ? 'selected' : '' }}>After 5</option>
                            <option value="after_10" {{ $editAdvertisement->position == 'after_10' ? 'selected' : '' }}>After 10</option>
                        </select>
                        @error('city')
                            <span id="city-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-1">
                        <label class="form-label" for="color">Color</label>
                        <input type="color" name="color" id="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color') ?? $editAdvertisement->color }}"/>
                        @error('color')
                            <span id="color-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="2">{{ old('description') ?? $editAdvertisement->description }}</textarea>
                        @error('description')
                            <span id="description-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="upload-img">Avertisement Image</label>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid rounded me-3 justify-content-center" src="{{ old('image') ?? asset('storage/advertisement/image/'.$editAdvertisement->image) }}" height="100" width="100" alt="no_img" id="uploadedAdvrtImg"/>
                            <input type="file" name="image" id="upload-img" class="img-file-input" hidden accept="image/png, image/jpeg" />
                            @error('image')
                                <span id="image-error" class="error invalid-feedback mb-3" style="display:block">{{ $message }}</span>
                            @enderror
                            <div class="button-wrapper">
                                <label for="upload-img" class="btn btn-primary me-2 mb-2" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new image</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                </label>
                                <button type="button" class="btn btn-label-secondary img-image-reset mb-2">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="upload-badge">Badge image</label>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid rounded me-3 justify-content-center" src="{{ old('badge_img') ?? asset('storage/advertisement/badgeImg/'.$editAdvertisement->badge_img) }}" height="100" width="100" alt="no_img" id="uploadedBadgeImg"/>
                            <input type="file" name="badge_img" id="upload-badge" class="badge-file-input" hidden accept="image/png, image/jpeg" />
                            @error('badge_img')
                                <span id="badge_img-error" class="error invalid-feedback mb-3" style="display:block">{{ $message }}</span>
                            @enderror
                            <div class="button-wrapper">
                                <label for="upload-badge" class="btn btn-primary me-2 mb-2" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new image</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                </label>
                                <button type="button" class="btn btn-label-secondary badge-image-reset mb-2">
                                    <i class="bx bx-reset d-block d-sm-none"></i>
                                    <span class="d-none d-sm-block">Reset</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 d-flex justify-content-center">
                    {{-- For get default image path --}}
                    <input type="hidden" id="default_image_path" value="{{asset('storage/no_img.jpeg')}}">

                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <button type="reset" class="btn btn-label-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('after-scripts')
<script src="{{ asset('Backend/assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('Backend/assets/vendor/libs/moment/moment.js') }}"></script>
{{-- <script src="{{ asset('Backend/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script> --}}
<script src="{{ asset('Backend/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('Backend/assets/vendor/libs/pickr/pickr.js') }}"></script>
<script src="{{ asset('js/backend/advertisements/advertisement.js') }}"></script>


@endpush
