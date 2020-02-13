define([
    'jquery',
    'BeeBots_AdminCustomerQuote/js/admin-order-common'
], function ($, adminOrderCommon) {
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
            adminOrderCommon.startLoader();
            let promise = adminOrderCommon.save();
            let afterSendQuoteEmailPromise = promise.pipe(this.sendQuoteEmail.bind(this, this.url));
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
            // TODO: show success message?
            console.log('finished sending email');
            adminOrderCommon.stopLoader();
        },

        onEmailFail: function () {
            // TODO: show error message?
            console.log('error sending email');
            adminOrderCommon.stopLoader();
        },

        onBackButtonChanged: function () {
            this.emailQuoteButton.show();
        }
    };
});