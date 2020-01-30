define([
    'jquery',
    'Magento_Sales/order/create/scripts',
], function ($) {
    'use strict';

    return {

        cancelUrl: false,

        init: function (cancelUrl) {
            if (cancelUrl) {
                this.cancelUrl = cancelUrl;
            }
            $('#save_and_close_quote').click(this.save.bind(this));
        },

        save: function () {
            this.startLoader();
            let params = window.order.serializeData('edit_form');
            let deferred = window.order.loadArea(false, false, params);
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

        startLoader: function () {
            $(window.productConfigure.blockForm).trigger('processStart');
            return this;
        },

        stopLoader: function () {
            $(window.productConfigure.blockForm).trigger('processStop');
            return this;
        },
    };
});