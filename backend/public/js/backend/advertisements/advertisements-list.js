$(document).ready(function() {
    // Remove class from DT Default search and page shorting boxes
    setTimeout(() => {
        $(".dataTables_filter .form-control").removeClass("form-control-sm"), $(".dataTables_length .form-select").removeClass("form-select-sm");
    }, 100);

    // Filter for Skill
    $(document).on('change', '#skills', function() {
        var status = $(this).val();
        if (status) {
            $('.advertisements-table').DataTable().column(2).search('\\b' + $.fn.dataTable.util.escapeRegex(status) + '\\b', true, false).draw();
        } else {
            $('.advertisements-table').DataTable().column(2).search('').draw();
        }
    });

    //Set value in chekbox input
    $('.show_in_front_profile, .show_in_front_ads, .ads-status-update').on('change', function() {
        if ($(this).is(':checked')) {
            $(this).val('1'); // Set the value to 1 when checked
        } else {
            $(this).val('0'); // Set the value to 0 when unchecked
        }
    });

    // update ads status with alert
    $(document).on('click', '.ads-status-update', function(event){
        event.preventDefault();

        var _this = $(this);
        var ads_id = $(this).data('id');
        var clickedCheckbox = $('#ads-status-' + ads_id);
        var value = $('#ads-status-' + ads_id).val();

        if( _this.hasClass('isActivated') ) {
            Swal.fire({
                title: "Do you want to inactive this ads?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#873d8f',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, inactivate it!'
            }).then((result) => {
                if( result.isConfirmed ){
                    $.ajax({
                        url: updateAdsStatusRoute,
                        type: 'post',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the headers
                        },
                        data : {
                            'ads_id'    : ads_id,
                            'value'     : value
                        },
                        success: function(data) {
                            if( data.results == 'success' ) {
                                var currentValue = clickedCheckbox.val();

                                if (value === '0') {
                                    clickedCheckbox.val(1);
                                    clickedCheckbox.prop('checked', true);
                                } else {
                                    clickedCheckbox.val(0);
                                    clickedCheckbox.prop('checked', false);
                                }

                                // Toggle the 'isFrontProfileAlready' class
                                clickedCheckbox.removeClass('isActivated');
                                clickedCheckbox.addClass('isInactivated');

                                $('#show_in_front_profile_'+ads_id).prop('disabled', true);
                                $('#show_in_front_ads_'+ads_id).prop('disabled', true);

                                // clickedCheckbox.prop('disabled', true);
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
                                toastr.success(data.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle errors
                            console.error(error);
                        }
                    });
                }
            });
        } else {
            Swal.fire({
                title: "Do you want to active this ads?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#873d8f',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, activate it!'
            }).then((result) => {
                if( result.isConfirmed ){
                    $.ajax({
                        url: updateAdsStatusRoute,
                        type: 'post',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the headers
                        },
                        data : {
                            'ads_id'    : ads_id,
                            'value'     : value
                        },
                        success: function(data) {
                            if( data.results == 'success' ) {
                                var currentValue = clickedCheckbox.val();

                                if (value === '0') {
                                    clickedCheckbox.val(1);
                                    clickedCheckbox.prop('checked', true);
                                } else {
                                    clickedCheckbox.val(0);
                                    clickedCheckbox.prop('checked', false);
                                }

                                // Toggle the 'isFrontProfileAlready' class
                                clickedCheckbox.addClass('isActivated');
                                clickedCheckbox.removeClass('isInactivated');

                                $('#show_in_front_profile_'+ads_id).prop('disabled', false);
                                $('#show_in_front_ads_'+ads_id).prop('disabled', false);
                                // clickedCheckbox.prop('disabled', true);

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
                                toastr.success(data.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle errors
                            console.error(error);
                        }
                    });
                }
            });
        }
    });

    // Make Ad for Profile page checkboxes alerts
    $(document).on('click','.show_in_front_profile',function(event){
        event.preventDefault();
        var form =  $(this).closest("form");
        var _this = $(this);
        var ads_id = $(this).data('id');
        var clickedCheckbox = $('#show_in_front_profile_' + ads_id);

        if( _this.hasClass('isFrontProfileAlready') ) {
            Swal.fire({
                title: "Are you sure to activate this ad on the profile's front page, because another ad is already an existing ad in place?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#873d8f',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, activate it!'
            }).then((result) => {
                console.log('Result', result);
                if( result.isConfirmed ){
                    $.ajax({
                        url: adminFrontProfileAdvertisementRoute,
                        type: 'post',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the headers
                        },
                        data : {
                            'ads_id' : ads_id
                        },
                        success: function(data) {
                            if( data.results == 'success' ) {
                                window.location.reload();
                                var currentValue = clickedCheckbox.val();
                                if (currentValue === '0') {
                                    clickedCheckbox.val(1);
                                    clickedCheckbox.prop('checked', true);
                                } else {
                                    clickedCheckbox.val(0);
                                    clickedCheckbox.prop('checked', false);
                                }

                                // Toggle the 'isFrontProfileAlready' class
                                clickedCheckbox.toggleClass('isFrontProfileAlready');
                                clickedCheckbox.prop('disabled', true);

                                // Uncheck all other checkboxes with the same class and remove the class
                                $('.show_in_front_profile:checked').not(clickedCheckbox).prop('checked', false);
                                $('.show_in_front_profile:checked').not(clickedCheckbox).prop('disabled', false);
                                $('.show_in_front_profile').not(clickedCheckbox).val(0);
                                $('.show_in_front_profile:checked').not(clickedCheckbox).addClass('isFrontProfileAlready');
                                $('.isFrontProfileAlready').not(clickedCheckbox).removeClass('isFrontProfileAlready');
                            } else {
                                console.error(data.errors);
                            }
                        },
                        error: function(xhr, status, error) {
                            // Handle errors
                            console.error(error);
                        }
                    });
                }
            })
        } else {
            if( _this.hasClass('makeFrontProfile') ) {
                Swal.fire({
                    title: "Do you want to active this seleceted ad on the profile's front page?",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#873d8f',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, activate it!'
                }).then((result) => {
                    if( result.isConfirmed ){
                        $.ajax({
                            url: adminFrontProfileAdvertisementRoute,
                            type: 'post',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the headers
                            },
                            data : {
                                'ads_id' : ads_id
                            },
                            success: function(data) {
                                if( data.results == 'success' ) {
                                    window.location.reload();
                                    var currentValue = clickedCheckbox.val();
                                    if (currentValue === '0') {
                                        clickedCheckbox.val(1);
                                        clickedCheckbox.prop('checked', true);
                                    } else {
                                        clickedCheckbox.val(0);
                                        clickedCheckbox.prop('checked', false);
                                    }

                                    // Toggle the 'isFrontProfileAlready' class
                                    clickedCheckbox.toggleClass('isFrontProfileAlready');
                                    clickedCheckbox.prop('disabled', true);

                                    // Uncheck all other checkboxes with the same class and remove the class
                                    $('.show_in_front_profile:checked').not(clickedCheckbox).prop('checked', false);
                                    $('.show_in_front_profile:checked').not(clickedCheckbox).prop('disabled', false);
                                    $('.show_in_front_profile').not(clickedCheckbox).val(0);
                                    $('.show_in_front_profile:checked').not(clickedCheckbox).addClass('isFrontProfileAlready');
                                    $('.isFrontProfileAlready').not(clickedCheckbox).removeClass('isFrontProfileAlready');
                                }  else {
                                    console.error(data.errors);
                                }
                            },
                            error: function(xhr, status, error) {
                                // Handle errors
                                console.error(error);
                            }
                        });
                    }
                });
            }
        }
    });

    // Make Ad for Ads page checkboxes alerts
    $(document).on('click', '.show_in_front_ads', function(e){
        var _this = $(this);
        var ads_id = $(this).data('id');
        var clickedCheckbox = $('#show_in_front_ads_' + ads_id);

        if( _this.hasClass('isFrontAdsAlready') ) {
            Swal.fire({
                title: "Are you sure to activate this ad on the ads's front page, because another ad is already an existing ad in place?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#873d8f',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, activate it!'
            }).then((result) => {
                if( result.isConfirmed ){
                    $.ajax({
                        url: adminFrontAdsAdvertisementRoute,
                        type: 'post',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the headers
                        },
                        data : {
                            'ads_id' : ads_id
                        },
                        success: function(data) {
                            console.log(data.results);
                            if( data.results == 'success' ) {
                                window.location.reload();
                                var currentValue = clickedCheckbox.val();

                                if (currentValue === '0') {
                                    clickedCheckbox.val(1);
                                    clickedCheckbox.prop('checked', true);
                                } else {
                                    clickedCheckbox.val(0);
                                    clickedCheckbox.prop('checked', false);
                                }

                                // Toggle the 'isFrontAdsAlready' class
                                clickedCheckbox.toggleClass('isFrontAdsAlready');
                                clickedCheckbox.prop('disabled', true);

                                // Uncheck all other checkboxes with the same class and remove the class
                                $('.show_in_front_ads:checked').not(clickedCheckbox).prop('checked', false);
                                $('.show_in_front_ads:checked').not(clickedCheckbox).prop('disabled', false);
                                $('.show_in_front_ads').not(clickedCheckbox).val(0);
                                $('.show_in_front_ads:checked').not(clickedCheckbox).addClass('isFrontAdsAlready');
                            } else {
                                console.error(data.errors);
                            }
                        },
                        error: function(xhr, status, error) {}
                    });
                }
            });
        } else {
            if( _this.hasClass('makeFrontAds') ) {
                Swal.fire({
                    title: "Do you want to active this seleceted ad on the ads's front page?",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#873d8f',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, activate it!'
                }).then((result) => {
                    if( result.isConfirmed ){
                        $.ajax({
                            url: adminFrontAdsAdvertisementRoute,
                            type: 'post',
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the headers
                            },
                            data : {
                                'ads_id' : ads_id
                            },
                            success: function(data) {
                                if( data.results == 'success' ) {
                                    window.location.reload();
                                    var currentValue = clickedCheckbox.val();
                                    if (currentValue === '0') {
                                        clickedCheckbox.val(1);
                                        clickedCheckbox.prop('checked', true);
                                    } else {
                                        clickedCheckbox.val(0);
                                        clickedCheckbox.prop('checked', false);
                                    }
                                    // Toggle the 'isFrontProfileAlready' class
                                    clickedCheckbox.toggleClass('isFrontAdsAlready');
                                    clickedCheckbox.prop('disabled', true);
                                    // Uncheck all other checkboxes with the same class and remove the class
                                    $('.show_in_front_ads:checked').not(clickedCheckbox).prop('checked', false);
                                    $('.show_in_front_ads:checked').not(clickedCheckbox).prop('disabled', false);
                                    $('.show_in_front_ads').not(clickedCheckbox).val(0);
                                    $('.show_in_front_ads:checked').not(clickedCheckbox).addClass('isFrontAdsAlready');
                                } else {
                                    console.error(data.errors);
                                }
                            },
                            error: function(xhr, status, error) {
                                // Handle errors
                                console.error(error);
                            }
                        });
                    }
                });
            }
        }
    });
});