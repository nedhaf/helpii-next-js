@extends('backend.layouts.administrator-app')
@section('head-title', 'User Management')

@section('meta')
<meta name="description" content="Most Powerful &amp; Comprehensive Bootstrap 5 HTML Admin Dashboard Template built for developers!" />
<meta name="keywords" content="dashboard, bootstrap 5 dashboard, bootstrap 5 design, bootstrap 5">
@endsection

@push('after-styles')
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}">
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/flatpickr/flatpickr.css') }}" />
<!-- Row Group CSS -->
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}">
<!-- SweetAlerts -->
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/toastr/toastr.css') }}" />
<link rel="stylesheet" href="{{ asset('Backend/assets/vendor/libs/animate-css/animate.css') }}" />
@endpush

@section('content')
<div class="row g-4 mb-4">
   <div class="col-sm-6 col-xl-3">
      <div class="card">
         <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
               <div class="content-left">
                  <span>{{ __('Total Users') }}</span>
                  <div class="d-flex align-items-end mt-2">
                     <h4 class="mb-0 me-2">{{ count($getUsers) }}</h4>
                     {{-- <small class="text-success">(+29%)</small> --}}
                  </div>
                  {{-- <p class="mb-0">Total Users</p> --}}
               </div>
               <div class="avatar">
                  <span class="avatar-initial rounded bg-label-primary">
                  <i class="fas fa-users fa-lg"></i>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-sm-6 col-xl-3">
      <div class="card">
         <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
               <div class="content-left">
                  <span>{{ __('Suspended Users') }}</span>
                  <div class="d-flex align-items-end mt-2">
                     <h4 class="mb-0 me-2">{{ $getSuspendedUsersCount }}</h4>
                     {{-- <small class="text-success">(+18%)</small> --}}
                  </div>
               </div>
               <div class="avatar">
                  <span class="avatar-initial rounded bg-label-danger">
                  <i class="fas fa-user-slash fa-lg"></i>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-sm-6 col-xl-3">
      <div class="card">
         <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
               <div class="content-left">
                  <span>{{ __('Active Users') }}</span>
                  <div class="d-flex align-items-end mt-2">
                     <h4 class="mb-0 me-2">{{ $getActiveUsersCount }}</h4>
                     {{-- <small class="text-danger">(-14%)</small> --}}
                  </div>
               </div>
               <div class="avatar">
                  <span class="avatar-initial rounded bg-label-success">
                  <i class="fas fa-user-check fa-lg"></i>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-sm-6 col-xl-3">
      <div class="card">
         <div class="card-body">
            <div class="d-flex align-items-start justify-content-between">
               <div class="content-left">
                  <span>{{ __('Inactive Users') }}</span>
                  <div class="d-flex align-items-end mt-2">
                     <h4 class="mb-0 me-2">{{ $getInActiveUsersCount }}</h4>
                     {{-- <small class="text-success">(+42%)</small> --}}
                  </div>
               </div>
               <div class="avatar">
                  <span class="avatar-initial rounded bg-label-warning">
                  <i class="fas fa-user-times fa-lg"></i>
                  </span>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- Users List -->
<div class="row">
   <section class="col-lg-12">
      <div class="card">
         <div class="card-datatable table-responsive">
            <div class="import-user-process-wrapper d-flex justify-content-center mt-3"></div>
            <div class="card-header border-bottom">
               <h5 class="card-title">Search Filter</h5>
               <div class="d-flex align-items-center row py-3 gap-3 gap-md-0">
                  <div class="col-md-3 user_status">
                     <label class="form-label" for="first_name">Filter By Status</label>
                     <select name="user_status" id="user-status" class="form-select text-capitalize">
                        <option value=""> Select Status </option>
                        <option value="Active"> Active </option>
                        <option value="Inactive"> Inactive </option>
                     </select>
                  </div>
                  <div class="col-md-3 user_deleted">
                     <label class="form-label" for="first_name">Filter By User Suspended?</label>
                     <select name="user_suspended" id="user-suspended" class="form-select text-capitalize">
                        <option value=""> Select Type </option>
                        <option value="Yes"> Yes </option>
                        <option value="No"> No </option>
                     </select>
                  </div>
               </div>
            </div>
            <table class="users-table table border-top">
               <thead>
                  <tr>
                     <th></th>
                     <th>#</th>
                     <th>Name</th>
                     <th>Email</th>
                     <th class="text-center">Status</th>
                     <th class="text-center">Confirmed</th>
                     <th class="text-center">Suspended</th>
                     <th class="text-center">Action</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($getUsers as $key => $user)
                     <tr>
                        <td class="text-center" width="10"></td>
                        <td class="text-center" width="10">{{ $key+1 }}</td>
                        <td class="text-center">
                           <div class="d-flex justify-content-start align-items-center user-name">
                              <div class="avatar-wrapper">
                                 <div class="avatar me-2">
                                    @if( !empty($user->avatar_location) )
                                       <img src="{{asset('storage/')}}/{{$user->avatar_location}}" alt="Avatar" class="rounded-circle">
                                    @endif
                                 </div>
                              </div>
                              <div class="d-flex flex-column">
                                 <span class="emp_name fw-medium text-truncate">{{ $user->full_name }}</span>
                              </div>
                           </div>
                        </td>
                        <td class="">{{ $user->email }}</td>
                        <td class="text-center">
                           <span class="badge  {{ $user->active == 1 ? 'bg-label-success' : 'bg-label-danger' }}">{{ $user->active == 1 ? __('Active') : __('Inactive') }}</span>
                        </td>
                        <td class="text-center">
                           <span class="badge  {{ $user->confirmed == 1 ? 'bg-label-success' : 'bg-label-danger' }}">{{ $user->confirmed == 1 ? __('Approved') : __('Pending') }}</span>
                        </td>
                        <td class="text-center">
                           <span class="badge  {{ !empty($user->deleted_at) ? 'bg-label-danger' : 'bg-label-success' }}">{{ !empty($user->deleted_at) ? __('Yes') : __('No') }}</span>
                        </td>
                        <td class="text-center">
                          <form action="{{ route('administrator.backend_destroy_user', ['delete_id' => $user->id]) }}" method="POST" id="confirm_delete">
                              @csrf
                              @method('DELETE')
                              <a href="{{ route('administrator.backend_edit_user',['id' => $user->uuid]) }}" class="btn btn-icon btn-outline-primary"  data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="left" data-bs-custom-class="tooltip-primary" data-bs-html="true" data-bs-original-title="Edit">
                                  <span class="tf-icons bx bx-edit-alt"></span>
                              </a>

                              <button type="button" class="btn btn-icon btn-outline-danger show-alert-delete-box" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="right" data-bs-custom-class="tooltip-danger" data-bs-html="true" data-bs-original-title="Delete">
                                  <span class="tf-icons bx bx-trash-alt"></span>
                              </button>
                          </form>
                        </td>
                     </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
      </div>
   </section>
</div>
@endsection
@push('after-scripts')
<script src="{{ asset('Backend/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('Backend/assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('Backend/assets/js/extended-ui-sweetalert2.js') }}"></script>
<script src="{{ asset('Backend/assets/vendor/libs/toastr/toastr.js') }}"></script>
<script src="{{ asset('js/backend/auth/user-management.js') }}"></script>
<script type = "text/javascript">
   // Variable declarations
   var csrfToken = "{{ csrf_token() }}";
   var importUsersRoute = "{{ route('administrator.backend_import_users') }}";
   var btnImporting = "{{ __('Importing Users') }}";
   var btnImport = "{{ __('Import Users') }}";
   var alertTitle = "{{ __('Be Aware!') }}";
   var alertDesc = "{{ __('Do not refresh this page or do any action from table until the import process are finish, Once import process will be done then alert will automatically destroy.') }}";

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

   $('.users-table').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "language": {
         'emptyTable': "No Users records",
         'paginate': {
            'previous': "Previous",
            'next': "Next",
         }
      },
      "dom": '<"row mx-2"<"col-md-2"<"me-3"l>><"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>>t<"row mx-2"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
      "language": {
         "sLengthMenu": "_MENU_",
         "search": "",
         "searchPlaceholder": "{{ __('Search..') }}",
         'emptyTable': "{{ __('No Users records') }}",
         'paginate': {
            'previous': "Previous",
            'next': "Next",
         }
      },
      "buttons": [{
         "text": '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">{{ __('Add New User') }}</span>',
         "className": "create-user btn btn-primary mx-2",
         "attr": {
            "data-bs-toggle": "offcanvas",
            "data-bs-target": "#offcanvasAddUser"
         },
         action: function ( e, dt, node, config ) {
            window.location.href = "{{ route('administrator.backend_create_user') }}";
         }
      },{
         "text": '<i class="fas fa-download me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">{{ __('Import Users') }}</span>',
         "className": "import-users btn btn-dark",
      }],
      "responsive": {
         "details": {
            "display": $.fn.dataTable.Responsive.display.modal({
               header: function(e) {
                  return "Details of " + e.data().full_name
               }
            }),
            "type": "column",
            renderer: function(e, t, a) {
               a = $.map(a, function(e, t) {
                  return "" !== e.title ? '<tr data-dt-row="' + e.rowIndex + '" data-dt-column="' + e.columnIndex + '"><td>' + e.title + ":</td> <td>" + e.data + "</td></tr>" : ""
               }).join("");
               return !!a && $('<table class="table"/><tbody />').append(a)
            }
         }
      },
   });

   // Remove class from DT Default search and page shorting boxes
   setTimeout(() => {
      $(".dataTables_filter .form-control").removeClass("form-control-sm"), $(".dataTables_length .form-select").removeClass("form-select-sm");
   }, 100);

   // Filter for User Status
   $( document ).on('change', '#user-status', function(){
      var status = $(this).val();
      if( status ) {
         $('.users-table').DataTable().column(4).search('\\b' + $.fn.dataTable.util.escapeRegex(status) + '\\b', true, false).draw();
      } else {
         $('.users-table').DataTable().column(4).search('').draw();
      }
   });

   // Filter for User Suspended
   $( document ).on('change', '#user-suspended', function(){
      var status = $(this).val();
      if( status ) {
         $('.users-table').DataTable().column(6).search('\\b' + $.fn.dataTable.util.escapeRegex(status) + '\\b', true, false).draw();
      } else {
         $('.users-table').DataTable().column(6).search('').draw();
      }
   });
</script>
@endpush