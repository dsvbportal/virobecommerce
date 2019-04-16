$(document).ready(function () {   
	var DT = $('#image_verify_list').dataTable({
        ajax: {
            url: $('#verification_docs_list').attr('action'),
            data: function (d) {
                return $.extend({}, d, {
                    type_filer: $('#verification_docs_list #type_filer').val(),
                    search_term: $('#verification_docs_list #search_term').val(),
                    status: $('#verification_docs_list #status').val(),
                    from: $('#verification_docs_list #from').val(),
                    to: $('#verification_docs_list #to').val(),
                    uname: $('#verification_docs_list #uname').val(),
                    account_id: $('#verification_docs_list #account_id').val()
                });
            }
        },
        columns: [
            {
                data: 'created_on',
                name: 'created_on',
                class: 'text-left',               
            },
            {
                data: 'name',
                name: 'name',
                render: function (data, type, row, meta) {
                    var html = row.full_name + ' (' + row.uname + ')';
                    return html;
                }
            },
            {
                data: 'type',
                name: 'type',               
            },
            {
                data: 'path',
                name: 'path',
                class: 'text-center',
                render: function (data, type, row, meta) {
                    var content =  content = '<a class = "btn btn-sm btn-info" href = "' + row.path + '" download><i class="fa fa-download"></i></a>';
					return content;
                }
            },
            {
                data: 'status',
                name: 'status',
                class: 'text-center status',
                render: function (data, type, row, meta) {
                    var content = '<span class="label label-'+ row.status_class +'">'+ row.status +'</span>';
					return content;					
                }
            },
			{
                class: 'text-center',
                orderable: false,
                render: function (data, type, row, meta) {
                    return addDropDownMenu(row.actions, true);                 
                }
            }
        ]
    });
	
	$('#search').click(function (e) {
        DT.fnDraw();
    });
	
	$('#resetbtn').click(function (e) {
        $('input,select', $(this).closest('form')).val('');
        DT.fnDraw();
    });
		
	$(document).on('click', '.change_status', function (event) {
        event.preventDefault();
		CURELE = $(this);
        if (confirm(CURELE.attr('data-confirm'))) {			
            $.ajax({
                data: {uv_id: CURELE.data('id'), status: CURELE.data('status'), curstatus: CURELE.data('curstatus')},
                url: CURELE.attr('href'),
                success: function (data) {
                    DT.fnDraw();
                }
            });
        }
    });
	
    /* $('#type_filer').loadSelect({
        url: window.location.BASE + 'admin/affiliate/doc-list',
        key: 'document_type_id',
        value: 'type',
    }); */
    /*$('.upload').change(function () {
        var uploadFile = $(this).val();
        var valArr = uploadFile.split('.');
        txtext = uploadFile.split('.')[(valArr.length) - 1];
        txtext = txtext.toLowerCase();
        doctypes = $(this).data('format');
        fformats = doctypes.split('|');
        if (fformats.indexOf(txtext) == - 1) {
            $(this).val('');
            alert('Invalide! - Available file types are ' + '(' + doctypes + ').');
            return false;
        }
        $(this).closest('.form-group').find('.file_err').empty();
        var doc_type = $(this).closest('.row').find('.select_type');
        if (uploadFile != '') {
            if (doc_type.val() != '') {
                $('#sent_doc').attr('disabled', false);
            } else {
                $(this).closest('.row').find('.doc_err').html('Plese select the Document Type')
                $('#sent_doc').attr('disabled', true);
                return false;
            }
        } else {
            $('#sent_doc').attr('disabled', true);
            return false;
        }
    });*/
   /*  $('#upload_form').on('submit', function (event) {
        event.preventDefault();
        $.ajax({
            data: new FormData(this),
            url: window.location.BASE + 'affiliate/save_document',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                DT.fnDraw();
            }
        });
    }); */
    
    /* $('#image_verify_list').on('click', '.delete', function (event) {
        event.preventDefault();	
        if (confirm('Are you Sure, You wants to delete it?')) {
            $.ajax({
                data: {uv_id: $(this).data('id')},
                url: window.location.BASE + 'admin/affiliate/delete_doc',
                success: function (data) {
                    DT.fnDraw();
                }
            });
        }
    }); */
});
