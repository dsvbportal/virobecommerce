$(document).ready(function () {
    var SBF = $('#store-bank-form');
    $('#postal_code', SBF).on('change', function () {
        var pincode = $('#postal_code', SBF).val();
        if (pincode != '' && pincode != null)
            $.ajax({
                url: window.location.BASE + 'check-pincode',
                data: {pincode: pincode},
                success: function (OP) {
                    $('#country_id, #state_id, #city_id', SBF).prop('disabled', false).empty();
                    $('#country_id', SBF).append($('<option>', {value: OP.country_id}).text(OP.country));
                    $('#state_id', SBF).append($('<option>', {value: OP.state_id}).text(OP.state));
                    $.each(OP.cities, function (k, e) {
                        $('#city_id', SBF).append($('<option>', {value: e.id}).text(e.text));
                    });
                    $('#country_id, #state_id, #city_id', SBF).trigger('change');
                },
                error: function () {
                    $('#country_id, #state_id, #city_id', SBF).empty();
                    $('#country_id', SBF).val('').prop('disabled', true);
                    $('#state_id', SBF).val('').prop('disabled', true);
                    $('#city_id', SBF).val('').prop('disabled', true);
                }
            });
    });
    SBF.on('submit', function (e) {
        e.preventDefault();
        CURFORM = SBF;
        $.ajax({
            url: SBF.attr('action'),
            data: SBF.serialize(),
            beforeSend: function () {
                $('input[type=submit]', SBF).attr('disabled', true).val('Processing..');
            },
            success: function (OP) {
                //    SBF.before(OP.msg);
                $('input[type=submit]', SBF).removeAttr('disabled', true).val('Save');
                //        window.location.href = OP.url;
            }
        });
    });
    var postcode = $('#postal_code').val();
    if (postcode) {
        $.ajax({
            url: window.location.BASE + 'check-pincode',
            data: {pincode: postcode},
            success: function (OP) {
                $('#country_id, #state_id, #city_id', SBF).prop('disabled', false).empty();
                $('#country_id', SBF).append($('<option>', {value: OP.country_id}).text(OP.country));
                $('#state_id', SBF).append($('<option>', {value: OP.state_id}).text(OP.state));
                $.each(OP.cities, function (k, e) {
                    $('#city_id', SBF).append($('<option>', {value: e.id}).text(e.text));
                });
                $('#city_id option[value=' + city_id + ']').attr('selected', true);
                $('#country_id, #state_id, #city_id', SBF).trigger('change');
            },
            error: function () {
                $('#country_id', SBF).val('').prop('disabled', true);
                $('#state_id', SBF).val('').prop('disabled', true);
                $('#city_id', SBF).val('').prop('disabled', true);
            }
        });
    }
});
