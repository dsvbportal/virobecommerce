$(document).ready(function () {
    var DT = $('#qualified_volume_list').dataTable({
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
                    /* render: function (data, type, row, meta) {
                        return new String(row.signedup_on).dateFormat('dd-mmm-yyyy HH:mm:ss');
                    } */
               },
               {
                data: 'uname',
                name: 'uname',
				 render: function (data, type, row, meta) {
                var txt = '';
				var txt='<b>'+row.fullname+'</b>'+'('+row.user_code+')';
				return txt;
				 }
                },
                {
					data: 'country_name',
					name: 'country_name',
				},
                {
					data: 'qv',
					name: 'qv',
				},
			    {
					data: 'confirm_date',
					name: 'confirm_date',
                    class: 'text-left',
                    /* render: function (data, type, row, meta) {
                        return new String(row.signedup_on).dateFormat('dd-mmm-yyyy HH:mm:ss');
                    } */
				},
           ]
       });
    $('#search_btn').click(function () {
        DT.fnDraw();
    });
	 $('#reset_btn').click(function (e) {
			$('input,select', $(this).closest('form')).val('');
			 DT.fnDraw();
		});
});

