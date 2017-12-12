<?php

namespace TransactworldPayment\Transactworld\Controller\Standard;

class Cancel extends \TransactworldPayment\Transactworld\Controller\TransactworldAbstract {

    public function execute() {
        $this->getOrder()->cancel()->save();
        
        $this->messageManager->addErrorMessage(__('Your order has been can cancelled'));
        $this->getResponse()->setRedirect(
                $this->getCheckoutHelper()->getUrl('checkout')
        );
    }

}
