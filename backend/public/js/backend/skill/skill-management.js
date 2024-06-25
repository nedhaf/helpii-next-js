$(document).ready(function(){
});

$('.skill-import-progress').hide();
// Import Skill
$(document).on('click', '.import-skill', function(e){
    $.ajax({
        url: importSkillsRoute,
        type: 'post',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the headers
        },
        beforeSend: function () {
            console.log("Before send");
            var ProccessHtml = `<div class="alert alert-info d-flex" role="alert">
               <span class="badge badge-center rounded-pill bg-info border-label-info p-3 me-2"><i class="fas fa-spinner fa-spin fs-4"></i></span>
               <div class="d-flex flex-column ps-1">
                  <h6 class="alert-heading d-flex align-items-center mb-1"><strong>Badges are Importing</strong></h6>
                  <span class="text-danger">Please do not refresh the page or make any operation in below table until progress are not complete.</span>
               </div>
            </div>`;
            $('.skill-import-progress-section').html(ProccessHtml);
        },
        success: function (data) {
            if( data.status ) {
                Swal.fire({
                    title: 'Skills Import',
                    text: "Skills are imported from live site please click OK button.",
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#873d8f',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            }
        },
        error: function (data) {

        },
        complete: function () {
            console.log("Process Complete");
            $('.skill-import-progress-section div').remove();
        }
    });
});

// Delete Skill
$(document).on('click','.show-alert-delete-box',function(event){
    var form =  $(this).closest("form");
    var name = $(this).data("name");
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure you want to delete this skill?',
        text: "You won't be able to revert this skill!",
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