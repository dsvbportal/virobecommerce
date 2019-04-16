(function (a) {
    a.CBB = {DEBUG: true, data: {}};
    a.CBB.loaderImg = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    a.location.CurPage = null;
    a.location.auto = true;
    a.location.PINCODE = null;
    a.location.position = {latitude: null, longitude: null};
    a.location.BASE = $('base').attr('href');
    a.location.ADMIN = a.location.BASE + 'admin/';
    a.location.MERCHANT = a.location.BASE;
    a.location.AFFILIATE = a.location.BASE + 'affiliate/';
    
    a.location.AddToUrl = function (title, url) {
        if (typeof (a.history.pushState) !== undefined) {
            var href = a.location.href, c_url = (href.indexOf('?') > 1) ? href.substring(0, href.indexOf('?')) : href;
            a.location.CurPage = {page: title, url: c_url + ((url !== '') ? '?' + url : '')};
            a.document.title = title;
            a.history.pushState(a.location.CurPage, a.location.CurPage.title, a.location.CurPage.url);
        }
    };
    a.location.ChangeUrl = function (title, url, op) {
        op = op || null;
        if (typeof (a.history.pushState) !== undefined) {
            a.location.CurPage = op != null ? op : {page: title, url: url};
            a.document.title = title;
            a.history.pushState(a.location.CurPage, a.location.CurPage.title, a.location.CurPage.url);
        }
    };
    a.location.GoToPrevious = function () {
        if (a.location.CurPage !== null) {
            a.document.title = a.location.CurPage.title;
            a.history.pushState(a.location.CurPage, a.location.CurPage.title, a.location.CurPage.url);
            a.location.CurPage = null;
        }
    };
    $(a).on('popstate', function (e) {
        if (e.originalEvent.state !== null && e.originalEvent.state.setContent) {
            a.document.title = e.originalEvent.state.page;
            $('.xbp-title').html(e.originalEvent.state.title);
            $('.xbp-icon-title').html([$('<i>', {class: 'fa fa-' + e.originalEvent.state.title_icon}), e.originalEvent.state.title]);
            $('#xbp-styles').html(e.originalEvent.state.styles);
            $('#xbp-breadcrumb').html(e.originalEvent.state.breadcrumb);
            $('#xbp-content').html(e.originalEvent.state.content);
            $('#xbp-scripts').html(e.originalEvent.state.scripts);
            //a.history.pushState(e.originalEvent.state, e.originalEvent.state.page, e.originalEvent.state.url);
        }
    });
    a.Error.stackTraceLimit = a.Infinity;
    a.location.PINCODE = (a.localStorage.getItem('location_settings') !== null && a.localStorage.getItem('location_settings') !== undefined && a.localStorage.getItem('location_settings') !== 'undefined') ? a.localStorage.getItem('location_settings') : null;
    a.location.setLocationSetings = function (status, pincode) {
        localStorage.setItem('location_settings', {auto: status, pincode: pincode});
        a.location.auto = status;
        a.location.PINCODE = pincode;
    };
    a.document.addEventListener('invalid', function (event) {
        event.preventDefault();
        a.CBB.customValidation(event);
    }, true);
    a.document.addEventListener('input', function (event) {
        a.CBB.customValidation(event);
    }, true);
    a.document.addEventListener('change', function (event) {
        a.CBB.customValidation(event);
    }, true);
    if (a.document.querySelector('[type="checkbox"]') != null) {
        a.document.querySelector('[type="checkbox"]').addEventListener('click', function (event) {
            a.CBB.customValidation(event);
        }, true);
    }
    a.CBB.customValidation = function (e) {
        var msg = null, _this = null;
        if (e.srcElement != undefined) {
            _this = e.srcElement;
        }
        if (e.target != undefined) {
            _this = e.target;
        }
        if (_this != null) {
            $(':submit', _this.form).attr('disabled', true);
            if (_this.dataset['errMsgTo'] != undefined) {
                $(_this.dataset['errMsgTo'], _this.form).attr({for : '', class: ''}).empty();
            }
            else {
                $('span[for="' + _this.name + '"]', _this.form).remove();
            }
            if (_this.getAttribute('type') == 'file' && _this.getAttribute('accept') != undefined && _this.getAttribute('accept') != '') {
                if (! ((new RegExp('(.*?)(' + ((_this.getAttribute('accept').replace(/\./g, '')).split(',')).join('|') + ')$')).test(_this.value))) {
                    msg = _this.dataset['typemismatch'];
                    $('#' + _this.id + '-preview').attr('src', _this.dataset['default']);
                }
                else if (_this.files && _this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#' + _this.id + '-preview').attr('src', reader.result);
                    }
                    reader.readAsDataURL(_this.files[0]);
                }
            }
            $('[data-requiredwith]', _this.form).each(function (k, element) {
                var fields = $(element).data('requiredwith').split(',');
                var passed = 0;
                var checking_fields = [];
                for (var i in fields) {
                    var field = fields[i].split(':');
                    checking_fields.push(field[0]);
                    if (field[0] == _this.name && (field[1] == undefined && _this.checked || (field[1] != undefined && field[1] == _this.value))) {
                        passed ++;
                    }
                }
                if (checking_fields.indexOf(_this.name) >= 0) {
                    if (passed == fields.length) {
                        $(element).attr('required', true);
                    }
                    else {
                        $(element).removeAttr('required');
                    }
                }
            });
            return a.CBB.confirmInput(e, _this);
        }
        return false;
    };
    a.CBB.confirmInput = function (e, _this) {
        if (e.type == 'input' && e.target.name == _this.name && ! _this.validity.badInput && ! _this.validity.patternMismatch && ! _this.validity.rangeOverflow && ! _this.validity.rangeUnderflow && ! _this.validity.stepMismatch && ! _this.validity.tooLong && ! _this.validity.tooShort && ! _this.validity.typeMismatch && ! _this.validity.valueMissing && _this.dataset['confirm'] != undefined && _this.value != $('[name="' + _this.dataset['confirm'] + '"]', _this.form).val()) {
            _this.dataset['customerror'] = _this.dataset['confirmErr'];
            _this.setCustomValidity(_this.dataset['confirmErr']);
            return  a.CBB.updateErrMsg(_this, false);
        }
        else {
            return a.CBB.checkExist(e, _this);
        }
    };
    a.CBB.checkExist = function (e, _this) {
        if (e.type == 'change' && e.target.name == _this.name && ! _this.validity.badInput && ! _this.validity.patternMismatch && ! _this.validity.rangeOverflow && ! _this.validity.rangeUnderflow && ! _this.validity.stepMismatch && ! _this.validity.tooLong && ! _this.validity.tooShort && ! _this.validity.typeMismatch && ! _this.validity.valueMissing && _this.dataset['checkExist'] != undefined) {
            var v = _this.dataset['checkExistData'].split(',');
            var data = {};
            for (var field in v) {
                var f = v[field].split(':');
                data[f[0]] = $(f[1]).val();
            }
            $.ajax({
                url: _this.dataset['checkExist'],
                data: data,
                success: function () {
                    _this.dataset['customerror'] = '';
                    _this.setCustomValidity('');
                    return a.CBB.updateErrMsg(_this, true);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.responseJSON.error[_this.name] != undefined) {
                        _this.dataset['customerror'] = jqXHR.responseJSON.error[_this.name].join(',');
                        _this.setCustomValidity(_this.dataset['customerror']);
                        return a.CBB.updateErrMsg(_this, false);
                    }
                }
            });
        }
        else if (_this.dataset['checkExist'] != undefined) {
            _this.setCustomValidity('');
            return a.CBB.updateErrMsg(_this, false);
        }
        else {
            _this.dataset['customerror'] = '';
            _this.setCustomValidity('');
            return a.CBB.updateErrMsg(_this, true);
        }
    };
    a.CBB.updateErrMsg = function (_this, enableFormSubmit) {
        var msg = null;
        if (! _this.validity.valid) {
            if (_this.validity.typeMismatch) {
                msg = _this.dataset['typemismatch'];
            } else if (_this.validity.badInput) {
                msg = _this.dataset['badinput'];
            } else if (_this.validity.patternMismatch) {
                msg = _this.dataset['patternmismatch'];
            } else if (_this.validity.rangeOverflow) {
                msg = _this.dataset['toolong'];
            } else if (_this.validity.rangeUnderflow) {
                msg = _this.dataset['tooshort'];
            } else if (_this.validity.stepMismatch) {
                msg = _this.dataset[''];
            } else if (_this.validity.tooLong) {
                msg = _this.dataset['toolong'];
            } else if (_this.validity.tooShort) {
                msg = _this.dataset['tooshort'];
            } else if (_this.validity.valueMissing) {
                msg = _this.dataset['valuemissing'];
            } else if (_this.validity.customError) {
                msg = _this.dataset['customerror'];
            }
            msg = msg != null ? msg : '';
            _this.setCustomValidity(msg);
        }
        if (_this.validationMessage != undefined && _this.validationMessage != '') {
            if ($('[name="' + _this.name + '"]', _this.form).length >= 1) {
                if (_this.dataset['errMsgTo'] != undefined) {
                    $(_this.dataset['errMsgTo'], _this.form).attr({for : _this.name, class: 'errmsg'}).append(_this.validationMessage);
                }
                else {
                    $('[name="' + _this.name + '"]', _this.form).after($('<span>').attr({for : _this.name, class: 'errmsg'}).html(_this.validationMessage));
                }
            }
            $(':submit', _this.form).attr('disabled', true);
            return false;
        }
        if (enableFormSubmit && (_this.form == null || _this.form != null && _this.form.checkValidity())) {
            $(':submit', _this.form).removeAttr('disabled');
            return true;
        }
        else {
            $(':submit', _this.form).attr('disabled', true);
            return false;
        }
    };

//    a.addEventListener('error', function (e) {
//        $.ajax({
//            data: {msg: e.message, file: e.filename, line: e.lineno, col: e.colno, trace: e.error != undefined && e.error.stack != undefined ? e.error.stack : null},
//            url: a.location.BASE + 'js-exceptions'
//        });
//        return true;
//    });

    a.CBB.ajaxComplete = function (event, xhr, settings) {		
        var data = xhr.responseJSON;		
		$('body').addClass('loaded');		
        a.CBB.location = xhr.getResponseHeader('location');
        if (a.CBB.location != null) {
            $('span#current-location').text(a.CBB.location);
            $('#edit-curernt-location').fadeOut('slow', function () {
                $('#display-curernt-location').fadeIn('fast');
            });
        }        
        if (xhr.status === 401) {
            if ($('#login-modal').length) {
                $('#loginfrm #uname').val('');
                $('#loginfrm #password').val('');
                $('#login-modal').modal();
            }
        }
        else if (xhr.status === 307 || xhr.status === 308) {
            if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
               /* if (notif !== undefined) {
                    notif({
                        width: 500,
                        msg: data.msg,
                        type: 'success',
                        position: 'right',
                        timeout: 6000,
                    });
				}*/
            }
            if (data !== undefined && data.url !== undefined) {
                setTimeout(function () {
                    window.location.href = data.url;
                }, 2000);
            }
        }
        else if (xhr.status === 200) {	
            if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
                /*if (notif !== undefined) {
                   notif({
                        width: 500,
                        msg: data.msg,
                        type: 'success',
                        position: 'right',
                        timeout: 6000,
                    });
                }*/
				if (CURFORM!=null && CURFORM.data!==undefined && CURFORM.data('errmsg-fld')!==undefined){
					if(CURFORM.data('errmsg-placement')=='append'){
						$(CURFORM.data('errmsg-fld')).append('<div class="alert alert-success alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ data.msg + '</div>')
					} 
					else if(CURFORM.data('errmsg-placement')=='after')
					{
						$(CURFORM.data('errmsg-fld')).after('<div class="alert alert-success alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ data.msg + '</div>')
					} 
					else {
						$(CURFORM.data('errmsg-fld')).before('<div class="alert alert-success alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ data.msg + '</div>')
					}
					
				} else if (CURFORM!=null){
					$(CURFORM).before('<div class="alert alert-success alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ data.msg + '</div>');
				}
            }
        }
        else if (xhr.status === 208 || xhr.status == 205) {
            if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
                /* if (notif !== undefined) {
                    notif({
                        width: 500,
                        msg: data.msg,
                        type: 'warning',
                        position: 'right',
                        timeout: 6000,
                    });
                }*/
            }
        }
        else if (xhr.status === 422 || xhr.status === 400 || xhr.status === 404) {
		    if (CURFORM != undefined && CURFORM !== null && data.error !== undefined && data.error !== null) {
                CURFORM.appendLaravelError(data.error);
                CURFORM = null;
            }
            else if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {	
                /*if (notif !== undefined) {
                    notif({
                        width: 500,
                        msg: data.msg,
                        type: 'error',
                        position: 'right',
                        timeout: 6000,
                    });
                }*/
				$(CURFORM).before('<div class="alert alert-'+((data.msgclass!=undefined)? data.msgclass:'danger')+' alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ data.msg + '</div>');
            }
        }
        else if (xhr.status === 500 && window.CBB.DEBUG) {
            /*if (notif !== data) {
                notif({
                    width: 500,
                    msg: 'Something went wrong',
                    type: 'error',
                    position: 'right',
                    timeout: 6000,
                });
            }*/
        }
    };
    if (! a.CBB.DEBUG) {
        a.CBB.console = a.console;
        a.console = undefined;
    }	
	
	
})(this);

function isEmpty(arg) {
  for (var item in arg) {
    return false;
  }
  return true;
}

var CKEDITOR = CKEDITOR != undefined ? CKEDITOR : null;
var notif, CURFORM = null, CROPPED = false;
var Constants = {};
$.ajaxSetup({
    dataType: 'JSON',
    method: 'POST',
    cache: true,
	/*headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }*/
});
/*
$( document ).on( 'ajaxSend', addLaravelCSRF );

function addLaravelCSRF( event, jqxhr, settings ) {
    jqxhr.setRequestHeader( 'X-XSRF-TOKEN', getCookie( 'XSRF-TOKEN' ) );
}

function getCookie(name) {
    function escape(s) { return s.replace(/([.*+?\^${}()|\[\]\/\\])/g, '\\$1'); };
    var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
    return match ? match[1] : null;
}
*/

if ($('#current-city').val() == "0" && document.location.auto && navigator.geolocation) {
    $.holdReady(true);
    navigator.geolocation.getCurrentPosition(function (d) {
        document.location.position = d.coords;
        $.ajaxSetup({
            dataType: 'JSON',
            method: 'POST',
            cache: true,
            headers: {lat: document.location.position.latitude, lng: document.location.position.longitude}
        });
    });
    $.holdReady(false);
}

$(document).ajaxStart(function () {
    $('body').removeClass('loaded');
	$('.alert-err,div.help-block').remove();
	$('.errmsg').text('').removeClass('errmsg');
});

$(document).ajaxComplete(window.CBB.ajaxComplete);

$.extend({
    updateMeta: function (title, description, image, keys) {
        title = title || null;
        description = description || null;
        image = image || null;
        keys = keys || null;
        if (title) {
            $('meta[namr="title"],meta[property="og:title"]').attr('content', title);
        }
        if (description) {
            $('meta[namr="description"],meta[property="og:description"]').attr('content', description);
        }
        if (image) {
            $('meta[property="og:image"]').attr('content', image);
        }
        if (keys) {
            $('meta[namr="keywords"]').attr('content', image);
        }
    },
    getURLParams: function (url) {
        url = (url !== undefined) ? url : window.location.search;
        var params = {};
        url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (str, key, value) {
            params[decodeURI(key)] = decodeURI(value);
        });
        return params;
    },
    stringify: function stringify(obj) {
        var t = typeof (obj);
        if (t != 'object' || obj === null) {
            if (t == 'string')
                obj = '"' + obj + '"';
            return String(obj);
        } else {
            var n, v, json = [], arr = (obj && obj.constructor == Array);
            for (n in obj) {
                v = obj[n];
                t = typeof (v);
                if (obj.hasOwnProperty(n)) {
                    if (t == 'string')
                        v = '"' + v + '"';
                    else if (t == 'object' && v !== null)
                        v = jQuery.stringify(v);
                    json.push((arr ? '' : '"' + n + '":') + String(v));
                }
            }
            return (arr ? '[' : '{') + String(json) + (arr ? ']' : '}');
        }
    }
});
$.fn.extend({
    pageSwapTo: function (target) {
        $(this).hide('drop', {direction: 'right', opacity: '0'}, 'fast', function () {
            $(target).show('fade', 'fast');
        });
    },
    pageSwapToRight: function (target) {
        $(this).hide('drop', {direction: 'right'}, 'fast', function () {
            $(target).show('fade', {direction: 'right'}, 'fast');
        });
    },
    pageSwapToLeft: function (target) {
        $(this).hide('drop', {direction: 'left'}, 'fast', function () {
            $(target).show('fade', {direction: 'left'}, 'fast');
        });
    },
    checkFileFormat: function (doctypes, str) {		  
        var fname = $(this).val();
        var txtext = fname.split('.')[1];		
       // txtext = '.' + txtext.toLowerCase(); 
        fformats = doctypes.split('|');		
        $('#' + $(this).attr('id') + '_error').remove();		
        if (fformats.indexOf(txtext) == - 1) {
            val = $(this).val('');
            $(this).after('<div class="help-block" id="' + $(this).attr('id') + '_error">' + str + "</div>");
            return false;
        }
        return true;
    },
    checkPincode: function (settings) {
        var _this = $(this);
        _this.settings = $.extend({}, {
            country: _this.closest('select.country'),
            region: _this.closest('select.region'),
            state: _this.closest('select.state'),
            district: _this.closest('select.district'),
            city: _this.closest('select.city'),
            callBack: null
        }, settings);
        _this.on('change', function () {
            if (_this.val() != '') {
                $.ajax({
                    url: document.location.USER + 'check-pincode',
                    data: {pincode: _this.val()},
                    success: function (op) {
                        _this.add($(_this.settings.country), op.country.id, op.country.value, true);
                        _this.add($(_this.settings.region), op.region.id, op.region.value, true);
                        _this.add($(_this.settings.state), op.state.id, op.state.value, true);
                        _this.add($(_this.settings.district), op.district.id, op.district.value, true);
                        $(_this.settings.city).empty();
                        $.each(op.cities, function (k, v) {
                            _this.add($(_this.settings.city), v.id, v.value, false);
                        });
                        if (_this.settings.callBack != null) {
                            _this.settings.callBack();
                        }
                    }
                });
            }
        });
        _this.add = function (ele, value, label, reset) {
            reset = reset || false;
            if (reset) {
                ele.empty();
            }
            ele.append($('<option>', {value: value}).text(label));
        };
    },
    resetForm: function () {
        var form = $(this);
        $('.fa-eye', form).each(function () {
            $(this).parent('span').siblings('input').attr('type', 'password');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        });
        $.each($('input', form), function () {
            var cur = $(this);
            if (! $(this).hasClass('ignore-reset'))
            {
                switch ($(this).attr('type'))
                {
                    case 'text':
                    case 'password':
                    case 'textarea':
                    case 'hidden':
                    case 'number':
                    case 'tel':
                    case 'url':
                    case 'email':
                    case 'date':
                        cur.val(cur.defaultValue);
                        break;
                    case 'radio':
                    case 'checkbox':
                        cur.prop('checked', false);
                        break;
                    case 'file':
                        $('#' + cur.attr('id') + '-preview').attr('src', cur.data('default'));
                        break
                }
            }
            if ($('[name="' + cur.attr('name') + '"]', form).data('err-msg-to') != undefined) {
                $($('[name="' + cur.attr('name') + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
            }
            else {
                $('span[for="' + cur.attr('name') + '"]', form).attr({for : '', class: ''}).empty();
            }
        });
        $.each($('p.form-control-static', form), function () {
            if (! $(this).hasClass('ignore-reset'))
            {
                $(this).empty();
            }
        });
        $.each($('textarea', form), function () {
            var Cur = $(this);
            $(this).val(Cur.defaultValue);
            if (! Cur.hasClass('ignore-reset'))
            {
                if (CKEDITOR != undefined && CKEDITOR.instances[Cur.attr('id')] != undefined) {
                    var data = CKEDITOR.instances[Cur.attr('id')].element.$.defaultValue;
                    CKEDITOR.instances[Cur.attr('id')].setData(data);
                }
                else {
                    Cur.val(Cur.defaultValue);
                }
            }
            if ($('[name="' + $(this).attr('name') + '"]', form).data('err-msg-to') != undefined) {
                $($('[name="' + $(this).attr('name') + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
            }
            else {
                $('span[for="' + $(this).attr('name') + '"]', form).remove();
            }
        });
        $.each($('select', form), function () {
            if (! $(this).hasClass('ignore-reset'))
            {
                $(this).val((this).defaultValue);
            }
            if ($('[name="' + $(this).attr('name') + '"]', form).data('err-msg-to') != undefined) {
                $($('[name="' + $(this).attr('name') + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
            }
            else {
                $('span[for="' + $(this).attr('name') + '"]', form).remove();
            }
        });
        return form;
    },
    pwdShowHide: function (type) {
        var _this = this;
        _this.changetype = 'text';
        _this.type = type || this.changetype;
        _this.siblings('span').on('click', function () {
            var _cur = $(this).siblings('input');
            if (_cur.attr('type') == 'password') {
                _cur.attr('type', _this.changetype);
                _cur.siblings('span').find('i').removeClass().addClass('fa fa-eye');
            }
            else {
                _cur.attr('type', 'password');
                _cur.siblings('span').find('i').removeClass().addClass('fa fa-eye-slash');
            }
        });
        return _this;
    },
    appendLaravelError: function (error) {
        var form = this;
        if (error != undefined) {
            $.each(error, function (k, e) {
                if ($('[name="' + k + '"]', form).data('err-msg-to') != undefined) {
                    $($('[name="' + k + '"]', form).data('err-msg-to'), form).empty().attr({for : '', class: ''}).empty();
                }
                if ($('[name="' + k + '"]', form).hasClass('noValidate') == false)
                {
                    if ($('[name="' + k + '"]', form).length == 1) {
                        if ($('[name="' + k + '"]', form).data('err-msg-to') != undefined) {
                            /* display errmsg on single container */
                            $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
                            $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : k, class: 'errmsg'}).append(e);
                            $('[name="' + k + '"]', form).on('change', function () {
                                $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
                                if ($(this).data('this-or-that') != undefined) {
                                    $('span[for="' + $(this).data('this-or-that') + '"]', form).attr({for : '', class: ''}).empty();
                                }
                            });
                        }
                        else {
                            $('span[for="' + k + '"]', form).remove();
                            $('[name="' + k + '"]', form).after($('<span>').attr({for : k, class: 'errmsg'}).html(e)).on('change', function () {
                                $('span[for="' + $(this).attr('name') + '"]', form).remove();

                                if ($(this).data('this-or-that') != undefined) {
                                    $('span[for="' + $(this).data('this-or-that') + '"]', form).remove();
                                }
                            });
                        }
                    }
                    else if ($('[name="' + k + '"]', form).length > 1) { /* display errmsg for radio control */
                        if ($('[name="' + k + '"]', form).data('err-msg-to') != undefined) {
                            $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
                            $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : k, class: 'errmsg'}).append(e);
                            $('[name="' + k + '"]', form).on('change', function () {
                                $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
                                if ($(this).data('this-or-that') != undefined) {
                                    $('span[for="' + $(this).data('this-or-that') + '"]', form).attr({for : '', class: ''}).empty();
                                }
                            });
                        }
                        else
                        {
                            $('span[for="' + k + '"]', form).remove();
                            $('#' + k + '_errmsg', form).attr({for : k, class: 'errmsg'}).html(e);
                            $('[name="' + k + '"]', form).on('change', function () {
                                $('span[for="' + $(this).attr('name') + '"]', form).remove();
                                if ($(this).data('this-or-that') != undefined) {
                                    $('span[for="' + $(this).data('this-or-that') + '"]', form).remove();
                                }
                            });
                        }
                    }
                }
            });
        }
        return form;
    }
    ,
    serializeObject: function ()
    {  
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (! o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
		console.log(o);
        return o;
    }
    ,
    addOptions: function (data, selected, reset, callback) {
        selected = selected || [];
        reset = reset || true;
        callback = callback || null;
        var _this = $(this);
        if (_this.attr('data-selected') != undefined) {
            selected = $.merge(_this.attr('data-selected').split(','), selected);
            _this.removeAttr('data-selected');
        }
        if (reset) {
            _this.empty();
        }
        $.each(data, function (k, e) {
            _this.append($('<option>', $.extend({}, {value: k}, (selected.indexOf(k) >= 0 ? {selected: selected} : {}))).text(e));
        });
        if (callback) {
            callback();
        }
        return _this;
    },
    setCountDown: function () {
        window.timers = [];
        var CURTimer = $(this);
        var countDownDate = new Date(CURTimer.data('expired_on')).getTime();
        window.timers[CURTimer.attr('id')] = setInterval(function () {
            var now = new Date().getTime(), distance = countDownDate - now, days = Math.floor(distance / (1000 * 60 * 60 * 24)), hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)), minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)), seconds = Math.floor((distance % (1000 * 60)) / 1000);
            CURTimer.text(days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's ');
            if (distance < 0) {
                clearInterval(window.timers[CURTimer.attr('id')]);
                CURTimer.text('EXPIRED');
            }
        }, 1000);
    }
});
if ($.fn.dataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
        bPaginate: true,
        bInfo: true,
        bSort: true,
        processing: true,
        serverSide: true,
        bStateSave: true,
        bFilter: false,
        ordering: true,
        lengthChange: false,
        pagingType: 'input_page',
        sDom: 't' + '<"col-sm-6 bottom info align"li>r<"col-sm-6 info bottom text-right"p>',
        order: [[0, 'desc']],
        oLanguage: {
            sLengthMenu: '_MENU_',
            sInfo: '_START_ to _END_ of _TOTAL_'
        },
        ajax: {
            type: 'POST'
        }
    });
    $.fn.dataTable.ext.errMode = function (settings, techNote, message) {
        //throw new Error(message);
        return true;
    };
}
$(document).ready(function () {
    $('body').addClass('loaded');
    if ($.fn.datepicker) {
        $('#from').datepicker().on('changeDate', function (date) {
            $('#to').datepicker('setStartDate', date.date);
        });
        $('#to').datepicker().on('changeDate', function (date) {
            $('#from').datepicker('setEndDate', date.date);
        });
    }
    if (window.location.hash != '' && $.fn.tab) {
        $('.nav-tabs a[href="' + window.location.hash + '"]').tab('shown.bs.tab');
    }

    if (window.location.hash != '' && $.fn.tab) {
        $('.nav-pills a[href="' + window.location.hash + '"]').tab('show').trigger('shown.bs.tab');
    }

    $('img.editable-img').on('click', function (e) {
        e.preventDefault();
        $($(this).data('input')).trigger('click');
    });
    if (CKEDITOR != undefined && CKEDITOR) {
        $('.ckeditor').each(function () {
            var ele = $(this);

            var editor = CKEDITOR.instances[ele.attr('id')];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace(ele.attr('id'));

            // CKEDITOR.replace(ele.attr('id'));
            ele.on('change', function () {
                var ele = $(this);
                CKEDITOR.instances[ele.attr('id')].setData(ele.val());
            });
        });
        CKEDITOR.on('instanceReady', function (e) {
            e.editor.on('change', function () {
                e.editor.updateElement();
                document.getElementById($(e.editor.element.$).attr('id')).checkValidity();
                $(e.editor.element.$).trigger('input');
            });
        });
    }
    if ($.fn.iCheck) {
        $('input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%',
            labelHover: false
        });
    }
    $('input[type="checkbox"]').on('ifClicked', function (e) {
        $(this).trigger('click');
    });
    
    $('.logout').on('click', function (e) {
        e.preventDefault();
        var Cur = $(this);
        $.ajax({
            url: Cur.attr('href'),
            success: function (op) {
                document.location.href = op.url;
            }
        });
    });
    $('.modal').on('show.bs.modal', function (e) {
        if (typeof (e.relatedTarget) != undefined && $(e.relatedTarget).attr('href') != undefined) {
            window.location.hash = $(e.relatedTarget).attr('href');
        }
    });
    $('.modal').on('hide.bs.modal', function (e) {
        window.location.hash = '';
    });
	
	$(document).on('click','.acc-tab .acc-title',function(e){
	    e.preventDefault();
		if($(this)){
			$(this).next().slideToggle();
			if($(this).find('i').hasClass('fa fa-minus')){
				$(this).find('i').attr('class','fa fa-plus');
			}else{
				$(this).find('i').attr('class','fa fa-minus');
			}										
		}
	});

	$.extend({/* $('[data-toggle="tooltip"]').tooltip();   */
		paymentGateWay: function (settings) {
			var _this = this;
			_this.settings = settings;
			
			console.log(settings);
			switch (_this.settings.payment_type) {

				case 'cashfree':
					$('#cashfree', '#payment-forms').show();
					$.cashfree(_this.settings);
					break;
				case 'pay-u':
					$('#pay-u', '#payment-forms').show();
					$.payU(_this.settings);
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
	$.fn.selectDOB = function(options) {
		
		var settings = $.extend({
            // These are the defaults.
			yearSel: "0",           
			monSel: "0",
            daySel: "0",
			dob: "", 
			inpDateFormat: 'yyyy-mm-dd'
        }, options );
		
		var _this = $(this);
		var yearFld = $('#dob_year',_this);
		var monFld = $('#dob_month',_this);
		var dayFld = $('#dob_day',_this);
		var dobFld = $('#dob',_this);		
		var now = new Date();	
		var year_str = '<option value="">Year</option>';
		var mon_str = '<option value="">Month</option>';
		var day_str = '<option value="">Day</option>';
		var months = {'1': 'Jan', '2': 'Feb', '3': 'Mar', '4': 'Apr', '5': 'May', '6': 'June', '7': 'July', '8': 'Aug', '9': 'Sept', '10': 'Oct', '11': 'Nov', '12': 'Dec'};		

		if(settings.dob!=''){
			settings.yearSel = settings.dob.split('-')[0];
			settings.monSel = $.trim(settings.dob.split('-')[1]);
			settings.daySel = $.trim(settings.dob.split('-')[2]);
			dobFld.val(settings.dob);
		}
		console.log(settings)
		yearFld.on('change',function () {
			mStr = mon_str;
			for (var i = 1; i <= 12; i++){
				mStr = mStr + '<option value="' + i + '"';
				if(i==settings.monSel){
					mStr = mStr + ' selected="selected" ';
				}
				mStr = mStr + '>' + months[i] + '</option>';
			}
			monFld.html(mStr);
			if(settings.monSel!=''){
				monFld.trigger('change');
			} else {
				dayFld.val('');	
			}				
		});	
			
		monFld.on('change',function () {
			var year = parseInt(yearFld.val());
			var month = parseInt(monFld.val());
			dStr = day_str;
			for (var i = 1; i <= (new Date(year, month, 0).getDate()); i++)
			{
				dStr = dStr + '<option value="' + i + '"';
				if(i==settings.daySel){
					dStr = dStr + ' selected="selected" ';
				}
				dStr = dStr + '>' + i + '</option>';
			}
			dayFld.html(dStr);			
		});

		dayFld.on('change',function () {		
			var dob = yearFld.val() + '-' + monFld.val() + '-' + dayFld.val();
			dobFld.val(dob);
		});
		
		for (var i = (now.getFullYear() - 13); i >= 1908; i--){
			year_str = year_str + '<option value="' + i + '"';
			if(i==settings.yearSel){
				year_str = year_str + ' selected="selected" ';
			}
			year_str = year_str + '>' + i + '</option>';	
		}
		
		yearFld.html(year_str);	
		
		if(settings.yearSel!=''){			
			yearFld.trigger('change');
		}	
 
    };	
});


function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57 || charCode == 46)) {
        return false;
    }
    return true;
}
function alphaNumeric_withspace(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || code == 32 || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function alphaNumeric_specialchar(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || code == 32 || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 9 || code == 45 || code == 95 || code == 43 || code == 38 || code == 40 || code == 41 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function alphaBets(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || code == 116 || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function alphaBets_withspace(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 116 || code == 32 || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function validateRegno(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 9 || code == 8 || code == 45 || code == 92 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function alphaNumeric_withoutspace(e) {
    var code = e.charCode ? e.charCode : e.keyCode;
    if (((code >= 65 && code <= 90) || (code >= 97 && code <= 122) || (code >= 48 && code <= 57) || (code == 37 && e.charCode == 0) || (code == 39 && e.charCode == 0) || code == 9 || code == 8 || (code == 46 && e.charCode == 0))) {
        return true;
    }
    return false;
}
function isNumberKeydot(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 46 && charCode > 31 && (charCode > 57 || charCode < 48 || charCode == 46)) {
        return false;
    }
    return true;
}
function RestrictSpace(evt) {
    if (event.keyCode == 32) {
        return false;
    }
}
function selectallchk(evt) {
    if (evt.checked) {
        $('.checkbox').each(function () {
            this.checked = true;
        });
    } else {
        $('.checkbox').each(function () {
            this.checked = false;
        });
    }
}
function selectall() {
    /*
     if(evt.checked) {
     $('.checkbox').each(function() {
     this.checked = true;
     });
     }else{
     $('.checkbox').each(function() {
     this.checked = false;
     });
     }*/
}
function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}
function stripHtmlTags(string) {
    return string.replace(/(<([^>]+)>)/ig, '');
}
function addSlashes(string) {
    return string.replace(/\\/g, '\\\\').
            replace(/\u0008/g, '\\b').
            replace(/\t/g, '\\t').
            replace(/\n/g, '\\n').
            replace(/\f/g, '\\f').
            replace(/\r/g, '\\r').
            replace(/'/g, '\\\'').
            replace(/"/g, '\\"');
}
function stripSlashes(string) {
    return string.replace(/\\/g, '');
}
function checkformat(ele, doctypes, str) {
    /* ele=>element, doctypes=> jpg,jpeg,png, str=>'Please select valid file format' */

    txtext = ele;
    txtext = txtext.toString().toLowerCase();
    fformats = doctypes.split('|');
    if (fformats.indexOf(txtext) == - 1) {
        return false;
    } else {
        return true;
    }
}
function addDropDownMenu(arr, text, class_name) {
    arr = arr || [];
    text = text || false;
    class_name = class_name || 'actions';
    var content = $('<div>', {class: 'btn-group'}).append($('<button>').attr({class: 'btn btn-sm btn-primary dropdown-toggle', 'data-toggle': 'dropdown'})
            .append([$('<i>', {class: 'fa fa-gear'}), $('<span>').attr({class: 'caret'})]),
            $('<ul>').attr({class: 'dropdown-menu pull-right', role: 'menu'}).append(function () {
        var options = [], data = {};
        $.each(arr, function (k, v) {
            data = {};
            if (! v.redirect) {
                v.class = v.class || (v.url ? class_name : 'show-modal');
            }
            else {
                data['target'] = v.target || '_blank';
            }
            v.url = v.url || '#';
            v.data = v.data || {};
            $.each(v.data, function (key, val) {
                data['data-' + key] = val;
            });
            options.push($('<li>').append($('<a>', {class: v.class}).attr($.extend({href: v.url}, data)).text(v.label)));
        });
        return options;
    }));
    return text ? content[0].outerHTML : content;
}

function addDropDownMenuActions(e, callback) {
    var Ele = e, data = Ele.data();
    callback = callback || null;
    if (Ele.data('confirm') == undefined || (Ele.data('confirm') != null && Ele.data('confirm') != '' && confirm(Ele.data('confirm')))) {
        if (data.confirm != undefined) {
            delete data.confirm;
        }
        $.ajax({
            url: Ele.attr('href'),
            data: data,
            success: function (data) {
                if (callback !== null) {
                    callback(data);
                }
            }
        });
    }
}
function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ('0' + parseInt(x).toString(16)).slice(- 2);
    }
    return '#' + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}


function removePreloader(){
	$('body').toggleClass('loaded');
}

function loadPreloader(){
	$('body').toggleClass('loaded');
}

/* $(document).on('click','#reflink_cpybtn',function(e){  
    var copyText = $("#referral-link");  
    copyText.select();  
    document.execCommand("copy");  
    alert("Copied the link: " + copyText.val());
}); */

$(document).on('click','#reflink_cpybtn',function(e){  
    var code = $("#referral-link").val();  
    const el = document.createElement('textarea');
	el.value = code;
	document.body.appendChild(el);
	el.select();
    document.execCommand("copy");  
    alert("Copied the code: " + code);
	document.body.removeChild(el);
});

$(document).on('click','#refcode_cpybtn',function(e){  
    var code = $("#referral-code").val();  
    const el = document.createElement('textarea');
	el.value = code;
	document.body.appendChild(el);
	el.select();
    document.execCommand("copy");  
    alert("Copied the code: " + code);
	document.body.removeChild(el);
});

$(document).on('click','.logoutBtn',function(e){
	e.preventDefault();
	$.ajax({        
        url: $(this).attr('href'),        
		dataType:'JSON',
        success: function (op) {
            window.location.href = op.url;
        }
    });
});

$(document).on('click','.pwdHS',function(e){
	e.preventDefault();
	if($($(this).data('target')).attr('type')=='password'){
		$($(this).data('target')).attr('type','text');
		$('i',$(this)).removeClass('fa-eye-slash').addClass('fa-eye');
	}
	else {
		$($(this).data('target')).attr('type','password');
		$('i',$(this)).removeClass('fa-eye').addClass('fa-eye-slash');
	}		 
});