define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
],function(Component,renderList){
    'use strict';
    renderList.push({
        type : 'transactworld',
        component : 'TransactworldPayment_Transactworld/js/view/payment/method-renderer/transactworld-method'
    });

    return Component.extend({});
})
