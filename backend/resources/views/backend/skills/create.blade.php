@extends('backend.layouts.administrator-app')

@section('head-title', 'Create Skill')

@section('content')
<div class="row">
    <section class="col-lg-6 offset-lg-3">
        <div class="card mb-4">
            <form class="card-body" method="post" action="{{ route('administrator.backend_store_skill') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Abc" value="{{ old('name') }}"/>
                        @error('name')
                            <span id="name-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="2">{{ old('description') }}</textarea>
                        @error('description')
                            <span id="description-error" class="error invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 ">
                        <img class="img-fluid rounded my-4 justify-content-center " src="{{asset('storage/skills/no_img.jpeg')}}" height="110" width="110" alt="no_img" id="uploadedSkillAvatar"/>
                        <input type="file" name="avatar" id="upload" class="skill-file-input" hidden />
                        @error('avatar')
                            <span id="avatar-error" class="error invalid-feedback mb-3" style="display:block">{{ $message }}</span>
                        @enderror
                        <div class="button-wrapper">

                            <label for="upload" class="btn btn-primary me-2 mb-2" tabindex="0">
                                <span class="d-none d-sm-block">Upload new photo</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>

                            </label>
                            <button type="button" class="btn btn-label-secondary skill-image-reset mb-2" disabled>
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Reset</span>
                            </button>

                            <div id="defaultFormControlHelp" class="form-text mb-3 text-warning"><strong>Note:</strong> If you need to upload / change photo, after select photo click on Create button.</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="active">Status</label>
                        <div class="mt-1 mb-0">
                            <label class="switch switch-lg">
                                <input type="checkbox" class="switch-input" type="checkbox" name="status" id="active" value="1" checked>
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
<script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script type = "text/javascript">
    $(document).ready(function(){

        tinymce.init({
            selector: 'textarea#description', // Replace this CSS selector to match the placeholder element for TinyMCE
            plugins: 'code table lists',
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | preview | forecolor backcolor',
            height: "300px"
        });

        // Skill avatar
        let e = document.getElementById("uploadedSkillAvatar");
        const l = document.querySelector(".skill-file-input"), c = document.querySelector(".skill-image-reset");
        if (e) {
            const r = e.src;
            l.onchange = () => {
                l.files[0] && (e.src = window.URL.createObjectURL(l.files[0]))
                c.disabled = false;
            }, c.onclick = () => {
                l.value = "", e.src = r, c.disabled = true;
            }
        }

        // Active skill switch
        $(document).on( 'click', '#active', function(e) {
            let isCheked = this.checked;
            let val = isCheked ? 1 : 0;
            $(this).val(val);
            $(this).attr('checked', isCheked);
        });

    });
</script>
@endpush