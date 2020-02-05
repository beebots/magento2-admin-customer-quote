define([
    'jquery',
    'BeeBots_AdminCustomerQuote/js/admin-order-common',
    'Magento_Sales/order/create/scripts'
], function($, adminOrderCommon){
    'use strict';

    return {

        quoteId: false,
        url: false,

        init: function(quoteId, url) {
            this.quoteId = quoteId;
            this.url = url;
            $('#email_quote').click(this.onSendEmailClicked.bind(this));
        },

        onSendEmailClicked: function() {
            adminOrderCommon.startLoader();
            let promise = adminOrderCommon.save();
            let afterSendQuoteEmailPromise = promise.pipe(this.sendQuoteEmail.bind(this, this.quoteId, this.url));
            afterSendQuoteEmailPromise.done(this.onEmailSent);
            afterSendQuoteEmailPromise.fail(this.onEmailFail);
        },

        sendQuoteEmail: function(quoteId, url) {
            if(!quoteId){
                console.error('quote id is missing');
                return;
            }
            if(!url){
                console.error('url is required');
                return;
            }
            adminOrderCommon.startLoader(); // something is turning off the loader in the middle, so start it again
            return $.ajax({
                url: url,
                method: 'POST',
                data: {'quote_id': quoteId},
                error: null, // override global value set in lib/web/mage/backend
                complete: null // override global value set in lib/web/mage/backend
            });
        },

        onEmailSent: function(){
            // TODO: show success message?
            console.log('finished sending email');
            adminOrderCommon.stopLoader();
        },

        onEmailFail: function(){
            // TODO: show error message?
            console.log('error sending email');
            adminOrderCommon.stopLoader();
        }
    };
});