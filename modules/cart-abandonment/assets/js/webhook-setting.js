(function ($) {
    var webhookSetting = {

        init: function () {

            function onChangeAPIKey() {
                if ($("#setting_api_key").val() == "") {
                    $("#h24_save_settings").css("display", "none");
                } else {
                    $("#h24_save_settings").css("display", "inline-block");
                }

                if ($("#setting_h24_domain").val() == "") {
                    $("#h24_goto_settings").css("display", "none");
                    $("#h24_enable_integration_note").css("display", "inline-block");
                } else {
                    $("#h24_goto_settings").css("display", "inline-block");
                    $("#h24_enable_integration_note").css("display", "none");
                }

                if ($("#setting_api_key").val() == "" && $("#setting_h24_domain").val() == "") {
                    $("#h24_save_settings").css("display", "none");
                }

                if ($("#setting_api_key").val() == "" && $("#setting_h24_domain").val() != "") {
                    $("#h24_save_settings").css("display", "inline-block");
                }
            }

            onChangeAPIKey();

            $("#wp_h24_setting_form").submit(function () {
                return false;
            })

            $("#wp_h24_whatsapp_button_form").submit(function () {
                return false;
            })

            $("#setting_api_key").on("change", function () {
                onChangeAPIKey();
            })

            $("#setting_api_key").on("keydown", function () {
                $("#api_key_invalid").css("display", "none");
                onChangeAPIKey();
            })

            $("body").on("click", "#h24_save_settings", function () {
                if ($("#setting_shop_name").val() == "" || $("#setting_email").val() == "" || $("#setting_whatsapp_number").val() == "") {
                    return;
                }
                var data = {
                    action: "h24_activate_integration_service",
                    security: WPVars._nonce,
                    api_key: $("#setting_api_key").val(),
                    shop_name: $("#setting_shop_name").val(),
                    email: $("#setting_email").val(),
                    whatsapp_number: $("#setting_whatsapp_number").val(),
                    environment: $("input[name='setting_environment']:checked").val(),
                };
                jQuery("#h24_loding").css("display", "flex");
                jQuery.post(
                    WPVars.ajaxurl, data, //Ajaxurl coming from localized script and contains the link to wp-admin/admin-ajax.php file that handles AJAX requests on Wordpress
                    function (response) {
                        jQuery("#h24_loding").css("display", "none");
                        if (response.data && response.data.result) {
                            location.href = "";
                        } else {
                            $("#api_key_invalid").css("display", "inline-block");
                        }
                    }
                );
            });

            $("body").on("click", "#h24_save_whatsapp_button", function () {

                var whatsapp_button_enabled = "disabled";
                if ($("#whatsapp_button_enabled").is(':checked')) {
                    whatsapp_button_enabled = "enabled";
                }

                var data = {
                    action: "h24_save_whatsapp_button",
                    whatsapp_button_enabled: whatsapp_button_enabled,
                    whatsapp_button_title: $("#whatsapp_button_title").val(),
                    whatsapp_button_sub_title: $("#whatsapp_button_sub_title").val(),
                    whatsapp_button_greeting_text1: $("#whatsapp_button_greeting_text1").val(),
                    whatsapp_button_greeting_text2: $("#whatsapp_button_greeting_text2").val(),
                    whatsapp_button_agent_name: $("#whatsapp_button_agent_name").val(),
                    whatsapp_button_message: $("#whatsapp_button_message").val(),
                };

                jQuery("#h24_loding").css("display", "flex");
                jQuery.post(
                    WPVars.ajaxurl, data, //Ajaxurl coming from localized script and contains the link to wp-admin/admin-ajax.php file that handles AJAX requests on Wordpress
                    function (response) {
                        jQuery("#h24_loding").css("display", "none");
                        if (response.data && response.data.result) {
                            location.href = "";
                        } else {

                        }
                    }
                );
            });
        },
    }

    webhookSetting.init();

})(jQuery);