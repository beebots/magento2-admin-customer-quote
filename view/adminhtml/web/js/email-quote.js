define([
    'jquery',
    'BeeBots_AdminCustomerQuote/js/admin-order-common',
    'Magento_Ui/js/modal/alert',
], function ($, adminOrderCommon, alert) {
    'use strict';

    return {

        url: false,
        emailQuoteButton: false,

        init: function (url) {
            this.url = url;
            this.emailQuoteButton = $('#email_quote');
            this.emailQuoteButton.click(this.onSendEmailClicked.bind(this))
            const $actionButtonsBar = $('#back_order_top_button').get(0);
            const config = {attributes: true, childList: true};
            const observer = new MutationObserver(this.onBackButtonChanged.bind(this));
            observer.observe($actionButtonsBar, config);
        },

        onSendEmailClicked: function () {
            $('#container .js-success').remove();
            adminOrderCommon.startLoader();
            let promise = adminOrderCommon.save();
            let afterSendQuoteEmailPromise = promise.then(this.sendQuoteEmail.bind(this, this.url));
            afterSendQuoteEmailPromise.done(this.onEmailSent);
            afterSendQuoteEmailPromise.fail(this.onEmailFail);
        },

        sendQuoteEmail: function (url) {
            if (!url) {
                console.error('url is required');
                return;
            }
            adminOrderCommon.startLoader(); // something is turning off the loader in the middle, so start it again
            return $.ajax({
                url: url,
                method: 'POST',
                data: {'quote_id': window.order.quoteId},
                error: null, // override global value set in lib/web/mage/backend
                complete: null // override global value set in lib/web/mage/backend
            });
        },

        onEmailSent: function () {
            $('#container').prepend('<div class="messages"><div class="message message-success success js-success">Email sent!</div></div>');
            adminOrderCommon.stopLoader();
        },

        onEmailFail: function () {
            alert({content:'Error sending email, try again. If it fails again, contact webteam.'});
            adminOrderCommon.stopLoader();
        },

        onBackButtonChanged: function () {
            this.emailQuoteButton.show();
        }
    };
});
