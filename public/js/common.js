
jQuery.fn.ForceNumericOnly = function() {
    return this.each(function() {
        $(this).keydown(function(e) {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            return (
                key == 8 ||
                key == 9 ||
                key == 13 ||
                key == 46 ||
                key == 110 ||
                key == 190 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105));
        });
    });
};

$('body').on('focus', "input[type='number']", function() {
    var tis = $(this);
    tis.ForceNumericOnly();
    tis.on('mousewheel', function(e) { $(this).blur(); });
    if (tis.val() < 0) {
        tis.val('0');
    }
});

function putMinToZerro() {
    $("input[type='number']").attr('min', '0');
}

/* ............for delete button start...................*/
$(document).on('submit', '.delete-form', function() {
    return confirm('Are you sure?');
});

/* ............for delete button end...................*/

function callOthers(selector) {
    var val = $(selector).val();
    if (val == 6) {
        $('#others').slideDown();
    } else {
        $('#others').slideUp();
    }

}

function callYes(selector) {
    var val = $(selector).val();
    if (val == 'yes') {
        $('#yes').slideDown();
    } else {
        $('#yes').slideUp();
    }

}

$(document).on('change', '.numbers', function() {
    var total = 0;
    $(".numbers").each(function(index) {
        var value = parseInt($(this).val());
        if (!isNaN(value)) {
            total += value;
        }
    });
    $('#total').val(total).change();
});

/* -- start User role permission check box true false control -- */
function permission_select_deselect_child(selector) {
    var check;
    if ($(selector).is(':checked') === false) {
        check = false;
    } else {
        check = true;
    }
    if ($(selector).parent().parent().hasClass('controller') === true) {
        var action_ul = $(selector).parent().parent().next('div.action-wraper');
        $.each(action_ul.children('.actions'), function(ind, val) {
            var cur_check_box = $(val).children().children('input');
            $(cur_check_box).prop('checked', check);
        });
    }
}

function permission_select_parent(selector) {
    var check = $('.' + selector).is(':checked');
    $('.parent_' + selector).prop('checked', check);
}
/* -- End User role permission check box true false control -- */

var radioChange = function(name, vall) {
    if (name == 'first_time' && vall == '1') {
        $("input[type='radio'][name='continue_next'][value='0']").prop('checked', true);
    } else if (name == 'first_time' && vall == '0') {
        $("input[type='radio'][name='continue_next'][value='1']").prop('checked', true);
    } else if (name == 'continue_next' && vall == '1') {
        $("input[type='radio'][name='first_time'][value='0']").prop('checked', true);
    } else {
        $("input[type='radio'][name='first_time'][value='1']").prop('checked', true);
    }
};

$(document).ready(function() {
    $('.glyphicon-edit').attr("title", "Edit");
    $('.glyphicon-edit').tooltip();

    $('.glyphicon-eye-open').attr("title", "View");
    $('.glyphicon-eye-open').tooltip();

    $('.glyphicon-plus').attr("title", "Add");
    $('.glyphicon-plus').tooltip();

    $('.glyphicon-send').parent().attr("title", "Send");
    $('.glyphicon-send').parent().tooltip();

    $('.glyphicon-remove').parent().attr("title", "Delete");
    $('.glyphicon-remove').parent().tooltip();

    putMinToZerro();

    /*All active link select for navigation*/
var parentAllClass = $(".in").attr("class")||'default';
if(parentAllClass!=='default'){
parentAllClass = parentAllClass.replace("panel-collapse collapse in ", "");
$("li#" + parentAllClass + ' a').css("color", "red");
}

});


// required validation for safari
$('#modalForm').click(function(e) {
    e.preventDefault();
    var sendModalForm = true;
    $('#modalForm [required]').each(function() {
        if ($(this).val() === '') {
            sendModalForm = false;
            alert("Required field should not be blank."); // or $('.error-message').show();
        }
    });

    if (sendModalForm) {
        this.form.submit();
    }
});

function readURL(input) {   
    var preview_location = $(input).parent().parent().find('.preview-div');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview_location.html('<img class="img-responsive" width="70" src="' + e.target.result + '">');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

$('body').on('click', ".add-data", function() {
    var index = $( ".add-data" ).index( this );
    $(".my-index").attr("value", index);
});

$('.add-icon').click(function(){ 
  var collection_img_path  = $(this).attr('src');
  var img = '<img width="30" src="'+collection_img_path+'">';
  var collection_index  = $('.my-index').attr('value'); 
  var filename = collection_img_path.split('/').pop();
  $( "div.icon-preview" ).eq(collection_index).html(img); 
  $( "input.icon-input" ).eq(collection_index).attr("value", filename);
});

$("form").submit(function(e) {

    var ref = $(this).find("[required]");

    $(ref).each(function(){
        if ( $(this).val() == '' )
        {
            alert("Required field should not be blank.");

            $(this).focus();

            e.preventDefault();
            return false;
        }
    });  return true;
});

$('.mailing-address-check').click(function(){
    if ( $('.mailing-address').css('visibility') == 'hidden' )
      $('.mailing-address').css('visibility','visible');
    else
      $('.mailing-address').css('visibility','hidden');
});

