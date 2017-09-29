<?php
namespace TransactworldPayment\Transactworld\Model;


class Environment implements \Magento\Framework\Option\ArrayInterface
{
    const ENVIRONMENT_PRODUCTION    = 'live';
    const ENVIRONMENT_SANDBOX       = 'test';

    /**
     * Possible environment types
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENVIRONMENT_SANDBOX,
                'label' => 'Test Mode',
            ],
            [
                'value' => self::ENVIRONMENT_PRODUCTION,
                'label' => 'Live Mode'
            ]
        ];
    }
}
