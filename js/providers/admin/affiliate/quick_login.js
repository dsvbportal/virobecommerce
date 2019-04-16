var UnameChecked = MobileChecked = EmailChecked = false;
$(document).ready(function () {
    $('#mobile').keypress(function (e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            $('#errmsg').html('Enter Digits Only').show().fadeOut('slow');
            return false;
        }
    });

    $('#submit').click(function () {
		
        $('#quick_login').validate({
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            rules: {
                uname: 'required',
                last_name: 'required',
            },
            messages: {
                uname: 'Please enter Uname',
            },
            submitHandler: function (form, event) {
                event.preventDefault();
					var datastring = $(form).serialize();
					$.ajax({
							url: $(form).attr('action'),
							type: 'POST',
							data: datastring,
							dataType: 'JSON',
							beforeSend: function () {
								$('#submit').attr('disabled', 'disabled');
							},
							success: function (data)
							{
								if(data.status == 'ok'){
								$('#submit').removeAttr('disabled');
		                         $('#quick_login').after('<div class="col-sm-12 alert alert-success"><a href="#" class="close" area-label="close" data-dismiss="alert">&times;</a>' + data.msg + '</div>');
                                  $('.alert').fadeOut(5000); 
									window.location.href=data.url;
								}else{
									$('#err').addClass('text-danger').html('Affiliate account not found');
									$('#submit').removeAttr('disabled');
									//$('#uname').val('');
								}
							},
							error: function () {
								$('#submit').removeAttr('disabled');
								alert('Something went wrong');
								return false;
							}
					});
                }
               /*  }
                else {
                    if (!UnameChecked) {
                        $('#uname').after('<div for="uname" class="text-success uname-err">Please Enter the User name</div>')
                    }
                    if (!MobileChecked) {
                        $('#mobile').after('<div for="mobile" class="text-success mobile-err">Please Enter the Mobile No.</div>')
                    }
                    if (!EmailChecked) {
                        $('#email').after('<div for="email" class="text-success email-err">Please Enter the Email.</div>')
                    }
                } 
            }*/
        });
    });
 /*  $('#create_user  #country_id').loadSelect({
	 
		firstOption: {key: '', value: '--Select--'},
		firstOptionSelectable: false, 
		url: window.location.ADMIN +'country-list/active', 
		key: 'country_id',
		value: 'country',
		//copyTo:[{selector:'#state_editfrm #country_id',key: 'country_id',value:'country',autoSelect:false}] 
	});  */

});	
//var loadedSelectValues = [];
/*jQuery.fn.extend({
     loadSelect: function (options) {
        var _this = this;
        if (! _this.length || ! _this.is('select')) {
            console.warn("Invalid Selector '" + _this.selector + "'");
            return false;
        }

        _this.xhr = false;
        _this.options = {
            values: [],
            url: '',
            key: 'key',
            value: 'value',
            optionData: [], //{key: '', value: ''}
            selected: false,
            palceHolder: true,
            firstOption: {key: '', value: '--Select--'},
            firstOptionSelectable: false,
            dependingSelector: [],
            notexistIn: [],
            copyTo: [], //{selector: false, key: 'key', value: 'value',autoSelect:true}
            data: {},
            cache: true,
            success: null
        };
        for (var key in _this.options) {
            if (_this.data(key) != undefined && _this.data(key) != '' && _this.data(key) != null) {
                _this.options[key] = _this.data(key);
            }
        }
        $.extend(_this.options, options);
        if (! (_this.selector in loadedSelectValues)) {
            loadedSelectValues[_this.selector] = {parentValues: _this.options.values, childernsValues: []};
        }
        _this.update = function (options) {
            if (_this.xhr && _this.xhr.readyState != 4) {
                _this.xhr.abort();
            }
            var option = '';
            _this.empty();
            if (_this.options.palceHolder) {
                _this.html($('<option>').val(_this.options.firstOption.key).text(_this.options.firstOption.value).attr('hidden', ! _this.options.firstOptionSelectable));
                for (var key in _this.options.copyTo) {
                    $(_this.options.copyTo[key].selector).html($('<option>').val(_this.options.firstOption.key).text(_this.options.firstOption.value).attr('hidden', ! _this.options.firstOptionSelectable));
                }
            }
            for (var id in options) {
                if (_this.options.notexistIn.length <= 0 || (_this.options.notexistIn.length && _this.options.notexistIn.indexOf(options[id][_this.options.key]) <= - 1)) {
                    option = $('<option>').val(options[id][_this.options.key]).text(options[id][_this.options.value])
                    if (_this.options.selected && ((_this.hasOwnProperty('multiple') && _this.options.selected.indexOf(options[id][_this.options.key]) >= 0) || _this.options.selected == options[id][_this.options.key])) {
                        option.attr('selected', 'selected');
                    }
                    if (_this.options.optionData.length) {
                        for (var k in _this.options.optionData) {
                            option.attr('data-' + _this.options.optionData[k].key, options[id][_this.options.optionData[k].value]);
                        }
                    }
                    _this.append(option);
                    if (_this.options.selected && ((_this.hasOwnProperty('multiple') && _this.options.selected.indexOf(options[id][_this.options.key]) >= 0) || _this.options.selected == options[id][_this.options.key])) {
                        _this.trigger('change');
                    }
                    if (_this.options.copyTo) {
                        for (var key in _this.options.copyTo) {
                            option = $('<option>').val(options[id][_this.options.copyTo[key].key])
                                    .text(options[id][_this.options.copyTo[key].value])
                                    .attr('class', 'pid_' + options[id][_this.options.key]);
                            $(_this.options.copyTo[key].selector).append(option);
                        }
                    }
                }
            }
            if (_this.options.success != null) {
                _this.options.success();
            }
        }
        _this.load = function () {
            var data = _this.options.data;
            if (_this.options.dependingSelector != []) {
                for (var key in _this.options.dependingSelector) {
                    var val = $(_this.options.dependingSelector[key]).val(),
                            selectorID = _this.dependingSelectorKey(_this.options.dependingSelector[key]);
                    data[selectorID] = val;
                }
            }
            _this.xhr = $.ajax({
                type: 'POST',
                url: _this.options.url,
                data: data,
                dataType: 'JSON',
                beforeSend: function () {
                    _this.html('<option value="" hidden="hidden">Loading...</option>');
                },
                success: function (options) {
                    if (_this.options.dependingSelector.length > 0) {
                        for (var key in _this.options.dependingSelector) {
                            var parent_id = $(_this.options.dependingSelector[key]).val(),
                                    selectorID = _this.dependingSelectorKey(_this.options.dependingSelector[key]);
                            if (loadedSelectValues[_this.selector].childernsValues[selectorID] == undefined) {
                                loadedSelectValues[_this.selector].childernsValues[selectorID] = [];
                            }
                            loadedSelectValues[_this.selector].childernsValues[selectorID][parent_id] = options;
                            _this.update(loadedSelectValues[_this.selector].childernsValues[selectorID][parent_id]);
                        }
                    }
                    else {
                        loadedSelectValues[_this.selector].parentValues = options;
                        _this.update(loadedSelectValues[_this.selector].parentValues);
                    }
                }
            });
        };
        _this.dependingSelectorKey = function (id) {
            return id.substr(id.lastIndexOf('#') + 1);
        };
        if (_this.options.dependingSelector.length > 0) {
            if (_this.options.palceHolder) {
                _this.html($('<option>').val(_this.options.firstOption.key).text(_this.options.firstOption.value).attr('hidden', ! _this.options.firstOptionSelectable));
                for (var key in _this.options.copyTo) {
                    $(_this.options.copyTo[key].selector).html($('<option>').val(_this.options.firstOption.key).text(_this.options.firstOption.value).attr('hidden', ! _this.options.firstOptionSelectable));
                }
            }
            for (var key in _this.options.dependingSelector) {
                $(_this.options.dependingSelector[key]).on('change', function () {
                    var parent_id = $(this).val(), selectorID = $(this).context.id;
                    if (_this.options.cache == false || loadedSelectValues[_this.selector].childernsValues.length == 0 || loadedSelectValues[_this.selector].childernsValues[selectorID] == undefined || loadedSelectValues[_this.selector].childernsValues[selectorID].length == 0 || (loadedSelectValues[_this.selector].childernsValues[selectorID] != undefined && loadedSelectValues[_this.selector].childernsValues[selectorID][parent_id] == undefined)) {
                        _this.load();
                    }
                    else {
                        _this.update(loadedSelectValues[_this.selector].childernsValues[selectorID][parent_id]);
                    }
                });
            }
        }
        else {
            _this.html('<option value="" hidden="hidden">Loading...</option>');
            if (_this.options.palceHolder) {
                for (var key in _this.options.copyTo) {
                    $(_this.options.copyTo[key].selector).html($('<option>').val(_this.options.firstOption.key).text(_this.options.firstOption.value).attr('hidden', ! _this.options.firstOptionSelectable));
                }
            }
            if (_this.options.cache == false || loadedSelectValues[_this.selector].parentValues.length == 0) {
                _this.load();
            }
            else {
                _this.update(loadedSelectValues[_this.selector].parentValues);
            }
        }
        if (_this.options.copyTo) {
            _this.on('change', function () {
                for (var key in _this.options.copyTo) {
                    if (_this.options.copyTo[key].autoSelect) {
                        $('option:selected', _this.options.copyTo[key].selector).removeAttr('selected');
                        $('option.pid_' + _this.val(), _this.options.copyTo[key].selector).attr('selected', 'selected');
                    }
                    else if (_this.options.copyTo[key].selected != '' && _this.options.copyTo[key].selected != null) {
                        $(_this.options.copyTo[key].selector).val(_this.options.copyTo[key].selected);
                    }
                }
            });
        }
    } 
});
*/