define([
    'jquery'
], function($){
    'use strict';

    return {
        startLoader: function () {
            $(window.productConfigure.blockForm).trigger('processStart');
            return this;
        },

        stopLoader: function () {
            $(window.productConfigure.blockForm).trigger('processStop');
            return this;
        },

        save: function () {
            let params = window.order.serializeData('edit_form');
            return window.order.loadArea(false, false, params);
        },
    };
});