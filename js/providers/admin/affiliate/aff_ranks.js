$(document).ready(function () {
    var DT = $('#table3').dataTable({
        ajax: {
            data: function (d) {
                return $.extend({}, d, $('input,select', '.panel_controls').serializeObject());
            }
        },
        columns: [
            {
                data: 'fullname',
                name: 'fullname'
            },
			{
                data: 'uname',
                name: 'uname'
            },
			{
                data: 'rank',
                name: 'rank'
            },
			{
                data: 'country',
                name: 'country'
            },
			{
                data: 'gen_1',
                name: 'gen_1',
				class:'text-right',
            },
			{
                data: 'gen_2',
                name: 'gen_2',
				class:'text-right',
            },
			{
                data: 'gen_3',
                name: 'gen_3',
				class:'text-right',
            },
        ]
    });
    $('#search').click(function (e) {
        e.preventDefault();
        DT.fnDraw();
    });
});
