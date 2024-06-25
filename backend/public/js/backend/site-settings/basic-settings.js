$(document).ready(function(){

    var defaultImg = $('#default_image_path').val();
    // Site Logo
    let ie = document.getElementById("uploadedSiteLogo");
    const il = document.querySelector(".logo-file-input"), ic = document.querySelector(".logo-image-reset");
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

    // Site Background
    let sb = document.getElementById("uploadedSiteBackground");
    const sbil = document.querySelector(".background-file-input"), sbic = document.querySelector(".background-image-reset");
    if (sb) {
        const r = sb.src;
        sbil.onchange = () => {
            sbil.files[0] && (sb.src = window.URL.createObjectURL(sbil.files[0]))
            sbic.disabled = false;
        }, sbic.onclick = () => {
            sbil.value = "", sbic.disabled = true;

            if( sbil.value == "") {
                sb.src = defaultImg;
            }
        }
    }

    // Site Tags
    var input = document.querySelector('input[name=tag]');
    new Tagify(input)
});