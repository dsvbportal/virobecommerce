$(function () {
    var mytree = [];
    $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
    $('.tree li.parent_li').on('click', 'span', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        var accountid = $(this).data('value');
        if (children.is(":visible")) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } else {
            var accountid = $(this).data('value');
            var id = "ch_" + accountid;
            var stateOpt = '';
            $.post('affiliate/referrals/get-direct-geneology/' + accountid, function (data) {
                if (data.status == "ok") {
                    $.each(data.direct, function (key, elements) {
                        mytree['ch_' + elements.account_id] = elements;
                        stateOpt += "<li class='parent_li'><span data-value='" + elements.account_id + "' title='Expand this branch'><b>" + elements.fullname + "(" + elements.username + ")" + "</b>";
                        //stateOpt += " ( " +elements.fullname+ " ) ";
                        stateOpt += " - Team (" + elements.team_count + ")</span>";
                        if (elements.team_count > 0)
                        {
                            stateOpt += "<ul id='ch_" + elements.account_id + "'><li style='display:none'><span>Loading....</span></li></ul>";
                        }
                        stateOpt += "</li>";
                    });

                }
                $("#" + id).html(stateOpt);
                showUserinfo(id, mytree);
            }, 'json');
            children.show('fast');
            $(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        e.stopPropagation();
    });


	//$('#gtree a.gtActive,#gtree2 a.gtActive,#gtree3 a.gtActive').click(function () {
	/*$('.generation_panel').on('mouseenter','#gtree1 a.spill,#gtree2 a.spill,#gtree3 a.spill',function (e) {
        $('.mySaleInfo').hide();
		$('.mySaleInfo',$(this)).show();
    $(".mySaleInfo").mouseleave(function() {
        $(this).hide();
    });
    })*/

    $('#geneologyFrm #searchBtn').click(function (e) {
        $.ajax({
            type: 'POST',
            url: $('#geneologyFrm').attr('action'),
            data: {uname: $('#geneologyFrm #uname').val()},
            dataType: 'json',
            beforeSend: function () {
                $('#gtree1,#gtree2,#gtree3').hide();
                $('.gtree a').removeClass().addClass('gtempty');
                $('.gtree a').attr('data-id', '');
                $('.gtree a').attr('title', '');
				$('.gtree a .acinfo,.gtree a .mySaleInfo').remove();
                $('#geneologyFrm #searchBtn').attr('disabled', true);
            },
            success: function (op) {
                $('#geneologyFrm #searchBtn').attr('disabled', false);
                if (op.status == 200) {
                    mytree = op.gusers;
                    var $treeDiv = $('#gtree' + op.gusers[op.tree_layout].mypos);
                    $.each(op.gusers, function (k, elm) {
                        $('#t' + elm.mypos, $treeDiv).attr('data-id', elm.username);
                        $('#t' + elm.mypos, $treeDiv).attr('title', elm.username);
                        $('#t' + elm.mypos, $treeDiv).removeClass('gtempty').addClass('gt' + elm.status);
						$('#t' + elm.mypos, $treeDiv).text('a');
						if(elm.status=='Active' || elm.status=='Blocked' || elm.status=='Free'){
							$('#t' + elm.mypos, $treeDiv).addClass('spill');
						}
						$('#t' + elm.mypos, $treeDiv).html('<div class="acinfo"><b>'+elm.user_code+'</b>'+elm.username+'</div>');
						$('#t' + elm.mypos, $treeDiv).append(
							$('<div>',{class:'mySaleInfo'}).append($('#user_info').html())
						)
						$popBox = $('#t' + elm.mypos+' .mySaleInfo', $treeDiv);
						$('td',$popBox).text('-');
						$('#tree_acfullname',$popBox).text(elm.fullname);
						$('#tree_acuname b',$popBox).text(elm.user_code);
						$('#tree_acsponser_uname',$popBox).text(elm.sponser_user_code);
						$('#tree_acupline_uname',$popBox).text(elm.upline_user_code);
						$('#tree_acqv',$popBox).text(elm.qv);
						$('#tree_accv',$popBox).text(elm.cv);
						$('#tree_acsignedup_on',$popBox).text(elm.activated_on);
						$('#tree_acddown_cnts',$popBox).text(elm.team_count);
						if (elm.geninfo !== undefined) {
							$.each(elm.geninfo, function (k, gelm) {
								$('.af' + gelm.gid + 'G_cnts',$popBox).text(gelm.cnts);
								$('.af' + gelm.gid + 'G_qv',$popBox).text(gelm.qv);
								$('.af' + gelm.gid + 'G_cv',$popBox).text(gelm.cv);
							});
						}

                    });
                    $treeDiv.show();
                   // $('#gtree' + op.tree_layout + ' a:eq(0)').trigger('click');
                }
            }
        });
    });
    $('#geneologyFrm #searchBtn').trigger('click');
    
    $('.gtree').on('click','a.spill',function (e) {
			console.log($(this).attr('data-id'))
          $('#geneologyFrm #uname').val($(this).attr('data-id'));
          $('#geneologyFrm #searchBtn').trigger('click');
    });
    
    
     $('#geneologyFrm #refreshBtn').click(function (e) {
          $('#geneologyFrm #uname').val($('#geneologyFrm #curuser').val());
          $('#geneologyFrm #searchBtn').trigger('click');
     });
    
});

function showUserinfo(id, list) {
    if (list[id] != undefined && list[id] != '') {
        document.getElementById("tree_acfullname").innerHTML = list[id].fullname + ' & ' + list[id].username;
        document.getElementById("tree_acinvby").innerHTML = list[id].sponser_uname;
        document.getElementById("tree_acginv_cnts").innerHTML = list[id].upline_uname;
        document.getElementById("tree_acddown_cnts").innerHTML = list[id].team_count;
        document.getElementById("tree_actsinby").innerHTML = list[id].signedup_on;
        document.getElementById("tree_actby").innerHTML = list[id].activated_on;

    }
}
