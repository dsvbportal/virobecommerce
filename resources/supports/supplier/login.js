$(document).ready(function () {
    $('#login_form').submit(function (event) {
        event.preventDefault();
        CURFORM = $('#login_form');
        $.ajax({
            url: $('#login_form').attr('action'),
            data: $('#login_form').serializeObject()
        });
    });
    var STEP = 1, FORM = $('#forgot-pwd-panel .step-1');
    $('#forgot-password-btn').on('click', function (e) {
        e.preventDefault();
        //window.location.ChangeUrl('Forgot Password', window.location.BASE + 'supplier/forgot-password');
        $('#forgot-pwd-panel').show();
        $('#login-panel').hide();
    });
    $('#login-btn').on('click', function (e) {
        e.preventDefault();
        window.location.ChangeUrl('Login', window.location.BASE + 'supplier/login');
        STEP = 1;
        FORM = $('#forgot-pwd-panel .step-1');
        $('#forgot-pwd-panel .step-2,#forgot-pwd-panel .step-3,#forgot-pwd-panel').hide();
        $('#forgot-pwd-panel .step-1,#login-panel').show();
        $('#forgot-pwd-panel form').resetForm();
    });
    $('form', '#forgot-password').on('submit', function (e) {
        e.preventDefault();
        CURFORM = FORM;
        $.ajax({
            url: FORM.attr('action'),
            data: $('#forgot-password input').serializeObject(),
            success: function () {
                switch (STEP) {
                    case 1:
                        $('#forgot-pwd-panel .step-1').hide();
                        $('#forgot-pwd-panel .step-2').show();
                        FORM = $('#forgot-pwd-panel .step-2');
                        STEP ++;
                        break;
                    case 2:
                        $('#forgot-pwd-panel .step-2').hide();
                        $('#forgot-pwd-panel .step-3').show();
                        FORM = $('#forgot-pwd-panel .step-3');
                        STEP ++;
                        break;
                    case 3:
                        $('#login-btn').trigger('click');
                        break;
                }
            }
        });
    });
    $('#resend-verification-code').on('click', function (e) {
        e.preventDefault();
        CURFORM = $('#forgot-pwd-panel .step-1');
        $.ajax({
            url: CURFORM.attr('action'),
            data: CURFORM.serializeObject()
        });
    });
    $('#show-hide-password', $('#forgot-password')).on('click', function () {
        if ($('#password', $('#forgot-password')).attr('type') === 'password') {
            $('#password', $('#forgot-password')).attr('type', 'text');
        }
        else {
            $('#password', $('#forgot-password')).attr('type', 'password');
        }
        var alt = $('#show-hide-password', $('#forgot-password')).attr('data-alternative');
        $('#show-hide-password', $('#forgot-password')).attr('data-alternative', $('#show-hide-password').text()).text(alt);
    });
});
