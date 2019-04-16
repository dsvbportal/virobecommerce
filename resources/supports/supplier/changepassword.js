$(document).ready(function () {
    $('#change_pwd').on('click', function (event)
    {
        $('.error').empty();
        event.preventDefault();
        $('.alert').remove();
        $('#suppliers_change_pwd').validate({
            errorElement: 'div',
            errorClass: 'error',
            focusInvalid: false,
            rules: {
                old_password: {
                    required: true,
                },
                new_password: {
                    required: true,
                },
                confirm_password: {
                    required: true,
                    equalTo: '#new_password',
                },
            },
            messages: {
                old_password: {
                    required: 'Please enter your New Password',
                },
                new_password: {
                    required: 'Please enter your Old Password',
                },
                confirm_password: {
                    required: 'Please enter your Confirm Password',
                    equalTo: 'Please enter same value',
                },
            },
            submitHandler: function (form, event) {
                event.preventDefault();
                if ($(form).valid()) {
                    var datastring = $(form).serialize();
                    $.ajax({
                        url: window.location.BASE + 'supplier/save_changepasswrord',
                        data: datastring,
                        beforeSend: function () {
                            $('input[type=submit]', $(form)).val('Processing..').attr('disabled', true);
                        },
                        success: function (data) {
                            $('.alert').remove();
                            if (data.result == 'OK')
                            {
                                $(form).before(data.msg);
                                $(form).resetForm();
                            }
                            else
                            {
                                $(form).before(data.msg);
                            }
                            $('input[type=submit]', $(form)).val('Submit').attr('disabled', false);
                        },
                        error: function () {
                            $('input[type=submit]', $(form)).val('Submit').attr('disabled', false);
                            alert('Something went wrong');
                        }
                    });
                }
            }
        });
        $('#suppliers_rpwd').modal();
    });
});
