(function (a) {
    a.TSP = {DEBUG: true, data: {}};
    a.TSP.loaderImg 	= 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    a.location.CurPage  = null;
    a.location.BASE 	= $('base').attr('href');
    a.location.API = {
        BASE: a.location.BASE + 'api/v1/',
        AFF: a.location.BASE + 'api/v1/affiliate/',
        SELLER: a.location.BASE + 'api/v1/seller/',
        CUSTOMER: a.location.BASE + 'api/v1/customer/'
    };
    a.location.AddToUrl = function (title, url) {		
        if (typeof (a.history.pushState) !== undefined) {
            var href = a.location.href, c_url = (href.indexOf('?') > 1) ? href.substring(0, href.indexOf('?')) : href;
            a.location.CurPage = {Page: title, Url: c_url + ((url !== '') ? '?' + url : '')};
            a.document.title = title;
            a.history.pushState(a.location.CurPage, a.location.CurPage.title, a.location.CurPage.Url);
        }
    };
    a.location.ChangeUrl = function (title, url) {		
        if (typeof (a.history.pushState) !== undefined) {			
            a.location.CurPage = {Page: title, Url: url};
            a.document.title = title;
            a.history.pushState(a.location.CurPage, a.location.CurPage.title, a.location.CurPage.Url);
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
        if (e.originalEvent.state !== null) {
            a.document.title = e.originalEvent.state.Page;
            a.history.pushState(e.originalEvent.state, e.originalEvent.state.Page, e.originalEvent.state.Url);
        }
    });
    a.Error.stackTraceLimit = a.Infinity;
    a.location.PINCODE = (a.localStorage.getItem('pincode') !== null && a.localStorage.getItem('pincode') !== undefined && localStorage.getItem('pincode') !== 'undefined') ? localStorage.getItem('pincode') : null;
    a.location.setPincode = function (pincode) {
        a.localStorage.setItem('pincode', pincode);
        a.location.PINCODE = pincode;
    };
    a.document.addEventListener('invalid', function (event) {
        event.preventDefault();
        a.TSP.customValidation(event);
    }, true);
    a.document.addEventListener('input', function (event) {
        a.TSP.customValidation(event);
    }, true);
    a.document.addEventListener('change', function (event) {
        a.TSP.customValidation(event);
    }, true);
    a.TSP.customValidation = function (e) {
        var msg = '', _this = null;
        if (e.srcElement != undefined) {
            _this = e.srcElement;
        }
        if (e.target != undefined) {
            _this = e.target;
        }
        if (_this != null) {
            if (_this.dataset['errMsgTo'] != undefined) {
                $(_this.dataset['errMsgTo']).attr({for : '', class: ''}).empty();
            }
            else {
                $('span[for="' + _this.name + '"]').remove();
            }
            if (! _this.validity.valid) {
                if (_this.validity.typeMismatch) {
                    msg = _this.dataset['typemismatch'];
                } else if (_this.validity.badInput) {
                    msg = _this.dataset['valuemissing'];
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
                }
            }
            msg = msg != undefined ? msg : '';
            _this.setCustomValidity(msg);
            if (_this.validationMessage != undefined && _this.validationMessage != '') {
                if ($('[name="' + _this.name + '"]').length >= 1) {
                    if (_this.dataset['errMsgTo'] != undefined) {
                        $(_this.dataset['errMsgTo']).attr({for : _this.name, class: 'errmsg'}).append(_this.validationMessage);
                    }
                    else {
                        $('[name="' + _this.name + '"]').after($('<span>').attr({for : _this.name, class: 'errmsg'}).html(_this.validationMessage));
                    }
                }
            }
        }
    };

    a.onerror = function (msg, fileName, line, col, error) {
		//console.log(msg+' '+fileName+' '+line+' '+col+' '+error);
        //console.warn(msg, fileName, line, col, error !== undefined && error.stack !== undefined ? error.stack : []);
      /*  $.ajax({
            data: {msg: msg, file: fileName, line: line, col: col, trace: error != undefined && error.stack != undefined ? error.stack : null},
            url: a.location.BASE + 'js-exceptions'
        });
        return true; */
    };
    a.addEventListener('error', function (e) {
       /* $.ajax({
            data: {msg: e.message, file: e.filename, line: e.lineno, col: e.colno, trace: e.error != undefined && e.error.stack != undefined ? e.error.stack : null},
            url: a.location.BASE + 'js-exceptions'
        });
        return true;*/
    });
	CURFORM = '',
    a.TSP.ajaxComplete = function (event, xhr, settings) {
		/*CURFORM.data('errmsg-fld','#selector');
		CURFORM.data('errmsg-placement','before');*/
        var data = xhr.responseJSON;
        $('#loader-wrapper,#loader').fadeOut(500);
        if (data !== undefined && data !== null && data.UserDetails !== undefined) {
            UserDetails[ACCOUNT_TYPE] = data.UserDetails;
            localStorage.setItem('UserDetails', JSON.stringify(UserDetails));
        }
        if (xhr.status === 401) {
            UserDetails[ACCOUNT_TYPE] = {token: UserDetails[ACCOUNT_TYPE].token};
            $('#loginfrm #user_login').val('');
            $('#loginfrm #user_password').val('');
            $('#login_modal').modal();
        }
        else if (xhr.status === 308) {
            if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
               /* if (notif !== undefined) {
                    notif({
                        msg: data.msg,
                        type: 'success',
                        position: 'right'
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
               /*  if (notif !== undefined) {
                    notif({
                        msg: data.msg,
                        type: 'success',
                        position: 'right'
                    });
                } */
				if (CURFORM.data!==undefined && CURFORM.data('errmsg-fld')!==undefined){
					if(CURFORM.data('errmsg-placement')=='append'){
						$(CURFORM.data('errmsg-fld')).append(data.msg)
					}
					else if(CURFORM.data('errmsg-placement')=='after')
					{
						$(CURFORM.data('errmsg-fld')).after(data.msg)
					}
					else {
						$(CURFORM.data('errmsg-fld')).before('<div class="alert alert-success alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ data.msg + '</div>')
					}
				} else {
					$(CURFORM).before('<div class="alert alert-success alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ data.msg + '</div>')
				}
            }
        }
        else if (xhr.status === 208) {
            if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
               /* if (notif !== undefined) {
                    notif({
                        msg: data.msg,
                        type: 'warning',
                        position: 'right'
                    });
                }*/
            }
        }
        else if (xhr.status === 406) {
            if (CURFORM != undefined && CURFORM !== null && data !== undefined && data.error !== undefined && data.error !== null) {
                CURFORM.appendLaravelError(data.error);
                CURFORM = null;
            }
            else if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
                /*if (notif !== undefined) {
                    notif({
                        msg: data.msg,
                        type: 'error',
                        position: 'right'
                    });
                }*/
            }
        }
		else if (xhr.status === 422 || xhr.status === 400 || xhr.status === 404) {
			//alert(CURFORM.data('errmsg-fld')) 
			
            if (CURFORM != undefined && CURFORM !== null && data !== undefined &&  data.error !== undefined && data.error !== null && xhr.status === 400) {
                CURFORM.appendLaravelError(data.error);
                CURFORM = null;
            }
            else if (data !== undefined && data.msg !== undefined && data.msg !== '' && data.msg !== null) {
			    if (CURFORM.data('errmsg-fld')==undefined) {
					  /*   notif({
                        msg: data.msg,
                        type: 'error',
                        position: 'right'
                    });   */
					CURFORM.before('<div class="alert alert-danger alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ data.msg + '</div>')

                } else if (CURFORM.data('errmsg-fld')!==undefined){
					if(CURFORM.data('errmsg-placement')=='append'){
						$(CURFORM.data('errmsg-fld')).append(data.msg)
					}
					else if(CURFORM.data('errmsg-placement')=='after')
					{
						$(CURFORM.data('errmsg-fld')).after(data.msg)
					}
					else {
						$(CURFORM.data('errmsg-fld')).before('<div class="alert alert-success alert-err"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>'+ data.msg + '</div>')
					}
				}
            }
        }
        else if (xhr.status === 500 && window.TSP.DEBUG) {
           /* if (notif !== data) {
                notif({
                    msg: data.error.message,
                    type: 'error',
                    position: 'right'
                });
            }*/
        }
    };
    if (! a.TSP.DEBUG) {
        a.TSP.console = a.console;
        a.console = undefined;
    }
})(this);

function getCookie(name) {
    function escape(s) { return s.replace(/([.*+?\^${}()|\[\]\/\\])/g, '\\$1'); };
    var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
    return match ? match[1] : null;
}
CKEDITOR = null;
var API_KEY = '1fc144d7aa3a9a43964428a979de6068';
var notif, CROPPED = false;
UserDetails = {
	customer: undefined, 
	admin: undefined, 
	supplier: undefined, 
	partner: undefined}, 
CURFORM = '', 
ACCOUNT_TYPE = ($('meta[name="account-type"]').length==1? $('meta[name="account-type"]').attr('content'):'');
if (localStorage.getItem('UserDetails') !== null && localStorage.getItem('UserDetails') !== undefined && localStorage.getItem('UserDetails') !== 'undefined') {
    UserDetails = JSON.parse(localStorage.getItem('UserDetails'));
}
var Constants = {};
var PRODUCT_LIST = {};
UserDetails[ACCOUNT_TYPE] = {};
UserDetails[ACCOUNT_TYPE]['token'] = $('meta[name="X-Device-Token"]').attr('content');
localStorage.setItem('UserDetails', JSON.stringify(UserDetails));
$.ajaxSetup({
    dataType: 'JSON',
    method: 'POST',
    //headers: {'X-Device-Token': UserDetails[ACCOUNT_TYPE].token, 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},	
	headers: {'X-Device-Token': UserDetails[ACCOUNT_TYPE].token, 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),'usrtoken':UserDetails[ACCOUNT_TYPE].token, 'api-key':API_KEY},
});


$(document).ajaxStart(function () {
    $('#loader-wrapper,#loader').fadeIn(100);
	$('.alert-err').remove();
});
$(document).ajaxComplete(window.TSP.ajaxComplete);
$.extend({
    isInitGroupRequest: function (force) {
        force = force || false;
        if ($.groupRequests === undefined || force) {
            $.groupRequests = {};
        }
        return $.groupRequests !== undefined ? true : false;
    },
    isGroupAjaxNotEmpty: function () {
        return $.isInitGroupRequest() && $.groupRequests !== {} ? true : false;
    },
    postGroupRequest: function () {
        if ($.isGroupAjaxNotEmpty()) {
            var temp = {};
            for (var requestID in $.groupRequests) {
                temp[requestID] = {id: requestID, url: $.groupRequests[requestID].url !== undefined ? $.groupRequests[requestID].url : null, data: $.groupRequests[requestID].data !== undefined ? $.groupRequests[requestID].data : []};
            }
            $.ajax({
                url: window.location.API.BASE + 'group-request',
                data: {requests: temp},
                success: function (data) {
                    for (var requestID in data.response) {
                        ajaxComplete({}, data.response[requestID], {});
                        $.groupRequests[requestID].success(data.response[requestID].responseJSON);
                    }
                    $.isInitGroupRequest(true);
                }
            });
        }
        else {
            console.warning('Group Request is empty');
        }
    },
    groupAjax: function (settings) {
        if ($.isInitGroupRequest()) {
            $.groupRequests[Math.random().toString(36).substr(2, 5)] = settings;
        }
    },
    getURLParams: function (url) {
		//console.log(url);
        url = (url !== undefined) ? url : window.location.search;
		//console.log(url);
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
    resetForm: function () {
        var form = $(this);
        $.each($('input', form), function () {
            if (! $(this).hasClass('ignore-reset'))
            {
                switch ($(this).attr('type'))
                {
                    case 'text':
                    case 'password':
                    case 'textarea':
                    case 'hidden':
                    case 'number':
                        $(this).val('');
                        break;
                    case 'radio':
                    case 'checkbox':
                        $(this).prop('checked', false);
                        break;
                }
            }
        });
        $.each($('textarea', form), function () {
            $(this).val('');
            if (! $(this).hasClass('ignore-reset'))
            {
                if (CKEDITOR != undefined) {
                    CKEDITOR.instances[$(this).attr('id')].setData('');
                }
            }
        });
        $.each($('select', form), function () {
            if (! $(this).hasClass('ignore-reset'))
            {
                $(this).val('');
            }
        });
    },
    appendLaravelError: function (error) {
        var form = this;
		//console.log(form);
		//console.log(form, error);
        if (error != undefined) {
            $.each(error, function (k, e) {
                //k = k.replace(/(\[\d*\])/ig, '[]');
                //k = k.replace(/(\[\d*\])/ig, '[]');
				//console.log(e);
                if ($('[name="' + k + '"]', form).data('err-msg-to') != undefined) {
                    $($('[name="' + k + '"]', form).data('err-msg-to'), form).attr({for : '', class: ''}).empty();
                }
                if ($('[name="' + k + '"]', form).hasClass('noValidate') == false)
                {
                    if ($('[name="' + k + '"]', form).length == 1) {
                        if ($('[name="' + k + '"]', form).data('err-msg-to') != undefined) {
                            /* display errmsg on single container */
							//console.log(form);
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
    },
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
        return o;
    },
    addOptions: function (data, selected, reset) {
        selected = selected || [];
        reset = reset || true;
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
        return _this;
    }	
});
$.holdReady(true);
var Constants = localStorage.getItem('Constants');
if (Constants === undefined || Constants === null) {
    $.ajax({
        url: window.location.BASE + 'init',
        success: function (data) {
            localStorage.setItem('Constants', JSON.stringify(data.Constants));
            Constants = data.Constants;
            $.holdReady(false);
        }
    });
}
else {
    Constants = JSON.parse(Constants);
    $.holdReady(false);
}

if ($.fn.dataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
        bPaginate: true,
        bProcessing: true,
        bAutoWidth: false,
        bDestroy: true,
        bRetrieve: true,
        bInfo: true,
        bSort: true,
        processing: true,
        serverSide: true,
        bStateSave: true,
        bFilter: true,
        sDom: 't' + '<"col-sm-6 bottom info align"li>r<"col-sm-6 info bottom text-right"p>',
        order: [[0, 'desc']],
        oLanguage: {
            sLengthMenu: '_MENU_',
            sInfo: '_START_ to _END_ of _TOTAL_'
        },
        ajax: {
            method: 'POST'
        }
    });
    $.fn.dataTable.ext.errMode = function (settings, techNote, message) {
        throw new Error(message);
        return true;
    };
}
$(document).ready(function () {
    $('#loader-wrapper,#loader').fadeOut(500);
    if ($.fn.datepicker) {
        $('#from').datepicker().on('changeDate', function (date) {
            $('#to').datepicker('setStartDate', date.date);
        });
        $('#to').datepicker().on('changeDate', function (date) {
            $('#from').datepicker('setEndDate', date.date);
        });
    }
    var url = document.location.toString();
    if (url.match('#') && $.fn.tab) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    });
    if (CKEDITOR != undefined && CKEDITOR) {
        CKEDITOR.on('instanceReady', function (e) {
            e.editor.on('change', function () {
                e.editor.updateElement();
                document.getElementById($(e.editor.element.$).attr('id')).checkValidity();
                $(e.editor.element.$).trigger('input');
            });
        });
    }
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

function searchRow (datalist,searchfield,value){
	return $.grep(datalist, function (obj,k) {		
		return obj[searchfield] == value;
	})[0];
}

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
$(document.body).on('click', '.business_card_info', function (e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var relation_id = $(this).data('relation_id');
    $.ajax({
        data: {id: relation_id},
        url: url,
        beforeSend: function () {
            $('#user_data .modal-body').html('Loading..');
            $('#user_data').modal();
        },
        success: function (data) {
            $('#user_data .modal-body').empty();
            $('#user_data .modal-body').html(data);
            $('#user_data').modal();
        }
    });
});
function checkformat(ele, doctypes, str) {
    txtext = ele;
    txtext = txtext.toString().toLowerCase();
    fformats = doctypes.split('|');
    if (fformats.indexOf(txtext) == - 1) {
        return false;
    } else {
        return true;
    }
}
function upload(url, id)
{
    $('#' + id).uploadFile({
        url: url,
        method: 'POST',
        fileName: 'myfile',
        dragDrop: false,
        multiple: false,
        returnType: 'json',
        allowedTypes: 'wmv,mp3,jpg,png,gif,doc,docx,pdf',
        sequential: false,
        sequentialCount: 1,
        maxFileCount: 5,
        showFileCounter: false,
        showDelete: true,
        showDownload: false,
        showPreview: false,
        abortStr: 'x',
        deletelStr: 'x',
        uploadStr: 'Change',
        onLoad: function (obj) {
            file_id = obj.formGroup;
        },
        onSuccess: function (files, data, xhr, pd) {
            $('#add_images').append($('<input>').attr('type', 'hidden').attr('name', 'files[]').val(data.img_path));
            $('#image_product_id').val(product_id);
        }
    });
}
/* function addDropDownMenu(arr, text) {
    arr = arr || [];
    text = text || false;
    var content = $('<div>', {class: 'btn-group'}).append($('<button>').attr({class: 'btn btn-md btn-primary dropdown-toggle', 'data-toggle': 'dropdown', 'disabled': (arr != '' ?  false:true)})
            .append($('<span>').attr({class: 'caret'})),
            $('<ul>').attr({class: 'dropdown-menu pull-right', role: 'menu'}).append(function () {
        var options = [], data = {};
        $.each(arr, function (k, v) {
            v.class = v.class || (v.url ? 'actions' : 'show-modal');
            v.url = v.url || '#';
            v.data = v.data || {};
            $.each(v.data, function (key, val) {
                data['data-' + key] = val;
            });
            options.push($('<li>').append($('<a>', {class: v.class}).attr($.extend({href: v.url}, data)).text(v.title)));
        });
        return options;
    }));
    return text ? content[0].outerHTML : content;
} */
function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    function hex(x) {
        return ('0' + parseInt(x).toString(16)).slice(- 2);
    }
    return '#' + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function addDropDownMenu(arr, text) {
    arr = arr || [];
    text = text || false;
    var content = $('<div>', {class: 'btn-group'}).append($('<button>').attr({class: 'btn btn-md tbl-drop-down btn-primary dropdown-toggle', 'data-toggle': 'dropdown'})
            .append([$('<i>', {class: 'fa fa-gear'}), $('<span>').attr({class: 'caret'})]),
            $('<ul>').attr({class: 'dropdown-menu pull-right', role: 'menu'}).append(function () {
        var options = [], data = {};
        $.each(arr, function (k, v) {
            data = {};
            if (! v.redirect) {
                v.class = v.class || (v.url ? 'actions' : 'show-modal');
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

function ecomUrl(apiUrl = '',addUrl = '') {	

    var curUrl ='';
	var domain = 'dsvb_affiliate';
	//alert(window.location.origin);
	if(apiUrl != null && apiUrl != undefined){
		if(apiUrl.indexOf('pay_gyft/api/v1/shopping') != -1){
			curUrl = apiUrl.replace('pay_gyft/api/v1/shopping', domain);	
		}else if(apiUrl.indexOf('pay_gyft/api/v1') != -1){
			curUrl = apiUrl.replace('pay_gyft/api/v1', domain);	
		}else if(apiUrl.indexOf('pay_gyft') != -1){
			curUrl = apiUrl.replace('pay_gyft', domain);	
		}else{
			curUrl = window.location.BASE+apiUrl;
		}
	}else{
		curUrl = window.location.BASE;
	}	
	//console.log(curUrl);
	return curUrl;
} 

/* function ecomUrl_old(apiUrl = '',addUrl = '') {	
	var curUrl = window.location.BASE;	
	apiUrl = (apiUrl != null) ? apiUrl:'';
	if(apiUrl.indexOf('?') != -1){
		curUrl = curUrl + addUrl + apiUrl.substring(apiUrl.indexOf('?'));		
	}
	else if(apiUrl.indexOf('pay_gyft') != -1){
		curUrl = apiUrl.replace('http://localhost/pay_gyft/',curUrl);		   
	}
	else{			
		if(addUrl != null && addUrl != ''){
			curUrl = curUrl + addUrl +'/'+ apiUrl;
		}else{
			curUrl = curUrl + apiUrl;
		}            
	} 
	return curUrl;
} */