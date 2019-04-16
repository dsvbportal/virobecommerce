$(document).ready(function () {
    var SSUF = $('#create_merchant');
    var CUSR = $('#check-user');
    var treeList = '';
    var treeList = '';
    var bcategoryId = '';
    var tSearch = '';
    var catArr_resource = [];

	SSUF.on('change', '#country', function () {
        var selected = $('#country option:selected');
        $('input.country-phonecode').val(selected.data('phonecode'));
        $('span.country-phonecode').text(selected.data('phonecode'));
        $('.country-flag').attr('src', selected.data('flag'));
        $('#mobile').attr('pattern', selected.data('mobile_validation'));
    });
    $('#country', SSUF).trigger('change');
	
  
    SSUF.on('submit', function (e) {
        e.preventDefault();
        CURFORM = SSUF;
        $.ajax({
            url: SSUF.attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: SSUF.serialize(),
            beforeSend: function () {
                $(':submit', SSUF).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
                $('input,select',CURFORM).val('');
				 $(':submit', SSUF).attr('disabled', false).val('Continue');
            },
            error: function (jqXhr) {
                $(':submit', SSUF).removeAttr('disabled', true).val('Sign Up');
            }
        });
    });

    $('#firstname,#lastname', SSUF).on('change', function () {
        $('#name', $('#signup-success-div')).html($('#firstname', SSUF).val() + ' ' + $('#lastname', SSUF).val());
    });

    $('#mobile', SSUF).on('keypress', function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
            return false;
        }
        return true;
    });

    $('#mobile-verification-div').on('click', '.dismiss', function (e) {
        e.preventDefault();
        window.location.href = 'seller/dashboard';
    })

   /*  $('#service_type').change(function (e) {
        e.preventDefault();
        if ($(this).val() == 2) {
            $('#cateFld').css('display', 'none');
        } else {
            $('#cateFld').css('display', 'block');
        }
    }) */

     SSUF.on('change', '#service_type', function () {
		var selec = $('#service_type option:selected');
		console.log(selec.val());
		if (selec.val() == 1 || selec.val() == 3) {
			$("#phy_locations option:selected").removeAttr("selected");
			$("#phy_locations option[value='1']").hide();
			$("#phy_locations option[value=2]").show();
			$('#phy_locations option[value=2]').attr('selected','selected');
			$("#phy_locations option[value=3]").show();
			$("#phy_locations option[value=4]").show();
			$("#phy_locations option[value=5]").show();
			$("#phy_locations option[value=6]").show();						
			$('#cateFld').css('display', 'block');
		}
		if (selec.val() == 2) {
			$("#phy_locations option:selected").removeAttr("selected");
			$("#phy_locations option[value='1']").show();			 
			$("#phy_locations option[value=2]").hide();
			$("#phy_locations option[value=3]").hide();
			$("#phy_locations option[value=4]").hide();
			$("#phy_locations option[value=5]").hide();
			$("#phy_locations option[value=6]").hide();			
			$('#phy_locations option[value=1]').attr('selected','selected'); 
			$('#cateFld').css('display', 'none');
		}
	});	
	$('#service_type', SSUF).trigger('change');
	
    tSearch = $('#category_serach').hierarchySelect({
        hierarchy: true,
        search: true,
        width: 255
    });

    var activeCls = '';
    function buildTree(pCats, level) {
        level = level || 1;
        //console.log(pCats);
        $.each(pCats, function (k, elm) {
            treeList = treeList + '<li  data-value="' + elm.id + '"  data-level="' + level + '" class="level-' + level + ' ' + activeCls + '"><a href="#">' + elm.name + '</a></li>';
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

    function businessCategories(bcategoryId) {
        bcategoryId = bcategoryId || null;
        $.ajax({
            url: $("#bcategory").data('url'),
            type: "post",
            dataType: "json",
            success: function (op) {
                if (op.data != '') {
                    var data = op.data;
                    catArr_resource = data;
                    Tree = buildTree(filterCategory(1), 1);
                    Tree = '<li data-value="" data-level="0" class="level-0"><a href="#">- Select -</a></li>' + Tree;
                    $('.dropdown-menu .inner').html(Tree);
                    if (bcategoryId != null) {
                        $('#example-one').hierarchySelect('setValue', bcategoryId);
                    }
                }
            }
        });
    }
    businessCategories();
});
