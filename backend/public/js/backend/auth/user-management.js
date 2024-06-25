$(document).ready(function(){
    // Google map place JS
    // function initialize() {

    //     $('form').on('keyup keypress', function(e) {
    //         var keyCode = e.keyCode || e.which;
    //         if (keyCode === 13) {
    //             e.preventDefault();
    //             return false;
    //         }
    //     });
    //     const locationInputs = document.getElementsByClassName("map-input");

    //     const autocompletes = [];
    //     const geocoder = new google.maps.Geocoder;
    //     for (let i = 0; i < locationInputs.length; i++) {

    //         const input = locationInputs[i];
    //         const fieldKey = input.id.replace("-input", "");
    //         // const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';
    //         const isEdit = document.getElementById("latitude").value != '' && document.getElementById("longitude").value != '';

    //         // const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || -33.8688;
    //         // const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 151.2195;
    //         const latitude = parseFloat(document.getElementById("latitude").value) || -33.8688;
    //         const longitude = parseFloat(document.getElementById("longitude").value) || 151.2195;

    //         // const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
    //         // const map = new google.maps.Map(document.getElementById('map'), {
    //         //     center: {lat: latitude, lng: longitude},
    //         //     zoom: 13
    //         // });
    //         // const marker = new google.maps.Marker({
    //         //     map: map,
    //         //     position: {lat: latitude, lng: longitude},
    //         // });

    //         // marker.setVisible(isEdit);

    //         const autocomplete = new google.maps.places.Autocomplete(input);
    //         autocomplete.key = fieldKey;
    //         autocompletes.push({input: input, map: map, marker: marker, autocomplete: autocomplete});
    //     }

    //     for (let i = 0; i < autocompletes.length; i++) {
    //         const input = autocompletes[i].input;
    //         const autocomplete = autocompletes[i].autocomplete;
    //         const map = autocompletes[i].map;
    //         const marker = autocompletes[i].marker;

    //         google.maps.event.addListener(autocomplete, 'place_changed', function () {
    //             marker.setVisible(false);
    //             const place = autocomplete.getPlace();

    //             geocoder.geocode({'placeId': place.place_id}, function (results, status) {
    //                 if (status === google.maps.GeocoderStatus.OK) {
    //                     const lat = results[0].geometry.location.lat();
    //                     const lng = results[0].geometry.location.lng();
    //                     setLocationCoordinates(autocomplete.key, lat, lng);
    //                 }
    //             });

    //             if (!place.geometry) {
    //                 window.alert("No details available for input: '" + place.name + "'");
    //                 input.value = "";
    //                 return;
    //             }

    //             if (place.geometry.viewport) {
    //                 map.fitBounds(place.geometry.viewport);
    //             } else {
    //                 map.setCenter(place.geometry.location);
    //                 map.setZoom(17);
    //             }
    //             marker.setPosition(place.geometry.location);
    //             marker.setVisible(true);

    //         });
    //     }
    // }

    // function setLocationCoordinates(key, lat, lng) {
    //     const latitudeField = document.getElementById("latitude");
    //     const longitudeField = document.getElementById("longitude");
    //     latitudeField.value = lat;
    //     longitudeField.value = lng;
    // }
});

// Suspend SweetAlert Box
$(document).on('click','.show-alert-suspend-box',function(event){
    var form =  $(this).closest("form");
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure you want to suspend this user?',
        text: "You can revert this user by click on Rise button!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#873d8f',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, suspend it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    })
});

// Rise SweetAlert Box
$(document).on('click','.show-alert-rise-box',function(event){
    var form =  $(this).closest("form");
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure you want to retrive this user?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#873d8f',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, retrive it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    })
});

// Delete User
$(document).on('click','.show-alert-delete-box',function(event){
    var form =  $(this).closest("form");
    var name = $(this).data("name");
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure you want to delete this user?',
        text: "You won't be able to revert this user!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#873d8f',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    })
});

// Import Users
// Import Skill
$(document).on('click', '.import-users', function(e){
    $.ajax({
        url: importUsersRoute,
        type: 'post',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the headers
        },
        beforeSend: function () {
            $('.import-user-process-wrapper').html('<div class="alert alert-info d-flex" role="alert"><span class="badge badge-center rounded-pill bg-info border-label-info p-3 me-2"><i class="fas fa-file-import fs-5"></i></span><div class="d-flex flex-column ps-1"><h5 class="alert-heading d-flex align-items-center mb-1"><strong>'+alertTitle+'<strong></h5><span><strong>'+alertDesc+'</strong></span></div></div>');
            $('.import-users span').html('<i class="fas fa-spinner fa-spin me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">'+btnImporting+'</span>');
            $('.create-user').prop('disabled', true);
        },
        success: function (data) {

        },
        error: function (data) {

        },
        complete: function () {
            $('.import-user-process-wrapper').html('');
            $('.import-users span').html('<i class="fas fa-download me-0 me-sm-1"></i><span class="d-none d-sm-inline-block">'+btnImport+'</span>');
            $('.create-user').prop('disabled', false);
        }
    });
});