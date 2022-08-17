/*======================================================

Project: Hello24 Chat Button - Free
Website: https://hello24.ai/
Author: Velusamy
Released On: 09, AUGUST 2022
@version: 1.5

========================================================*/
(function ($) {
    console.log('auto function');
    if (!window.hello24_phoneNumber) {
        console.log('No phone number provided');
        return;
    }

    if (!window.jQuery) {
        return;
    }

    $(document).ready(function () {
        var hello24_chat_button_color = window.hello24_chat_theme_color || "#FFFFFF";
        var hello24_chat_theme_color = window.hello24_chat_theme_color || "#000075";
        var hello24_chat_theme_color_gradient = window.hello24_chat_theme_color_gradient || "#2E2EFF";
        var hello24_chat_button_size = window.hello24_chat_button_size || "large";
        var hello24_chat_button_position = window.hello24_chat_button_position || "right";
        var hello24_chat_mobile_link = window.hello24_chat_mobile_link;
        var hello24_chat_web_link = window.hello24_chat_web_link;

        var hello24_chat_button_size_px = "30px";
        var hello24_chat_button_size_inside_px = "24px";

        if (hello24_chat_button_size == "large") {
            hello24_chat_button_size_px = "50px";
            hello24_chat_button_size_inside_px = "36px";
        } else if (hello24_chat_button_size == "medium") {
            hello24_chat_button_size_px = "40px";
            hello24_chat_button_size_inside_px = "32px";
        }

        var default_hello24_chat_button = `data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iMWVtIiBoZWlnaHQ9IjFlbSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ieE1pZFlNaWQgbWVldCIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHN0eWxlPSItbXMtdHJhbnNmb3JtOiByb3RhdGUoMzYwZGVnKTsgLXdlYmtpdC10cmFuc2Zvcm06IHJvdGF0ZSgzNjBkZWcpOyB0cmFuc2Zvcm06IHJvdGF0ZSgzNjBkZWcpOyI+PHBhdGggZmlsbD0ibm9uZSIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIgc3Ryb2tlLXdpZHRoPSIzMiIgZD0iTTQwOCA2NEgxMDRhNTYuMTYgNTYuMTYgMCAwIDAtNTYgNTZ2MTkyYTU2LjE2IDU2LjE2IDAgMCAwIDU2IDU2aDQwdjgwbDkzLjcyLTc4LjE0YTggOCAwIDAgMSA1LjEzLTEuODZINDA4YTU2LjE2IDU2LjE2IDAgMCAwIDU2LTU2VjEyMGE1Ni4xNiA1Ni4xNiAwIDAgMC01Ni01NloiLz48Y2lyY2xlIGN4PSIxNjAiIGN5PSIyMTYiIHI9IjMyIiBmaWxsPSJ3aGl0ZSIvPjxjaXJjbGUgY3g9IjI1NiIgY3k9IjIxNiIgcj0iMzIiIGZpbGw9IndoaXRlIi8+PGNpcmNsZSBjeD0iMzUyIiBjeT0iMjE2IiByPSIzMiIgZmlsbD0id2hpdGUiLz48L3N2Zz4=`;
        var hello24_companyName = window.hello24_companyName || "Hello24";
        var hello24_title = window.hello24_title || "Need help?";
        var hello24_subTitle = window.hello24_subTitle || "Typically replies in minutes";
        var hello24_greetingText1 = window.hello24_greetingText1 || "Hello there 👋";
        var hello24_greetingText2 = window.hello24_greetingText2 || "How can I help you?";
        var hello24_agentName = window.hello24_agentName || "Customer Support";
        var hello24_phoneNumber = window.hello24_phoneNumber || "";
        var hello24_message = window.hello24_message || "Hi";
        var hello24_chat_button = window.hello24_chat_button || default_hello24_chat_button;

        var styles = `
            .h24-style1 .wc-content .wc-bubble span {
                color: rgba(0, 0, 0, 0.4);
            }
            
            /* Common CSS */
            .h24-style1 .wc-header,
            .h24-style1 .wc-button,
            .h24-style1 .wc-footer .wc-list {
                background: -webkit-linear-gradient(to right, ${hello24_chat_theme_color}, ${hello24_chat_theme_color_gradient});
                background: -moz-linear-gradient(to right,  ${hello24_chat_theme_color}, ${hello24_chat_theme_color_gradient});
                background: -ms-linear-gradient(to right,  ${hello24_chat_theme_color}, ${hello24_chat_theme_color_gradient});
                background: -o-linear-gradient(to right,  ${hello24_chat_theme_color}, ${hello24_chat_theme_color_gradient});
                background: linear-gradient(to right,  ${hello24_chat_theme_color}, ${hello24_chat_theme_color_gradient});
            }
            
            .h24-style1 .wc-panel,
            .h24-style1 .wc-content .wc-bubble,
            .h24-style1 .wc-footer {
                background-color: #fff;
            }
            
            .h24-style1 .wc-panel .wc-header,
            .h24-style1 .wc-button i,
            .h24-style1 .wc-user-info,
            .h24-style1 .wc-list p,
            .h24-style1 .wc-list i {
                color: #fff;
            }
            
            .h24-style1 *,
            .h24-style1 ::after,
            .h24-style1 ::before {
                box-sizing: border-box
            }

            .h24-style1 p {
                margin-top: 0;
                margin-bottom: 1rem
            }
            
            .h24-style1 a {
                color: #007bff;
                text-decoration: none;
                background-color: transparent;
                -webkit-text-decoration-skip: objects
            }
            
            .h24-style1 a:hover {
                color: #0056b3;
                text-decoration: underline
            }
            
            .h24-style1 a:not([href]):not([tabindex]) {
                color: inherit;
                text-decoration: none
            }
            
            .h24-style1 a:not([href]):not([tabindex]):focus,
            .h24-style1 a:not([href]):not([tabindex]):hover {
                color: inherit;
                text-decoration: none
            }
            
            .h24-style1 a:not([href]):not([tabindex]):focus {
                outline: 0
            }
            
            .h24-style1 img,
            .h24-style1 svg {
                vertical-align: middle;
                border-style: none
            }
            
            /*======================================================
            H24 Style 7 code
            ========================================================*/
            
            /* WhatsChat Main Panel */
            .h24-style1 {
                font-family: 'Open Sans', "Helvetica Neue", Helvetica, Arial, sans-serif;
                z-index: 999;
            }
            
            /* WhatsChat Floating Button */
            .h24-style1 .wc-button {
                position: fixed;
                width: ${hello24_chat_button_size_px};
                height: ${hello24_chat_button_size_px};
                bottom: 40px;
                ${hello24_chat_button_position}: 20px;
                border: 1px solid #fff;
                border-radius: 50px;
                text-align: center;
                box-shadow: 0 0 10px rgba(12, 12, 12, 0.3);
                cursor: pointer;
                display: flex;
                z-index: 999;
            }
            
            .h24-style1 .wc-button #wc-chat svg, .h24-style1 .wc-button #wc-chat img{
                width: ${hello24_chat_button_size_inside_px};
                height: ${hello24_chat_button_size_inside_px};
            }

            .h24-style1 .wc-button:hover {
                box-shadow: 0px 0px 10px rgba(12, 12, 12, 0.5);
            }
            
            .h24-style1 .wc-button i {
                margin-top: 13px;
                font-size: 25px;
                z-index: 999;
            }
            
            /* The popup chat - hidden by default */
            .h24-style1 .wc-panel {
                display: none;
                position: fixed;
                width: 300px;
                bottom: 100px;
                ${hello24_chat_button_position}: 30px;
                border-radius: 10px;
                box-shadow: 0 3px 6px #b4b4b4;
                z-index: 9;
            }
            
            /* WhatsChat Header */
            .h24-style1 .wc-panel .wc-header {
                display: flex;
                padding: 15px 20px;
                text-align: center;
                border-radius: 7px 7px 0 0;
            }
            
            .h24-style1 .wc-header .wc-img-cont {
                position: relative;
                padding: 3px;
                margin-bottom: 4px;
                height: 52px;
                width: 52px;
                border: 2px solid #fff;
                border-radius: 50%;
                overflow: hidden;
                transition: transform 200ms ease-in-out;
            }
            
            .h24-style1 .wc-img-cont .wc-user-img {
                display: block;
                margin: 0 auto;
                max-width: 100%;
                height: 100%;
                border-radius: 50%;
            }
            
            .h24-style1 .wc-header .wc-user-info {
                margin-left: 15px;
                text-align: left;
            }
            
            .h24-style1 .wc-user-info strong {
                font-size: 15px;
                line-height: 20px;
            }
            
            .h24-style1 .wc-user-info p {
                margin-bottom: 0;
                font-size: 12px;
                line-height: 20px;
            }
            
            /* WhatsChat Body */
            .h24-style1 .wc-body {
                display: flex;
                flex-direction: column;
                position: relative;
                padding: 16px 20px 20px 10px;
                width: 300px;
                background-color: rgb(230, 221, 212);
            }
            
            .h24-style1 .wc-body::before {
                content: '';
                display: block;
                position: absolute;
                left: 0;
                top: 0;
                height: 100%;
                width: 100%;
                opacity: 0.08;
                z-index: 0;
                background: #eed9c4;
            }
            
            /* CSS Chat Bubble */
            .h24-style1 .wc-content .wc-bubble {
                display: inline-block;
                position: relative;
                padding: 7px 14px 6px;
                margin-top: 5px;
                margin-left: 15px;
                width: 200px;
                height: auto;
                border-radius: 0 8px 8px 8px;
                transition: 0.3s ease all;
            }
            
            .h24-style1 .wc-content .tri-right.border.left-top:before {
                content: ' ';
                position: absolute;
                width: 0;
                height: 0;
                left: -40px;
                right: auto;
                top: -8px;
                bottom: auto;
                border: 32px solid;
                border-color: #666 transparent transparent transparent;
            }
            
            .h24-style1 .wc-content .tri-right.left-top:after {
                content: ' ';
                position: absolute;
                width: 0;
                height: 0;
                left: -20px;
                right: auto;
                top: 0px;
                bottom: auto;
                border: 22px solid;
                border-color: #fff transparent transparent transparent;
            }
            
            .h24-style1 .wc-content .wc-bubble span {
                font-size: 13px;
                font-weight: 700;
                line-height: 18px;
            }
            
            .h24-style1 .wc-content .wc-bubble p {
                padding-top: 10px;
                padding-left: 10px;
                padding-bottom: 5px;
                margin-bottom: 0;
                font-size: 14px;
                line-height: 20px;
            }
            
            .h24-style1 .wc-header svg, .h24-style1 .wc-header img {
                width: 32px;
                height: 32px;
            }

            /* WhatsChat Footer */
            .h24-style1 .wc-footer {
                margin: 20px;
                border-radius: 18px;
            }
            
            .h24-style1 .wc-footer svg, .h24-style1 .wc-footer img {
                width: 32px;
                height: 32px;
            }

            .h24-style1 .wc-footer .wc-list {
                display: flex;
                position: relative;
                padding: 8px 0;
                margin: 20px 20px;
                justify-content: center;
                align-items: center;
                font-weight: 700;
                line-height: 20px;
                border: none;
                border-radius: 18px;
                cursor: pointer;
                overflow: hidden;
            }
            
            .h24-style1 .wc-list p {
                margin-bottom: 0;
                margin-left: 8px;
                margin-right: 8px;
                font-size: 15px;
            }
            
            .h24-style1 .wc-list i {
                font-size: 20px;
            }
                    
        `;
        var styleSheet = document.createElement("style");
        styleSheet.type = "text/css";
        styleSheet.innerText = styles;
        document.getElementsByTagName("head")[0].appendChild(styleSheet);

        var popup = `	
            <div class="h24-style1">
                <a class="wc-button">
                    <span id="wc-chat" style="display: inline-block; margin: auto">
                    <img src="${hello24_chat_button}" alt="CHAT"/>
                    </span>
                    <span id="wc-close" style="display: none; margin: auto">
                        <svg width="30px" height="30px" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink"
                            preserveAspectRatio="xMidYMid meet" viewBox="0 0 1024 1024"
                            style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);">
                            <path fill="#FFFFFF"
                                d="M195.2 195.2a64 64 0 0 1 90.496 0L512 421.504L738.304 195.2a64 64 0 0 1 90.496 90.496L602.496 512L828.8 738.304a64 64 0 0 1-90.496 90.496L512 602.496L285.696 828.8a64 64 0 0 1-90.496-90.496L421.504 512L195.2 285.696a64 64 0 0 1 0-90.496z" />
                        </svg>
                    </span>
                </a>
    
                <div class="wc-panel">
                    <div class="wc-header">
                        <img src="${hello24_chat_button}" alt="CHAT"/>
                        <div class="wc-user-info">
                            <strong>`+ hello24_title + `</strong>
                            <p>`+ hello24_subTitle + `</p>
                        </div>
                    </div>
                    <div class="wc-body">
                        <div class="wc-content">
                            <div class="wc-bubble tri-right left-top">
                                <span>`+ hello24_agentName + `</span>
                                <br>
                                <p>`+ hello24_greetingText1 + `</p>
                                <p>`+ hello24_greetingText2 + `</p>
                            </div>
                        </div>
                    </div>
                    <div class="wc-footer">
                        <a class="wc-list" number="`+ hello24_phoneNumber + `" message="` + hello24_message + `">
                            <img src="${hello24_chat_button}" alt="CHAT"/>
                            <p style="font-size: 14px;">START CHAT</p>
                        </a>
                        <p style="text-align: center;font-size: 14px;">Powered by <a href="https://hello24.ai" target="_blank">Hello24.ai</a></p>
    
                    </div>
                </div>
            </div>
        `;

        $(document.body).append(popup);

        //click event on a tag
        $('.wc-list').on("click", function () {

            var number = $(this).attr("number");
            var message = $(this).attr("message");

            //checking for device type
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                window.open(hello24_chat_mobile_link + number + '/?text=' + message, '-blank');
            }
            else {
                window.open(hello24_chat_web_link + '?phone=' + number + '&text=' + message, '-blank');
            }
        });

        console.log('hide/show function');

        $("#wc-close").hide();
        $("#wc-chat").click(function () {
            $("#wc-chat").hide();
            $("#wc-close").fadeIn();
            $(".wc-panel").show();
        });

        $("#wc-close").click(function () {
            $("#wc-close").hide();
            $("#wc-chat").fadeIn();
            $(".wc-panel").hide();
        });
    });

})(jQuery);
