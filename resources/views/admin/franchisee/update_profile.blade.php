
<form method="post" class="form-horizontal form-validate" name="create_bank" id="edit_prf" action="<?php echo URL::to('/admin/update_franchisee_profile');?>">
    <input type="hidden" id="account_id" name="account_id" value="<?php echo $account_id;?>"/>
    <input type="hidden" id="old_email" name="old_email" value="{{ $user_details->email or ''}}"/>
    <input type="hidden" id="old_mobile" name="old_mobile" value="{{ $user_details->mobile or ''}}"/>
    <input type="hidden" id="district_id" value="{{isset($user_details->district)? $user_details->district:''}}" />
    <input type="hidden" id="state_id" value="{{isset($user_details->state)? $user_details->state:''}}" />
    <input type="hidden" id="city_id" value="{{isset($user_details->city)? $user_details->city:''}}"/>
    <input type="hidden" id="currency_id" value="{{isset($user_details->currency)? $user_details->currency:''}}"/>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="textfield" class="col-sm-6">Username:</label>
            <div class="col-sm-6">
                <div><strong><?php echo (isset($user_details->uname)) ? $user_details->uname : '';?></strong></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="textfield" class="col-sm-6">Support Center Type:</label>
            <div class="col-sm-6">
                <div><strong><?php echo (isset($franchisee_details->franchisee_type_name)) ? $franchisee_details->franchisee_type_name : '';?>
                        <br />
                        @if(isset($franchisee_details->access_country_name) && !empty($franchisee_details->access_country_name))
                        {{ '('.$franchisee_details->access_country_name.')' }}
                        @elseif(isset($franchisee_details->access_region_name) && !empty($franchisee_details->access_region_name))
                        {{ '('.$franchisee_details->access_region_name.')' }}
                        @elseif(isset($franchisee_details->access_state_name) && !empty($franchisee_details->access_state_name))
                        {{ '('.$franchisee_details->access_state_name.')' }}
                        @elseif(isset($franchisee_details->access_district_name) && !empty($franchisee_details->access_district_name))
                        {{ '('.$franchisee_details->access_district_name.')' }}
                        @elseif(isset($franchisee_details->access_city_name) && !empty($franchisee_details->access_city_name))
                        {{ '('.$franchisee_details->access_city_name.')' }}
                        @endif
                    </strong></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="textfield" class="col-sm-6">Deposited:</label>
            <div class="col-sm-6">
                <div><strong><?php echo (isset($user_details->is_deposited) && $user_details->is_deposited == 1) ? 'Yes' : 'No';?></strong></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="textfield" class="col-sm-6">Deposited Amount:</label>
            <div class="col-sm-6">
                <div><strong><?php echo (isset($user_details->deposited_amount)) ? $user_details->deposited_amount.' '.$user_details->currency_code : '';?>
                    </strong></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="textfield" class="col-sm-6">Country Support Center:</label>
            <div class="col-sm-6">
                <div><strong>
                        @if(isset($franchisee_details->country_frname) && !empty($franchisee_details->country_frname) )
                        {{ $franchisee_details->country_frname }}
                        @elseif(isset($franchisee_details->country_frname1) && !empty($franchisee_details->country_frname1))
                        {{$franchisee_details->country_frname1}}
                        @elseif(isset($franchisee_details->country_frname3) && !empty($franchisee_details->country_frname2))
                        {{$franchisee_details->country_frname2}}
                        @elseif(isset($franchisee_details->country_frname3) && !empty($franchisee_details->country_frname3))
                        {{$franchisee_details->country_frname3}}
                        @else
                        {{ '-' }}
                        @endif
                    </strong></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="textfield" class="col-sm-6">Regional Support Center:</label>
            <div class="col-sm-6">
                <div><strong>
                        @if(isset($franchisee_details->region_frname) && !empty($franchisee_details->region_frname) )
                        {{ $franchisee_details->region_frname }}
                        @elseif(isset($franchisee_details->region_frname1) && !empty($franchisee_details->region_frname1) )
                        {{ $franchisee_details->region_frname1 }}
                        @elseif(isset($franchisee_details->region_frname2) && !empty($franchisee_details->region_frname2) )
                        {{ $franchisee_details->region_frname2 }}
                        @else
                        {{ '-' }}
                        @endif
                    </strong></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="textfield" class="col-sm-6">State Support Center:</label>
            <div class="col-sm-6">
                <div><strong>
                        @if(isset($franchisee_details->state_frname) && !empty($franchisee_details->state_frname) )
                        {{ $franchisee_details->state_frname }}
                        @elseif(isset($franchisee_details->state_frname1) && !empty($franchisee_details->state_frname1) )
                        {{ $franchisee_details->state_frname1 }}
                        @else
                        {{ '-' }}
                        @endif
                    </strong></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="textfield" class="col-sm-6">District Support Center:</label>
            <div class="col-sm-6">
                <div><strong>
                        @if(isset($franchisee_details->district_frname) && !empty($franchisee_details->district_frname) )
                        {{ $franchisee_details->district_frname }}
                        @else
                        {{ '-' }}
                        @endif
                    </strong></div>
            </div>
        </div>
    </div>
    <div style="clear:both"></div>
    <hr width="100%" />
    <div class="form-group">
        <label for="textfield" class="col-sm-3">Office Available:</label>
        <div class="col-sm-6">
            <input type="radio" name="office_available" class="simple" value="{{Config::get('constants.ON')}}" {{(isset($user_details->office_available) && $user_details->office_available == 1) ? "checked='checked'" : ""}} />Yes
            <input type="radio" name="office_available" class="simple" value="{{Config::get('constants.OFF')}}" {{(isset($user_details->office_available) && $user_details->office_available == 0) ? "checked='checked'" : ""}} />No
            <div id="office_available_err"></div>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">Support Center Name:</label>
        <div class="col-sm-6">
            <input type="text" name="user[company_name]" id="company_name" class="form-control"  placeholder="Enter Support Center Name" data-rule-required="true" value="<?php echo $user_details->company_name;?>" >
            <span><small>Company or Frim Name</small></span>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">Address:</label>
        <div class="col-sm-6">
            <textarea name="user[company_address]" id="company_address" class="form-control" data-rule-required="true"><?php echo $user_details->company_address;?></textarea>
            <span><small>Company or Frim Address</small></span>
        </div>
    </div>
    <div class="form-group">
        <label><b>Contact Person Details</b></label>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">Full Name:</label>
        <div class="col-sm-6">
            <input name="user[first_name]" class="form-control" type="text" id="first_name" value='<?php echo $user_details->firstname.' '.$user_details->lastname;?>' required/>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">DOB:</label>
        <div class="col-sm-6">
            <input name="user[dob]" class="form-control" placeholder="MM/DD/YYYY" type="text" id="dob" value="<?php echo date('m/d/Y ', strtotime($user_details->dob));?> "required/>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">Address:</label>
        <div class="col-sm-6">
            <textarea name="user[address]" class="form-control" id="address" required style="width: 100%; height: 84px;"><?php echo $user_details->address;?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">Country:</label>
        <div class="col-sm-6">
            <select name="user[country]" id="country" class="form-control" >
                <option value="">Choose Your Country</option>
                <?php
                foreach ($countrylist as $row)
                {
                    ?>
                    <option value="<?php echo $row->country_id;?>" {{($user_details->country == $row->country_id)?' selected ':''}}><?php echo $row->country_name;?></option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">State:</label>
        <div class="col-sm-6">
            <select name="user[state]" class="form-control" id="state">
                <option value="">---Select State---</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">District:</label>
        <div class="col-sm-6">
            <select name="user[district]" class="form-control" id="district">
                <option value="">---Select District---</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">City:</label>
        <div class="col-sm-6">
            <select name="user[city]" class="form-control" id="city" required>
                <option value="">--City--</option>
                @foreach($citys as $city)
                <option value="{{ $city->city_id}}" {{($user_details->city==$city->city_id)?'selected="selected"':''}}>{{ $city->city_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">		
        <label for="textfield" class="col-sm-3">Zip/Postal Code:</label>
        <div class="col-sm-6">
            <input name="user[zipcode]" class="form-control" type="text" id="zipcode" value='<?php echo $user_details->postal_code;?>' />
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">ISD Code:</label>
        <div class="col-lg-6">
            <select  name="user[phonecode]" class="form-control" id="phonecode" disabled >
                <option value="">--Select PhoneCode--</option>
                @if (!empty($countrylist))
                @foreach ($countrylist as $row)
                <option id="c_<?php echo $row->country_id;?>" value="<?php echo $row->phonecode;?>" <?php if ($row->phonecode == $user_details->phonecode) echo "selected='selected'";?>><?php echo $row->phonecode;?></option>
                @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">Mobile No:</label>
        <div class="col-sm-6">
            <input name="user[mobile]" class="form-control" type="text" id="mobile" value='<?php echo $user_details->office_phone;?>' required/>
            <input type="button" name="checkMobile" id="checkMobile" value="Check Mobile" />
            <div id="mobile_avail_status"></div>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">Contact Email:</label>
        <div class="col-sm-6">
            <input name="email" class="form-control" type="text" id="email" value="{{(isset($user_details->email)) ? $user_details->email : ''}}" required/>
            <input type="button" name="checkEmail" id="checkEmail" value="Check Email" />
            <div id="email_avail_status"></div>
        </div>
    </div>
    <div class="form-group">
        <label for="textfield" class="col-sm-3">&nbsp;</label>
        <div class="col-sm-6">
            <input type="submit" name="submit" id="submit" class="btn btn-primary" value="Save">
        </div>
    </div>
</form>
<!--script src="{{URL::asset('js/providers/admin/franchisee/franchisee_update_profile.js')}}"></script>
<script type="text/javascript">
$('.alert').remove();
$("#country").change();
$("#state").change();
$("#district").change();
$('#edit_prf').validate({
    errorElement: 'div',
    errorClass: 'help-block',
    focusInvalid: false,
    rules: {
        "user[mobile]": {
            required: true,
            digits: true,
            minlength: 3,
            maxlength: 13,
        },
        "user[company_name]": {
            required: true,
            minlength: 6,
            maxlength: 50,
        },
        "user[company_address]": "required",
        "user[first_name]": {
            required: true,
            minlength: 3,
            maxlength: 50
        },
		
        "user[dob]": "required",
        "user[country]": "required",
        "user[state]": "required",
        "user[district]": "required",
        "user[city]": "required",
        "user[zipcode]": {
            required: true,
            maxlength: 8,
            minlength: 6
        },
        office_available: "required",
    },
    errorPlacement: function (error, element) {
        var name = $(element).attr("name");
        if (name == "isdeposited" || name == "office_available") {
            error.appendTo($("#" + name + "_err"));
        }
        else {
            error.insertAfter(element);
        }
    },
    messages: {
        "user[mobile]": {
            digits: "Invalid phone number ! Only numbers allowed",
            required: "Please provide a valid Phone Number .",
            maxlength: "Please provide a valid Phone Number",
            minlength: "Please provide a valid Phone Number"
        },
        "user[company_name]": {
            required: "Please enter company or firm name",
            minlength: "Company or Firm name must be greater than 6 characters",
            maxlength: "Company or Firm name must be less than 50 characters"
        },
        "user[company_address]": {
            required: "Please enter company or frim address",
        },
        "user[first_name]": {
            required: "Please Enter First Name .",
            minlength: "First name must be greater than 3 characters",
            maxlength: "First name must be less than 50 characters"
        },
        "user[dob]": "Please select Date of birth",
        "user[country]": "Please select country",
        "user[state]": "Please select state",
        "user[district]": "Please select district",
        "user[city]": "Please select city",
        "user[zipcode]": {
            required: "Please enter zip code",
            minlength: "Please Provide Valide zipcode",
            maxlength: "Please Provide Valide zipcode"
        },
        office_available: "Please choose office available or not"
    },
    highlight: function (e) {
        $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
    },
    success: function (e) {
        $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
        $(e).remove();
    },
    submitHandler: function (form) {
        if ($(form).valid()) {
            $.ajax({
                type: 'POST',
                url: $('#edit_prf').attr('action'),
                data: $('#edit_prf').serialize(),
                dataType: 'json',
                beforeSend: function () {
                    $('input[type=submit]', $(form)).val('Processing...');
                    $('input[type=submit]', $(form)).attr('disabled', true);
                },
                success: function (op) {
                    if (op.status == 'success') {
                        $('#msg').html('<div class="alert alert-success" style="width:100%"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + op.msg + '<br></div>');
                        $("#view_user_profile .modal-body").html('<div class="alert alert-success" style="width:100%"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + op.msg + '<br></div>');
                    } else {
                        $('#msg').html('<div class="alert alert-danger" style="width:100%"><button data-dismiss="alert" class="close" type="button"><i class="ace-icon fa fa-times"></i></button>' + op.msg + '<br></div>');
                    }
                    $('input[type=submit]', $(form)).val('Save');
                    $('input[type=submit]', $(form)).removeAttr('disabled');
                }
            });
        }
        return false;
    },
    invalidHandler: function (form) {
    }
});
$('#edit_prf').on('click', '#checkMobile', function () {
    $('#edit_prf #submit').attr('disabled', 'disabled');
    var mobile = $("#mobile").val();
    $("#mobile_avail_status").text('');
    if (mobile) {
        $.ajax({
            url: "<?php echo URL::to('admin/franchisee_check_mobile');?>",
            type: "POST",
            data: {mobile: $("#mobile").val(), old_mobile: $("#old_mobile").val()},
            datatype: "application/json",
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'ok') {
                    $("#mobile_avail_status").text(data.msg).addClass('text-success');
                    $("#mobile_avail_status").removeClass("help-block");
                    $('#edit_prf #submit').removeAttr('disabled');
                } else if (data.status == 'error') {
                    $("#mobile_avail_status").text(data.msg).addClass('text-danger');
                    $("#mobile_avail_status").addClass("help-block");
                    $('#edit_prf #submit').attr('disabled', 'disabled');
                }
            },
            error: function () {
                alert('Something went wrong');
            }
        });
		}
});
$('#edit_prf').on('click', '#checkEmail', function () {
    $('#edit_prf #submit').attr('disabled', 'disabled');
    var email = $("#edit_prf #email").val();
    if (email != '' && isValidEmailAddress(email)) {
        $.ajax({
            url: "<?php echo URL::to('admin/franchisee_check_email');?>",
            type: "POST",
            data: {email: email, old_email: $("#old_email").val()},
            datatype: "application/json",
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'ok') {
                    $("#email_avail_status").text(data.msg).addClass('text-success');
                    $("#email_avail_status").removeClass("help-block");
                    $('#edit_prf #submit').removeAttr('disabled');
                } else if (data.status == 'error') {
                    $("#email_avail_status").text(data.msg).addClass('text-danger');
                    $("#email_avail_status").addClass("help-block");
                    $('#edit_prf #submit').attr('disabled', 'disabled');
                }
            },
            error: function () {
                alert('Something went wrong');
            }
        });
    }
});
function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
    // alert( pattern.test(emailAddress) );
    return pattern.test(emailAddress);
}
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#dob").datepicker({
            //defaultDate: "+1w",
            showDropdowns: 'true',
            dateFormat: 'mm-dd-yyyy',
            startDate: '<?php echo date('m/d/Y', strtotime('-100 year'))?>',
            endDate: '<?php echo date('m/d/Y', strtotime('-18 year'))?>',
        });
    });
</script>
<style type="text/css">
    .help-block{
        color:#f56954;
    }
</style-->
