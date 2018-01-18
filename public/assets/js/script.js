function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
}

$(document).ready(function() {

    $('select').select2();

    $('textarea').summernote({
        tabsize: 2,
        height: 400
    });

    $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd',  minDate: "0"  });


    $('.datatable').DataTable();



timeago().render($('.time_ago'));


});

$("#add_participant").click(function () {
    $("#training_form_participants").append("<div class='bg-gray p-3 mb-3'> <div class='form-group'> <label>Participant name</label> <input type='text' class='form-control' required='required' name='participants[]'> </div> <div class='form-group'> <label>Participant email</label> <input type='email' class='form-control' required='required' name='participants_email[]'> </div> <div class='form-group'> <label>Participant phone</label> <input type='text' class='form-control' onkeypress='return isNumber(event)' name='participants_phone[]'></div></div>");
});



function printContent(el){
    var restorepage = $('body').html();
    var printcontent = $('#' + el).clone();
    $('body').empty().html(printcontent);
    window.print();
    $('body').html(restorepage);
}


function printElem(divId) {
    var content = document.getElementById(divId).innerHTML;
    var mywindow = window.open('', 'Print', 'height=600,width=800');

    mywindow.document.write('<html><head><title>Print</title>');
    mywindow.document.write('<link rel="stylesheet" href="../../../assets/css/bootstrap.min.css" media="screen, print" type="text/style">');
    //mywindow.document.write('<link rel="stylesheet" href="../../../assets/look_css/css/look_base_v2.css" type="text/style">');
    mywindow.document.write('<link rel="stylesheet" href="../../../assets/css/style.css" type="text/style">');
    mywindow.document.write('</head><body >');
    mywindow.document.write(content);
    mywindow.document.write('</body></html>');

    mywindow.document.close();
    mywindow.focus()
    mywindow.print();
    //mywindow.close();
    return true;
}


 toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": true,
  "progressBar": false,
  "positionClass": "toast-top-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
};