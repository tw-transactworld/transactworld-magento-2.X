<?php

namespace TransactworldPayment\Transactworld\Model;

class Account implements \Magento\Framework\Option\ArrayInterface {

    const ACC_BIZ = 'transactworldbiz';
    const ACC_MONEY = 'transactworldmoney';

    /**
     * Possible environment types
     * 
     * @return array
     */
    public function toOptionArray() {
        return [
            [
                'value' => self::ACC_BIZ,
                'label' => 'TransactworldBiz',
            ],
            [
                'value' => self::ACC_MONEY,
                'label' => 'TransactworldMoney'
            ]
        ];
    }

}
