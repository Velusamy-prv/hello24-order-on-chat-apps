(function ($) {

    var timer;
    var isProcessing = false;
    var h24_cart_abandonment = {
        init: function () {

            if (H24Variables._show_gdpr_message && !$("#h24_cf_gdpr_message_block").length) {
                $("#billing_email").after("<span id='h24_cf_gdpr_message_block'> <span style='font-size: xx-small'> " + H24Variables._gdpr_message + " <a style='cursor: pointer' id='h24_ca_gdpr_no_thanks'> " + H24Variables._gdpr_nothanks_msg + " </a></span></span>");
            }
            $(document).on(
                'keyup keypress change',
                '#billing_email, #billing_phone, input.input-text, textarea.input-text, select',
                this._getCheckoutData
            );

            $("#h24_ca_gdpr_no_thanks").click(function () {
                h24_cart_abandonment._set_cookie();
            });

            $(document.body).on('updated_checkout', function () {
                h24_cart_abandonment._getCheckoutData();
            });

            $(document).on('ready', function (e) {
                setTimeout(function () {
                    h24_cart_abandonment._getCheckoutData();
                }, 800);
            });
        },

        _set_cookie: function () {


            var data = {
                'h24_ca_skip_track_data': true,
                'action': 'cartflows_skip_cart_tracking_gdpr',
                'security': H24Variables._gdpr_nonce,
            };

            jQuery.post(
                H24Variables.ajaxurl, data,
                function (response) {

                    if (response.success) {
                        $("#h24_cf_gdpr_message_block").empty().append("<span style='font-size: xx-small'>" + H24Variables._gdpr_after_no_thanks_msg + "</span>").delay(5000).fadeOut();
                    }

                }
            );

        },

        _validate_email: function (value) {
            var valid = true;
            if (value.indexOf('@') == -1) {
                valid = false;
            } else {
                var parts = value.split('@');
                var domain = parts[1];
                if (domain.indexOf('.') == -1) {
                    valid = false;
                } else {
                    var domainParts = domain.split('.');
                    var ext = domainParts[1];
                    if (ext.length > 14 || ext.length < 2) {
                        valid = false;
                    }
                }
            }
            return valid;
        },

        _getCheckoutData: function () {

            if (isProcessing == true)
                return;


            var h24_phone = jQuery("#billing_phone").val();
            var h24_email = jQuery("#billing_email").val();

            if (typeof h24_email === 'undefined') {
                return;
            }

            var atposition = h24_email.indexOf("@");
            var dotposition = h24_email.lastIndexOf(".");


            if (typeof h24_phone === 'undefined' || h24_phone === null) { //If phone number field does not exist on the Checkout form
                h24_phone = '';
            }

            clearTimeout(timer);

            if (!(atposition < 1 || dotposition < atposition + 2 || dotposition + 2 >= h24_email.length) || h24_phone.length >= 1) { //Checking if the email field is valid or phone number is longer than 1 digit
                //If Email or Phone valid
                var h24_name = jQuery("#billing_first_name").val();
                var h24_surname = jQuery("#billing_last_name").val();
                var h24_phone = jQuery("#billing_phone").val();
                var h24_country = jQuery("#billing_country").val();
                var h24_city = jQuery("#billing_city").val();

                //Other fields used for "Remember user input" function
                var h24_billing_company = jQuery("#billing_company").val();
                var h24_billing_address_1 = jQuery("#billing_address_1").val();
                var h24_billing_address_2 = jQuery("#billing_address_2").val();
                var h24_billing_state = jQuery("#billing_state").val();
                var h24_billing_postcode = jQuery("#billing_postcode").val();
                var h24_shipping_first_name = jQuery("#shipping_first_name").val();
                var h24_shipping_last_name = jQuery("#shipping_last_name").val();
                var h24_shipping_company = jQuery("#shipping_company").val();
                var h24_shipping_country = jQuery("#shipping_country").val();
                var h24_shipping_address_1 = jQuery("#shipping_address_1").val();
                var h24_shipping_address_2 = jQuery("#shipping_address_2").val();
                var h24_shipping_city = jQuery("#shipping_city").val();
                var h24_shipping_state = jQuery("#shipping_state").val();
                var h24_shipping_postcode = jQuery("#shipping_postcode").val();
                var h24_order_comments = jQuery("#order_comments").val();

                var data = {
                    action: "save_cart_abandonment_data",
                    h24_email: h24_email,
                    h24_name: h24_name,
                    h24_surname: h24_surname,
                    h24_phone: h24_phone,
                    h24_country: h24_country,
                    h24_city: h24_city,
                    h24_billing_company: h24_billing_company,
                    h24_billing_address_1: h24_billing_address_1,
                    h24_billing_address_2: h24_billing_address_2,
                    h24_billing_state: h24_billing_state,
                    h24_billing_postcode: h24_billing_postcode,
                    h24_shipping_first_name: h24_shipping_first_name,
                    h24_shipping_last_name: h24_shipping_last_name,
                    h24_shipping_company: h24_shipping_company,
                    h24_shipping_country: h24_shipping_country,
                    h24_shipping_address_1: h24_shipping_address_1,
                    h24_shipping_address_2: h24_shipping_address_2,
                    h24_shipping_city: h24_shipping_city,
                    h24_shipping_state: h24_shipping_state,
                    h24_shipping_postcode: h24_shipping_postcode,
                    h24_order_comments: h24_order_comments,
                    security: H24Variables._nonce,
                    h24_post_id: H24Variables._post_id,
                }

                isProcessing = true;
                timer = setTimeout(
                    function () {
                        if (h24_cart_abandonment._validate_email(data.h24_email)) {
                            jQuery.post(
                                H24Variables.ajaxurl, data, //Ajaxurl coming from localized script and contains the link to wp-admin/admin-ajax.php file that handles AJAX requests on Wordpress
                                function (response) {
                                    isProcessing = false;
                                }
                            );
                        } else {
                            isProcessing = false;
                        }
                    }, 500
                );
            } else {
                //console.log("Not a valid e-mail or phone address");
            }
        }

    }

    h24_cart_abandonment.init();

})(jQuery);