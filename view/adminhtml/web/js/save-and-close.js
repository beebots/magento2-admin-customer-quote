define([
    'jquery',
    'BeeBots_AdminCustomerQuote/js/admin-order-common',
], function ($, adminOrderCommon) {
    'use strict';

    return {

        cancelUrl: false,
        saveCloseButton: false,

        init: function (cancelUrl) {
            if (cancelUrl) {
                this.cancelUrl = cancelUrl;
            }
            this.saveCloseButton = $('#save_and_close_quote');
            this.saveCloseButton.click(this.onSaveAndClose.bind(this));
            const $actionButtonsBar = $('#back_order_top_button').get(0);
            const config = {attributes: true, childList: true};
            const observer = new MutationObserver(this.onBackButtonChanged.bind(this));
            observer.observe($actionButtonsBar, config);
        },

        onSaveAndClose: function() {
            adminOrderCommon.startLoader();
            let deferred = adminOrderCommon.save();
            deferred.done(this.onSaveFinish.bind(this));
        },

        onSaveFinish: function () {
            this.close();
        },

        close: function () {
            if (this.cancelUrl) {
                window.location.href = this.cancelUrl;
            } else {
                $('#reset_order_top_button').click();
            }
        },

        onBackButtonChanged: function () {
            this.saveCloseButton.show();
        }
    };
});