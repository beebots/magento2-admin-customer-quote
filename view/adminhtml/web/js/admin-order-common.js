define([
    'jquery',
    'Magento_Sales/order/create/scripts',
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
            params.set('beePreCloseRequest', true);
            params.set('json', true);
            params.each(function(keyValue)
            {
                let key = keyValue.key;
                if(key.indexOf('item[') === 0){
                    params.unset(key);
                }
            });
            return window.order.loadArea(['message'], true, params);
        },
    };
});
