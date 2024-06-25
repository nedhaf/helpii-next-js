@extends('backend.layouts.administrator-app')

@section('head-title', 'Advertisements')


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
<!-- Advertisement List -->
<div class="row">
   <section class="col-lg-12">
      <div class="card">
         <div class="card-datatable table-responsive">
            <div class="card-header border-bottom">
               <h5 class="card-title">Search Filter</h5>
               <div class="d-flex align-items-center row py-3 gap-3 gap-md-0">
                  <div class="col-md-3 skills_list">
                     <label class="form-label" for="skills">Filter By Skill</label>
                     <select name="skills" id="skills" class="form-select text-capitalize">
                        <option value=""> Select Skill </option>
                        @if( !empty( $getSkills ) )
                           @foreach( $getSkills as $k => $skill )
                              <option value="{{ $skill->name }}">{{ $skill->name }}</option>
                           @endforeach
                        @endif
                     </select>
                  </div>
               </div>
            </div>
            <table class="advertisements-table table border-top">
               <thead>
                  <tr>
                     <th width="15">#</th>
                     <th>Ad Title</th>
                     <th class="text-center">Skill</th>
                     <th class="text-center">Cost</th>
                     <th class="">Appearing Type</th>
                     <th class="text-center">Status</th>
                     <th class="text-center">Action</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($getAdvertisements as $key => $advertisement)
                     <tr>
                        <td class="text-center" width="10">{{ $key+1 }}</td>
                        <td class="text-center">
                           <div class="d-flex justify-content-start align-items-center user-name">
                              <div class="avatar-wrapper">
                                 <div class="avatar me-2">
                                    @if( !empty($advertisement->badge_img) )
                                       <img src="{{asset('storage/advertisement/badgeImg/'.$advertisement->badge_img)}}" alt="{{ $advertisement->title }}" class="rounded-circle">
                                    @endif
                                 </div>
                              </div>
                              <div class="d-flex flex-column">
                                 <span class="emp_name fw-medium text-truncate">{{ $advertisement->title }}</span>
                              </div>
                           </div>
                        </td>
                        <td class="text-center">{{ $advertisement->getSkill->name }}</td>
                        <td class="text-center">{{ $advertisement->cost }}</td>
                        <td class="" >
                           <div class="appearing_types_td appearing_types_td_{{ $advertisement->id }}">
                              <div class="">
                                 <input name="show_in_front_profile" class="form-check-input show_in_front_profile {{ !empty($IsFrontProfile) && $advertisement->show_in_front_profile == 0 ? 'isFrontProfileAlready' : 'makeFrontProfile' }}" type="checkbox" value="{{ $advertisement->show_in_front_profile == 1 ? 1 : 0 }}" id="show_in_front_profile_{{ $advertisement->id }}" data-id="{{ $advertisement->id }}" {{ $advertisement->show_in_front_profile == 1 ? 'checked' : '' }} {{ $advertisement->status == 0 ? 'disabled' : '' }}>
                                 <label class="form-check-label" for="show_in_front_profile_{{ $advertisement->id }}">Show in front profile</label>
                              </div>

                              <div class="">
                                 <input name="show_in_front_ads" class="form-check-input show_in_front_ads {{ !empty($IsFrontAds) && $advertisement->show_in_front_ads == 0 ? 'isFrontAdsAlready' : 'makeFrontAds' }}" type="checkbox" value="{{ $advertisement->show_in_front_ads == 1 ? 1 : 0 }}" id="show_in_front_ads_{{ $advertisement->id }}" data-id="{{ $advertisement->id }}" {{ $advertisement->show_in_front_ads == 1 ? 'checked' : '' }} {{ $advertisement->status == 0 ? 'disabled' : '' }}>
                                 <label class="form-check-label" for="show_in_front_ads_{{ $advertisement->id }}">Show in front ads</label>
                              </div>
                              {{-- @if( $advertisement->status == 0 )
                                 <div id="action-warning-{{ $advertisement->id }}" class="ads-checkbox-action form-text text-danger text-wrap">If you want to take action for this data then please make status <mark>Active</mark> from <mark>Status</mark> column.</div>
                              @endif --}}
                           </div>
                        </td>
                        <td class="">
                           <label class="switch switch-lg">
                              <input type="checkbox" class="switch-input ads-status-update {{ $advertisement->status == 1 ? 'isActivated' : 'isInactivated' }}" type="checkbox" id="ads-status-{{ $advertisement->id }}" value="{{ $advertisement->status == 1 ? 1 : 0 }}" {{ $advertisement->status == 1 ? 'checked' : '' }} data-id="{{ $advertisement->id }}">
                              <span class="switch-toggle-slider">
                                 <span class="switch-on">
                                    <i class="bx bx-check"></i>
                                 </span>
                                 <span class="switch-off">
                                    <i class="bx bx-x"></i>
                                 </span>
                              </span>
                           </label>
                        </td>
                        <td class="text-center">
                          <form action="" method="POST" id="confirm_delete">
                              @csrf
                              @method('DELETE')
                              <a href="{{ route('administrator.backend_advertisement_edit', ['id' => $advertisement->id]) }}" class="btn btn-icon btn-outline-primary"  data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="left" data-bs-custom-class="tooltip-primary" data-bs-html="true" data-bs-original-title="Edit">
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
<script src="{{ asset('js/backend/advertisements/advertisements-list.js') }}"></script>
<script type = "text/javascript">

   // Variable declarations
   var csrfToken = "{{ csrf_token() }}";
   var adminFrontProfileAdvertisementRoute = "{{ route('administrator.admin-front-profile-advertisement') }}";
   var adminFrontAdsAdvertisementRoute = "{{ route('administrator.admin-front-ads-advertisement') }}";
   var updateAdsStatusRoute = "{{ route('administrator.backend_ads_update_status') }}";

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

   $('.advertisements-table').DataTable({
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "language": {
         'emptyTable': "No Advertisements records",
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
         'emptyTable': "{{ __('No Advertisements records') }}",
         'paginate': {
            'previous': "Previous",
            'next': "Next",
         }
      },
      "buttons": [{
         "text": '<i class="bx bx-plus me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">{{ __('Add New Advertisement') }}</span>',
         "className": "create-user btn btn-primary mx-2",
         "attr": {
            "data-bs-toggle": "offcanvas",
            "data-bs-target": "#offcanvasAddUser"
         },
         action: function ( e, dt, node, config ) {
            window.location.href = "{{ route('administrator.backend_advertisement_create') }}";
         }
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

</script>
@endpush