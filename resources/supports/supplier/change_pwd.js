$(document).ready(function () {
    /*** Change Password Validate ***/
    $('#changepassword').validate({
        errorElement: 'div',
        errorClass: 'error',
        focusInvalid: false,
        // Specify the validation rules
        rules: {
            newpassword: {
                required: true,
                minlength: 6
            },
            confirmpassword: {
                required: true,
                equalTo: '#newpassword'
            },
            oldpassword: {
                required: true,
            }
        },
        // Specify the validation error messages
        messages: {
            newpassword: {
                required: 'Please provide a new password',
                minlength: 'Your password must be at least 6 characters long'
            },
            confirmpassword: {
                required: 'Please confirm a new password'
            },
            oldpassword: {
                required: 'Please enter your old password',
            }
        },
        submitHandler: function (form, event) {
            if ($(form).valid()) {
                var datastring = $(form).serialize();
                $.ajax({
                    url: $(form).attr('action'), // Url to which the request is send
                    // Type of request to be send, called as method
                    data: datastring, // Data sent to server, a set of key/value pairs representing form fields and values
                    beforeSend: function () {
                        $('.alert').remove();
                    },
                    success: function (data) 		// A function to be called if request succeeds
                    {
                        if (data.result == 'OK')
                        {
                            $('#changepassword').before('<div class="alert ' + data.alertclass + '"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');
                            $('#password').val('');
                            $('#newpassword').val('');
                            $('#confirmpassword').val('');
                            $('#oldpassword').val('');
							console.log(window.location.BASE + 'seller/logout');
							window.location.assign(window.location.BASE + 'seller/logout');
                        }
                        else
                        {
                            $('#changepassword').before('<div class="alert ' + data.alertclass + '"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + data.msg + '<br></div>');
                        }
                    },
                    error: function () {
                        alert('Something went wrong');
                    }
                })
            }
        }
    });       /* end */
});
