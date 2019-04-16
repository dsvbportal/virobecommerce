$(document).ready(function () {
    var DT = $('#user_details').dataTable({
		"ordering": false,
		"bStateSave": true,
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
        columns: [
            {
                data: 'last_active',
                name: 'last_active',
				class: 'text-left',
            },
			{
                data: 'user_code',
                name: 'user_code',
				class: 'text-left',
            },
			{
                data: 'email',
                name: 'email',
				class: 'text-left',
            },
		
			{
                name: 'status',
                class: 'text-center',
                data: function (row, type, set) {
                    return '<button class="btn btn-success activate_user" value="'+row.account_id+'">' +'Send Verification'+ '</button>';
                }
            },
        ],
    });
	$('#search').click(function () {
		DT.fnDraw();
	});  
	
	$('#resetbtn').click(function (e) {
		$('input,select', $(this).closest('form')).val('');
		DT.fnDraw();
	});
	
});

	$('#user_details').on('click', '.activate_user', function (e) {
        e.preventDefault();
        var data = $(this).val();		
         CurEle = $(this);		
     	 $.ajax({
            data: {account_id: data},
            url: 'admin/affiliate/activate-user',
            type: "POST",
            dataType: 'JSON',
            beforeSend: function () {
                $('body').toggleClass('loaded');
                $('.alert,div.help-block').remove();
            },
            success: function (res) {
               if (res.status == 200) {
				  $("#user_details").dataTable().fnDraw();
                  $('#user_details').before('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + res.msg + '</div>');
                  $('.alert').fadeOut(7000); 
                }  
            },
             error: function (res) {
              
            }
        }); 
    }); 
