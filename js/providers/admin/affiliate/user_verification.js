$(document).ready(function () {
    var DT = $('#user_verification_list').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('.panel_controls input,select').serializeObject());
            }
        },
        columns: [
               {
                    data: 'created_on',
                    name: 'created_on',
                    class: 'text-left',
                    render: function (data, type, row, meta) {
                        return new String(row.created_on).dateFormat('dd-mmm-yyyy HH:mm:ss');
                    }
               },
			   {
                    data: 'uname',
                    name: 'uname',
					class: 'text-left',
                },
			    {
                    data: 'fullname',
                    name: 'fullname',
					class: 'text-left',
					render: function (data, type, row, meta) {
                        return row.fullname+'<br><span class="text-muted">Country: <span class="text-primary">'+row.country_name+'</span></span>';
                    }
                 },
			     {
                    data: 'proof_type',
                    name: 'proof_type',
					class: 'text-left',
                 },
			     {
                    data: 'type',
                    name: 'type',
					class: 'text-left',
                },
           ]
       });
    $('#search').click(function () {
        DT.fnDraw();
    });
	 $('#resetbtn').click(function (e) {
			$('input,select', $(this).closest('form')).val('');
			 DT.fnDraw();
		});
});

