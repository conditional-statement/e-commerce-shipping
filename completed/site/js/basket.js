var basketObject = {
    updateBasketEnter : function(thisIdentity) {
    	"use strict";
        $(document).on('keypress', thisIdentity, function(e) {
            var code = e.keyCode ? e.keyCode : e.which;
            if (code == 13) {
            	e.preventDefault();
            	e.stopPropagation();
                basketObject.updateBasket();
            }
        });
    },
    refreshBigBasket : function() {
    	"use strict";
        $.post('/basket/action/view', function(data) {
            $('#big_basket').html(data);
        }, 'html');
    },
    removeFromBasket : function(thisIdentity) {
    	"use strict";
        $(document).on('click', thisIdentity, function(e) {
        	e.preventDefault();
            var item = $(this).attr('rel');
            $.post('/mod/basket_remove.php', { id : item }, function(data) {
                basketObject.refreshBigBasket();
                basketObject.refreshSmallBasket();
            }, 'html');
        });
    },
    refreshSmallBasket : function() {
    	"use strict";
        $.post('/mod/basket_small_refresh.php', function(data) {
            $.each(data, function(k, v) {
                $("#basket_left ." + k + " span").text(v);
            });
        }, 'json');
    },
    add2Basket : function(thisIdentity) {
    	"use strict";
        $(document).on('click', thisIdentity, function(e) {
        	e.preventDefault();
            var trigger = $(this);
            var param = trigger.attr("rel");
            var item = param.split("_");
            $.post('/mod/basket.php', { id : item[0], job : item[1] }, function(data) {
                var new_id = item[0] + '_' + data.job;
                if (data.job != item[1]) {
                    if (data.job === 0) {
                        trigger.attr("rel", new_id);
                        trigger.text("Remove from basket");
                        trigger.addClass("red");
                    } else {
                        trigger.attr("rel", new_id);
                        trigger.text("Add to basket");
                        trigger.removeClass("red");
                    }
                    basketObject.refreshSmallBasket();
                }
            }, 'json');
        });
    },
    updateBasketClick : function(thisIdentity) {
    	"use strict";
        $(document).on('click', thisIdentity, function(e) {
        	e.preventDefault();
            basketObject.updateBasket();
        });
    },
    updateBasket : function() {
    	"use strict";
        jQuery.each($('#frm_basket :input'), function() {
            var sid = $(this).attr('id').split('-');
            var val = $(this).val();
            $.post('mod/basket_qty.php', { id: sid[1], qty: val }, function(data) {
                basketObject.refreshSmallBasket();
                basketObject.refreshBigBasket();
            }, 'html');
        });
    },
    loadingPayPal : function(thisIdentity) {
    	"use strict";
        $(document).on('click', thisIdentity, function(e) {
        	e.preventDefault();
        	e.stopPropagation();
        	var thisShippingOption = $('input[name="shipping"]:checked');
        	if (thisShippingOption.length > 0) {
	            var token = $(this).attr('id');
	            var image = "<div style=\"text-align:center\">";
	            image = image + "<p><img src=\"/images/loadinfo.net.gif\"";
	            image = image + " alt=\"Proceeding to PayPal\" />";
	            image = image + "<br />Please wait while we are redirecting you to PayPal...</p>";
	            image = image + "</div><div id=\"frm_pp\"></div>";
	            $('#big_basket').fadeOut(200, function() {
	                $(this).html(image).fadeIn(200, function() {
	                    basketObject.send2PayPal(token);
	                });
	            });
	         } else {
		         systemObject.topValidation('Please select the shipping option');
	         }
        });
    },
    send2PayPal : function(token) {
    	"use strict";
        $.post('/mod/paypal.php', { token : token }, function(data) {
        	if (data && !data.error) {
	            $('#frm_pp').html(data.form);
	            // submit form automatically
	            $('#frm_paypal').submit();
	        } else {
		        systemObject.topValidation(data.message);
		        var thisTimeout = setTimeout(function() {
		        	window.location.reload();
		        }, 5000);
	        }
        }, 'json');
    },
    emailInactive : function(thisIdentity) {
    	"use strict";
    	$(document).on('click', thisIdentity, function(e) {
    		e.preventDefault();
    		var thisId = $(this).attr('data-id');
    		jQuery.getJSON('/mod/resend.php?id=' + thisId, function(data) {
    			if (!data.error) {
    				location.href = '/resent.html';
    			} else {
    				location.href = '/resent-failed.html';
    			}
    		});
    	});
    },
    shipping : function(thisIdentity) {
    	"use strict";
	    $(document).on('change', thisIdentity, function(e) {
		   	var thisOption = $(this).val();
		   	$.getJSON('/mod/summary_update.php?shipping=' + thisOption, function(data) {
			   if (data && !data.error) {
				   $('#basketSubTotal').html(data.totals.basketSubTotal);
				   $('#basketVat').html(data.totals.basketVat);
				   $('#basketTotal').html(data.totals.basketTotal);
			   }	
		   	});
	    });
    }
};
var systemObject = {
	showHideRadio : function(thisIdentity) {
		"use strict";
		$(document).on('click', thisIdentity, function(e) {
			var thisTarget = $(this).attr('name');
			var thisValue = $(this).val();
			if (thisValue == 1) {
				$('.' + thisTarget).hide();
			} else {
				$('.' + thisTarget).show();
			}
		});
	},
	topValidationTemp : function(thisMessage) {
		"use strict";
		var thisTemp = '<div id="topMessage">';
		thisTemp += thisMessage;
		thisTemp += '</div>';
		return thisTemp;
	},
	topValidation : function(thisMessage) {
		"use strict";
		if (thisMessage !== '' && typeof thisMessage !== 'undefined') {
			if ($('#topMessage').length > 0) {
				$('#topMessage').remove();
			}
			$('body').prepend($(systemObject.topValidationTemp(thisMessage)).fadeIn(200));
			var thisTimeout = setTimeout(function() {
				$('#topMessage').fadeOut(200, function() {
					$(this).remove();
				});
			}, 5000);
		}
	}
};
$(function() {
	"use strict";
	systemObject.showHideRadio('.showHideRadio');
    basketObject.removeFromBasket('.remove_basket');
    basketObject.updateBasketClick('.update_basket');
    basketObject.updateBasketEnter('.fld_qty');
	basketObject.add2Basket(".add_to_basket");
	basketObject.loadingPayPal('.paypal');
	basketObject.emailInactive('#emailInactive');
	basketObject.shipping('.shippingRadio');
});










