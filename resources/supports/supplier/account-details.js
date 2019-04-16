$(document).ready(function () {
    var SADF = $('#supplier-account-details-form');
    $('#postal_code', SADF).on('change', function () {
        var pincode = $('#postal_code', SADF).val();
        if (pincode != '' && pincode != null)
            $.ajax({
                url: window.location.BASE + 'check-pincode',
                data: {pincode: pincode},
                success: function (OP) {
                    $('#country_id, #state_id, #city_id', SADF).prop('disabled', false).empty();
                    $('#country_id', SADF).append($('<option>', {value: OP.country_id}).text(OP.country));
                    $('#state_id', SADF).append($('<option>', {value: OP.state_id}).text(OP.state));
                    $.each(OP.cities, function (k, e) {
                        $('#city_id', SADF).append($('<option>', {value: e.id}).text(e.text));
                    });
                    $('#country_id, #state_id, #city_id', SADF).trigger('change');
                },
                error: function () {
                    $('#country_id, #state_id, #city_id', SADF).empty();
                    $('#country_id', SADF).val('').prop('disabled', true);
                    $('#state_id', SADF).val('').prop('disabled', true);
                    $('#city_id', SADF).val('').prop('disabled', true);
                }
            });
    });
    SADF.on('submit', function (e) {
        e.preventDefault();
        CURFORM = SADF;
        $.ajax({
            url: SADF.attr('action'),
            data: SADF.serialize(),
            beforeSend: function () {
                $('input[type=submit]', SADF).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
                $('input[type=submit]', SADF).removeAttr('disabled', true).val('Sign Up');
                //    SADF.hide();
                if (OP.url != undefined) {
                    //        window.location.href = OP.url;
                }
            },
            error: function (jqXhr) {
                $('input[type=submit]', SADF).removeAttr('disabled', true).val('Sign Up');
            }
        });
    });
    var postcode = $('#postal_code').val();
    if (postcode) {
        $.ajax({
            url: window.location.BASE + 'check-pincode',
            data: {pincode: postcode},
            success: function (OP) {
                $('#country_id, #state_id, #city_id', SADF).prop('disabled', false).empty();
                $('#country_id', SADF).append($('<option>', {value: OP.country_id}).text(OP.country));
                $('#state_id', SADF).append($('<option>', {value: OP.state_id}).text(OP.state));
                $.each(OP.cities, function (k, e) {
                    $('#city_id', SADF).append($('<option>', {value: e.id}).text(e.text));
                });
                $('#city_id option[value=' + city_id + ']').attr('selected', true);
            },
            error: function () {
                $('#country_id', SADF).val('').prop('disabled', true);
                $('#state_id', SADF).val('').prop('disabled', true);
                $('#city_id', SADF).val('').prop('disabled', true);
            }
        });
    }
    $('input.website').keyup(function () {
        if (! ((this.value.match('^http://')) || (this.value.match('^https://')))) {
            this.value = 'http://' + this.value;
        }
    });
});
