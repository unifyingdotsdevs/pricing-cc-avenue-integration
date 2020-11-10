jQuery( document ).ready(function() {
  	jQuery.validator.addMethod("emailNumber", function(value, element) {
			return this.optional(element) || /^(.+)@(.+)$/i.test(value);
		}, "Email must be Valid");


  	jQuery('.proteen_form_handler').on('submit', function(e) {

});

	var form = jQuery(".proteen_form_handler");
	form.validate({
		errorElement: 'span',
		errorClass: 'help-block',
		highlight: function(element, errorClass, validClass) {
			jQuery(element).closest('.form-group').addClass("has-error");
		},
		unhighlight: function(element, errorClass, validClass) {
			jQuery(element).closest('.form-group').removeClass("has-error");
		},
		rules: {
			merchant_param3: {
				required: true,
			},
			merchant_param5 : {
				required: true,
				number: true,
			},
			merchant_param4: {
				required: true,
				emailNumber:true,
			},

		},
		messages: {
			merchant_param3: {
				required: "This is a required field",
			},

			merchant_param5: {
				required: "This is a required field",
			},
			merchant_param4: {
				required: "This is a required field",
			},

		},
		submitHandler: function(form, e) { // <- pass 'form' argument in
            
            var v = grecaptcha.getResponse();

            if(v.length == 0) {
				jQuery(".captcha_wrapper").after('<span id="captcha-error" class="help-block">This is a required field</span>');
				e.preventDefault();
			} else {
				jQuery(".btn-payment").attr("disabled", true);

				form.submit();
			}
        }
	});
});

jQuery('.proteen_pricing.web .btn-info').click(function(){

	jQuery(".superStyle").removeClass("superStyle");
	jQuery(".superStyle-block").removeClass("superStyle-block");


	var z = jQuery(this).parent();
	var table = jQuery(this).closest('table');
	table.find('.superStyle').removeClass('superStyle');
	table.find('.superStyle-block').removeClass('superStyle-block');

	var index = z.index() + 1;
	var p = 'tbody tr td:nth-child('+index+')';
    table.find('thead th').eq(z.index()).addClass('superStyle');
	table.find(p).addClass('superStyle-block');


	var selected_node =table.find('thead th').eq(z.index());
	setup_checkout_table(selected_node);

      jQuery('html, body').animate({
         scrollTop: jQuery(".addon_wrapper").offset().top-140
     }, 1500);
});


jQuery('body').on('click', '.proteen_pricing.mobile .plan-action-btn .btn', function() {
	jQuery(".superStyle").removeClass("superStyle");
	jQuery(".superStyle-block").removeClass("superStyle-block");


	var qz = jQuery(this).closest('.mobile');

	qz.find('.superStyle').removeClass('superStyle');
	qz.find('.superStyle-block').removeClass('superStyle-block');

	var qq = jQuery(this).closest('.pricing_element');

	qq.addClass('superStyle-block');

	var selected_node =qq;

	setup_checkout_table(selected_node);
	  jQuery('html, body').animate({
         scrollTop: jQuery(".addon_wrapper").offset().top-80
     }, 1500);
});


jQuery( document ).ready(function() {
	if (jQuery(".superStyle-block").length > 0) {
		var q = jQuery('.superStyle-block a');
		var z = q.parent();
		var table = q.closest('table');

		var index = z.index() + 1;
		var p = 'tbody tr td:nth-child('+index+')';
	    table.find('thead th').eq(z.index()).addClass('superStyle');
		table.find(p).addClass('superStyle-block');
	}
});

jQuery( document ).ready(function() {
	if (jQuery(".superStyle").length > 0) {
		var selected_node = jQuery(".superStyle:first");
		setup_checkout_table(selected_node);
	}
});

jQuery('.princing_addon_package .addon-selection').click(function() {

	//clear amount data
	jQuery("#discount-amount span").text("");
	jQuery("#coupon-status").text("");
	jQuery("#coupon_id").val('');
	jQuery("#ccode").val('');
	jQuery("#coupon-status").removeClass("success");
	jQuery("#discount-amount").hide();


	if(!jQuery(this).hasClass('added-pack')) {
		jQuery(this).text('Remove');
		jQuery(this).closest('.princing_addon_package').addClass('selected-addon');
		jQuery(this).addClass('added-pack');
		var addon_addon = jQuery(this).parent();
		var price_node = addon_addon.find('.add_on_price_node');
		var plan_price = price_node.data("plan_pricing");
		var plan_name = price_node.data("plan_name");
		var plan_id = price_node.data("plan_id");
		append_row(plan_name, plan_price, plan_id);	
	} else {
		jQuery(this).text('Add');
		jQuery(this).closest('.princing_addon_package').removeClass('selected-addon');
		jQuery(this).removeClass('added-pack');
		var addon_addon = jQuery(this).parent();
		var price_node = addon_addon.find('.add_on_price_node');
		var plan_id = price_node.data("plan_id");
		jQuery("#addon_"+plan_id).remove();
	}
	update_cart_total();
});

jQuery('body').on('click', '.remove_addon', function() {

	//clear amount data
	jQuery("#discount-amount span").text("");
	jQuery("#coupon-status").text("");
	jQuery("#coupon_id").val('');
	jQuery("#ccode").val('');
	jQuery("#coupon-status").removeClass("success");
	jQuery("#discount-amount").hide();


	var addon_id = jQuery(this).data("addon_id");
	var po = jQuery("#addon_plan_"+addon_id);
	var btn = po.find('.addon-selection');
	btn.text('Add');
	po.removeClass('selected-addon');
	btn.removeClass('added-pack');
	jQuery("#addon_"+addon_id).remove();
	update_cart_total();
});


function setup_checkout_table(selected_node) {
	//clear amount data
	jQuery("#discount-amount span").text("");
	jQuery("#coupon-status").text("");
	jQuery("#coupon_id").val('');
	jQuery("#ccode").val('');
	jQuery("#coupon-status").removeClass("success");
	jQuery("#discount-amount").hide();

	var p_node = selected_node.find('.price_node');
	var plan_price = p_node.data("plan_pricing");
	var plan_name = p_node.data("plan_name");
	var plan_type = p_node.data("plan_type");
	var plan_id = p_node.data("plan_id");

	if(plan_type == "Year") {
		jQuery('.book').show();
	} else {
		jQuery('.book').hide();
	}

	if(plan_type == "Month") {
		plan_type = "Monthly";
	} else {
		plan_type = "Yearly";
	}

	jQuery('.checkout-table .primary_plan .package .plan-name').text(plan_name);
	jQuery('.checkout-table .primary_plan .package .plan-type').text(plan_type);
	jQuery('.checkout-table .primary_plan .primary-price').text(get_formatted_number(plan_price));
	jQuery('.checkout-table .primary_plan').attr('data-plan_id', plan_id);
	update_cart_total();
}


function append_row(plan_name, plan_price, plan_id) {
	var row = "<tr id='addon_"+plan_id+"' class='addon-item plan' data-plan_id='"+plan_id+"'><td class='package'><span class='plan-name'>"+plan_name+"</span><span class='remove_addon' data-addon_id="+plan_id+">X</span></td><td>INR <span class='primary-price'>"+get_formatted_number(plan_price)+"</span></td></tr>";
	jQuery('.checkout-table tbody tr.add-on-titles').after(row);
}


function update_cart_total() {
	var total = 0;
	jQuery('.primary-price').each(function(i, obj) {
		if(jQuery(this).text() != '') {
			var pt = jQuery(this).text().replace(/,/g, '');
			// total = total + parseInt(jQuery(this).text());
			total = total + parseInt(pt);
		}
	});
	var discount = parseInt(jQuery("#discount-amount span").text());


	if(!isNaN(discount)) {
		total = total - discount;		
	}

	jQuery('#checkout-price').text(get_formatted_number(total));

	var plans_id = '';
	jQuery(".checkout-table .plan").each(function() {
	    // console.log(this);
	    // console.log("ss"+jQuery(this).attr("data-plan_id"));
	    plans_id = plans_id + jQuery(this).attr("data-plan_id") +',';
	    // console.log(plans_id);
	});
	jQuery('#selected_plan').val(plans_id);

}














jQuery( document ).ready(function() {

	jQuery('body').on('click', '#coupon-btn', function() {
		var code = jQuery('#ccode').val();
		var selected_plan = jQuery('#selected_plan').val();

		if(code != '' && selected_plan != '') {
			jQuery.ajax({
				type: 'POST',
				url: proteen_ajax_scripts.ajaxurl,
				dataType: 'json',
					data: {
						'action'	:	'proteen_validate_coupon',
						'code'		:   code,
						'plan_id'	: 	selected_plan
					}
				}).done(function( response ) {

					if(response.success) {
						console.log(response);
						console.log('valid');
						jQuery("#discount-amount").show();
						jQuery("#discount-amount span").text(response.discount);
						jQuery("#coupon-status").text("Applied!");
						jQuery("#coupon_id").val(response.coupon_id);
						jQuery("#coupon-status").addClass("success");
						update_cart_total();
					} else {
						jQuery("#discount-amount span").text("");
						jQuery("#coupon-status").text("Invalid");
						jQuery("#coupon_id").val('');
						jQuery("#ccode").val('');
						jQuery("#coupon-status").removeClass("success");
						console.log('failed');
						jQuery("#discount-amount").hide();
						update_cart_total();
					}

			  }).fail(function( response ) {
			    console.log('Failed AJAX Call :( /// Return response: ' + response);
			  });
		}

	});	
});


jQuery( document ).ready(function() {

	jQuery('body').on('click', '.wow-modal-id-11', function() {
		//fill up form data ASAP
		var plan_ids = jQuery('#selected_plan').val();
		jQuery('#wow-modal-window-11 #selected_plan').val(plan_ids);

		var coupon_id = jQuery('#coupon_id').val();
		jQuery('#wow-modal-window-11 #coupon_id').val(coupon_id);

		var price = jQuery('#checkout-price').text();
		price1 = price.replace(/,/g, '');
		jQuery('#proteen_amount').val(price1);

		jQuery('#wow-modal-window-11 #checkout-price').text(get_formatted_number(price));		
	});

	

});


function get_formatted_number(number) {
	var p = number.toLocaleString(undefined, {maximumFractionDigits:2}) // "1,234.57"
	return p;
}