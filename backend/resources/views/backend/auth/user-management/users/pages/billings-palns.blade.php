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
            <li class="nav-item active"><a class="nav-link active" href="{{ route('backend_edit_user_billings_plans',['edit_id' => $editUser->id]) }}"><i class="bx bx-detail me-1"></i>Billing & Plans</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('backend_edit_user_preferances',['edit_id' => $editUser->id]) }}"><i class="fas fa-sliders me-1"></i>Preferences</a></li>
        </ul>

        <!-- Billings N Plans Details -->
        <div class="card mb-4">
            <h5 class="card-header">Billings N Plans</h5>
        </div>
    </div>
    <!--/ User Pills -->
</div>
@endsection

@section('js')
<script>

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