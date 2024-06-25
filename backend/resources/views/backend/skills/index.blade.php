@extends('backend.layouts.administrator-app')

@section('head-title', 'Skills')

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
<!-- Users List -->
<div class="row">
    <section class="col-lg-10 offset-lg-1">
      <div class="skill-import-progress-section"></div>
        <div class="card">
            <div class="card-datatable table-responsive">
                <table class="skills-table table border-top">
                    <thead>
                        <tr>
                            <th width="15">#</th>
                            <th>Name</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($getSkills as $key => $skill)
                           <tr>
                              <td class="text-center" width="10">{{ $key+1 }}</td>
                              <td class="text-center">
                                 <div class="d-flex justify-content-start align-items-center user-name">
                                    <div class="avatar-wrapper">
                                       <div class="avatar avatar-md me-2">
                                          @if( !empty($skill->avatar) )
                                             <img src="{{asset('storage/skills/')}}/{{$skill->avatar}}" alt="Avatar" class="rounded-circle">
                                          @endif
                                       </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                       <span class="emp_name fw-medium text-truncate">{{ $skill->name }}</span>
                                    </div>
                                 </div>
                              </td>
                              <td class="text-center">
                                 <span class="badge  {{ $skill->status == 1 ? 'bg-label-success' : 'bg-label-danger' }}">{{ $skill->status == 1 ? __('Active') : __('Inactive') }}</span>
                              </td>
                              <td class="text-center">
                                 <form action="{{ route('administrator.backend_destroy_skill', ['id' => $skill->id]) }}" method="POST" id="confirm_delete">
                                    @csrf
                                    @method('DELETE')
                                    <a href="{{ route('administrator.backend_skill_edit', ['id' => $skill->id]) }}" class="btn btn-icon btn-outline-primary"  data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="left" data-bs-custom-class="tooltip-primary" data-bs-html="true" data-bs-original-title="Edit">
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
<script src="{{ asset('js/backend/skill/skill-management.js') }}"></script>
<script type = "text/javascript">
   // Variable declarations
   var csrfToken = "{{ csrf_token() }}";
   var importSkillsRoute = "{{ route('administrator.backend_import_skill') }}";

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

   $('.skills-table').DataTable({
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
         'emptyTable': "{{ __('No Skills found') }}",
         'paginate': {
            'previous': "Previous",
            'next': "Next",
         }
      },
      "buttons": [{
         "text": '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">{{ __('Add New Skill') }}</span>',
         "className": "create-skill btn btn-primary mx-2",
         "attr": {
            "data-bs-toggle": "offcanvas",
            "data-bs-target": "#offcanvasAddUser"
         },
         action: function ( e, dt, node, config ) {
            window.location.href = "{{ route('administrator.backend_skill_create') }}";
         }
      },
      // {
      //    "text": '<i class="fas fa-download me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">{{ __('Import Skills') }}</span>',
      //    "className": "import-skill btn btn-dark",
      // }
      ],
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
      // initComplete: function() {
      //    this.api().columns(5).every(function() {
      //       var t = this,
      //          a = $('<select id="FilterTransaction" class="form-select text-capitalize"><option value=""> Select Status </option></select>').appendTo(".user_status").on("change", function() {
      //             var e = $.fn.dataTable.util.escapeRegex($(this).val());
      //             t.search(e ? "^" + e + "$" : "", !0, !1).draw()
      //          });
      //       t.data().unique().sort().each(function(e, t) {
      //          a.append('<option value="' + l[e].title + '" class="text-capitalize">' + l[e].title + "</option>")
      //       })
      //    })
      // }initComplete: function() {
      //    this.api().columns(5).every(function() {
      //       var t = this,
      //          a = $('<select id="FilterTransaction" class="form-select text-capitalize"><option value=""> Select Status </option></select>').appendTo(".user_status").on("change", function() {
      //             var e = $.fn.dataTable.util.escapeRegex($(this).val());
      //             t.search(e ? "^" + e + "$" : "", !0, !1).draw()
      //          });
      //       t.data().unique().sort().each(function(e, t) {
      //          a.append('<option value="' + l[e].title + '" class="text-capitalize">' + l[e].title + "</option>")
      //       })
      //    })
      // }
   });
   setTimeout(() => {
      $(".dataTables_filter .form-control").removeClass("form-control-sm"),
      $(".dataTables_length .form-select").removeClass("form-select-sm")
   }, 100)
</script>
@endpush