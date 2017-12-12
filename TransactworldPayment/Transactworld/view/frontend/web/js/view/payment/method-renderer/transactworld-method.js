define(
    [
        'Magento_Checkout/js/view/payment/default',
        'TransactworldPayment_Transactworld/js/action/set-payment-method',
    ],
    function(Component,setPaymentMethod){
    'use strict';

    return Component.extend({
        defaults:{
            'template':'TransactworldPayment_Transactworld/payment/transactworld'
        },
        redirectAfterPlaceOrder: false,
        
        afterPlaceOrder: function () {
            setPaymentMethod();    
        }

    });
});
