$(document).ready(function () {
    var SSUF = $('#supplier-sign-up-form');
    var CUSR = $('#check-user');
	var treeList = '';
	loadOptions();	
	function loadOptions() {
        $.ajax({
            url: window.location.BASE + 'seller-categories',
            success: function (op) {
                if (op.data) {
                    var data = op.data;
                    catArr_resource = data;
                    Tree = buildTree(filterCategory(1485), 1);
                    Tree = '<li data-value="" data-level="0" class="level-0 active"><a href="#">- Select -</a></li>' + Tree;
                    $('.dropdown-menu .inner').append(Tree);
                }
                /* if (op.countries != '') {
                    $('#country option').remove();
                    $.each(op.countries, function (index, val) {
                        $('#country').append('<option value="' + val.country_id + '">' + val.country + '</option>');
                    });
                } */
            }
        });
    }
	function buildTree(pCats, level) {
        level = level || 1;
        $.each(pCats, function (k, elm) {
            treeList = treeList + '<li data-value="' + elm.id + '"  data-level="' + level + '" class="level-' + level + '"><a href="#">' + elm.name + '</a></li>';
            sCats = filterCategory(elm.id);
            if (sCats.length > 0) {
                treeList = buildTree(sCats, parseInt(level) + 1);
            }
        });
        return treeList;
    }
    function filterCategory(pid) {
        var resultAarray = jQuery.grep(catArr_resource, function (elm, i) {
            return (elm.parent_id == pid);
        });
        return resultAarray;
    }
	
	CUSR.on('submit', function (e) {
        e.preventDefault();
        CURFORM = CUSR;
        $.ajax({
            url: CUSR.attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: CUSR.serialize(),
            beforeSend: function () {
                $('input[type=submit]', CUSR).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
				console.log(OP.data);
				CURFORM.hide();
				SSUF.removeClass("hidden");				
				$('#firstname, #supplier-sign-up-form').val(OP.data.firstname);
				$('#lastname, #supplier-sign-up-form').val(OP.data.lastname);
				$('#email, #supplier-sign-up-form').val(OP.data.email);
				$('#mobile, #supplier-sign-up-form').val(OP.data.mobile);     				
				
            },
            error: function (jqXhr) {			
                $('input[type=submit]', CUSR).removeAttr('disabled', true).val('Sign Up');
				CURFORM.hide();
				SSUF.removeClass("hidden");					
            }
        });
    });	
	
    SSUF.on('submit', function (e) {
        e.preventDefault();
        CURFORM = SSUF;
        $.ajax({
            url: SSUF.attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: SSUF.serialize(),
            beforeSend: function () {
                $('input[type=submit]', SSUF).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
				console.log(OP);
                $('input[type=submit]', SSUF).removeAttr('disabled', true).val('Sign Up');
                SSUF.before($('<div>').attr({class: 'alert alert-success col-sm-8 col-sm-offset-2'}).append(OP.msg));
                SSUF.hide();
                if (OP.url != undefined) {
                    window.location.href = OP.url;
                }
            },
            error: function (jqXhr) {
                $('input[type=submit]', SSUF).removeAttr('disabled', true).val('Sign Up');
            }
        });
    });	
    $('#show-hide-password').on('click', function () {
        if ($('#pass_key').attr('type') === 'password') {
            $('#pass_key').attr('type', 'text');
        }
        else {
            $('#pass_key').attr('type', 'password');
        }
        var alt = $('#show-hide-password').attr('data-alternative');
        $('#show-hide-password').attr('data-alternative', $('#show-hide-password').text()).text(alt);
    });
	
    $('#firstname,#lastname', SSUF).on('change', function () {
        $('#name', $('#signup-success-div')).html($('#firstname', SSUF).val() + ' ' + $('#lastname', SSUF).val());
    });
	
    $('#firstname,#lastname', SSUF).on('keypress', function (e) {
        var code = e.charCode ? e.charCode : e.keyCode;
        if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || code == 116 || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
            return true;
        }
        return false;
    });
    $('#mobile', SSUF).on('keypress', function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
            return false;
        }
        return true;
    });
});
