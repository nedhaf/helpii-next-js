$(document).ready(function(){
    // Skill list Select2 Dropdown
    $('#skill-lists').select2({
        placeholder: "Select skill",
        allowClear: true,
        closeOnSelect: true,
        width: 'resolve'
    });

    // Start date datepicker
    var from = $("#start_date").datepicker({
        language: 'sv',
        format: 'yyyy-mm-dd',
        autoclose: true,
        startDate: new Date(),
    }).on( "changeDate", function(selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#end_date').datepicker('setStartDate', minDate);
    });

    // End date datepicker
    var to = $("#end_date").datepicker({
        language: 'sv',
        format: 'yyyy-mm-dd',
        autoclose: true,
    }).on( "changeDate", function(selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#start_date').datepicker('setEndDate', minDate);
    });

    // Advertisement Image
    let ie = document.getElementById("uploadedAdvrtImg");
    const il = document.querySelector(".img-file-input"), ic = document.querySelector(".img-image-reset");
    if (ie) {
        const r = ie.src;
        il.onchange = () => {
            il.files[0] && (ie.src = window.URL.createObjectURL(il.files[0]))
            ic.disabled = false;
        }, ic.onclick = () => {
            il.value = "", ic.disabled = true;

            if( il.value == "") {
                ie.src = defaultImg;
            }
        }
    }

    // Badge Image
    var defaultImg = $('#default_image_path').val();
    let e = document.getElementById("uploadedBadgeImg");
    const l = document.querySelector(".badge-file-input"), c = document.querySelector(".badge-image-reset");
    if (e) {
        const r = e.src;
        l.onchange = () => {
            l.files[0] && (e.src = window.URL.createObjectURL(l.files[0]))
            c.disabled = false;
        }, c.onclick = () => {
            l.value = "", c.disabled = true;

            if( l.value == "") {
                e.src = defaultImg;
            }
        }
    }

});