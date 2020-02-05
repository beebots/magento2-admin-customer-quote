define([
    'jquery',
    'BeeBots_AdminCustomerQuote/js/admin-order-common',
    'Magento_Sales/order/create/scripts'
], function ($, adminOrderCommon) {
    'use strict';

    return {

        cancelUrl: false,

        init: function (cancelUrl) {
            if (cancelUrl) {
                this.cancelUrl = cancelUrl;
            }
            $('#save_and_close_quote').click(this.onSaveAndClose.bind(this));
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
            // if (this.cancelUrl) {
            //     window.location.href = this.cancelUrl;
            // } else {
            //     $('#reset_order_top_button').click();
            // }
        },
    };
});