$.extend({
    paymentGateWay: function (settings) {
        var _this = this;
        _this.settings = settings;
        switch (_this.settings.payment_type) {

            case 'cashfree':
                $('#cashfree', '#payment-forms').show();
                $.cashfree(_this.settings.gateway_info);
                break;
            case 'pay-u':
                $('#pay-u', '#payment-forms').show();
                $.payU(_this.settings.gateway_info);
                break;
        }
    },
    payU: function (settings) {
        var _this = this;
        _this.id = $('#pay-u');
        _this.addFiled = function () {
            _this.id.empty();
            _this.id.append($('<form>', {action: settings.url, method: 'post'}).append(function () {
                var fields = [];
                $.each(settings, function (k, e) {
                    fields.push($('<input>', {type: 'hidden', name: k, value: e}));
                });
                return fields;
            }));
            $('form', _this.id).trigger('submit');
        };
        _this.addFiled();
    },
    cashfree: function (settings) {
        var _this = this;
        _this.data = {};
        _this.data.appId = settings.appId;
        _this.data.orderId = settings.orderId;
        _this.data.orderAmount = parseFloat(settings.orderAmount);
        _this.data.orderCurrency = settings.orderCurrency;
        _this.data.customerName = settings.customerName;
        _this.data.customerPhone = settings.customerPhone;
        _this.data.customerEmail = settings.customerEmail;
        _this.data.notifyUrl = settings.notifyUrl;
        _this.data.returnUrl = settings.returnUrl;
        _this.data.orderNote = settings.orderNote;
        _this.data.paymentModes = settings.paymentModes;
        _this.data.paymentToken = settings.signature;
        _this.config = {};
        _this.config.layout = {view: "inline", container: "cashfree"};
        _this.config.mode = settings.mode;
        _this.response = CashFree.init(_this.config);
        _this.postPaymentCallback = function (event) {
            if (event.status != 'ERROR') {
                switch (event.name) {
                    case "PAYMENT_REQUEST":
                        if ($('#cashfree', '#deal-purchase #payment-forms').length) {
                            $('#cashfree', '#deal-purchase #payment-forms').hide();
                            $.ajax({
                                url: settings.datafeed,
                                data: {response: event.response},
                                success: function (op) {
                                    $('my-deal').myDeal(op.deal);
                                    $('#step-1,#step-2', '#deal-purchase').hide();
                                    $('#step-3', '#deal-purchase').show();
                                    $('#deal').hide();
                                    $('#deal-purchase').show();
                                },
                                error: function (xhr, textStatus, errorThrown) {
                                    var data = xhr.responseJSON;
                                    if (xhr.status === 422) {

                                    }
                                }
                            });
                        }
                        else if ($('#cashfree', '#wallet_balance #payment-forms').length) {
                            $('#cashfree', '#wallet_balance #payment-forms').hide();
                            $.ajax({
                                url: settings.datafeed,
                                data: {response: event.response},
                                success: function (op) {
                                },
                                error: function (xhr, textStatus, errorThrown) {
                                    var data = xhr.responseJSON;
                                    if (xhr.status === 422) {

                                    }
                                }
                            });
                        }
                }
            }
        };
        if (_this.response.status == "OK") {
            CashFree.makePayment(_this.data, _this.postPaymentCallback);
        } else {
            console.log(_this.response.message);
        }
        return _this;
    }
});
