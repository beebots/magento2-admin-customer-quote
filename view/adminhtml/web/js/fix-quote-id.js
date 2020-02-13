define([
    'Magento_Sales/order/create/form',
], function () {
    'use strict';

    return {
        init: function(quoteId){
            debugger;
            window.order.quoteId = quoteId;
        }
    };
});