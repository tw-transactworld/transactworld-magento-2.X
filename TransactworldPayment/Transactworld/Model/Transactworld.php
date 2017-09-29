<?php


namespace TransactworldPayment\Transactworld\Model;

use Magento\Sales\Api\Data\TransactionInterface;

class Transactworld extends \Magento\Payment\Model\Method\AbstractMethod {

    const PAYMENT_TRANSACTWORLD_CODE = 'transactworld';
    const ACC_BIZ = 'transactworldbiz';
    const ACC_MONEY = 'transactworldmoney';

    protected $_code = self::PAYMENT_TRANSACTWORLD_CODE;

    /**
     *
     * @var \Magento\Framework\UrlInterface 
     */
    protected $_urlBuilder;
    protected $_supportedCurrencyCodes = array(
        'AFN', 'ALL', 'DZD', 'ARS', 'AUD', 'AZN', 'BSD', 'BDT', 'BBD',
        'BZD', 'BMD', 'BOB', 'BWP', 'BRL', 'GBP', 'BND', 'BGN', 'CAD',
        'CLP', 'CNY', 'COP', 'CRC', 'HRK', 'CZK', 'DKK', 'DOP', 'XCD',
        'EGP', 'EUR', 'FJD', 'GTQ', 'HKD', 'HNL', 'HUF', 'INR', 'IDR',
        'ILS', 'JMD', 'JPY', 'KZT', 'KES', 'LAK', 'MMK', 'LBP', 'LRD',
        'MOP', 'MYR', 'MVR', 'MRO', 'MUR', 'MXN', 'MAD', 'NPR', 'TWD',
        'NZD', 'NIO', 'NOK', 'PKR', 'PGK', 'PEN', 'PHP', 'PLN', 'QAR',
        'RON', 'RUB', 'WST', 'SAR', 'SCR', 'SGF', 'SBD', 'ZAR', 'KRW',
        'LKR', 'SEK', 'CHF', 'SYP', 'THB', 'TOP', 'TTD', 'TRY', 'UAH',
        'AED', 'USD', 'VUV', 'VND', 'XOF', 'YER'
    );
    
    private $checkoutSession;

    /**
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
      public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \TransactworldPayment\Transactworld\Helper\Transactworld $helper,
        \Magento\Sales\Model\Order\Email\Sender\OrderSender $orderSender,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Checkout\Model\Session $checkoutSession      
              
    ) {
        $this->helper = $helper;
        $this->orderSender = $orderSender;
        $this->httpClientFactory = $httpClientFactory;
        $this->checkoutSession = $checkoutSession;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger
        );

    }

    public function canUseForCurrency($currencyCode) {
        if (!in_array($currencyCode, $this->_supportedCurrencyCodes)) {
            return false;
        }
        return true;
    }

    public function getRedirectUrl() {
        return $this->helper->getUrl($this->getConfigData('redirect_url'));
    }

    public function getReturnUrl() {
        return $this->helper->getUrl($this->getConfigData('return_url'));
    }

    public function getCancelUrl() {
        return $this->helper->getUrl($this->getConfigData('cancel_url'));
    }

    /**
     * Return url according to environment
     * @return string
     */
    public function getCgiUrl() {
        $env = $this->getConfigData('environment');
        if ($env === 'live') {
            return $this->getConfigData('production_url');
        }
        return $this->getConfigData('sandbox_url');
    }

    public function buildCheckoutRequest() {
        $order = $this->checkoutSession->getLastRealOrder();
        $billing_address = $order->getBillingAddress();

        $params = array();
        $params["key"] = $this->getConfigData("merchant_key");
        if ($this->getConfigData('account_type') == self::ACC_MONEY) {
            $params["service_provider"] = $this->getConfigData("service_provider");
        }
		
        $params["toid"] = $this->getConfigData("toid");
        $params["totype"] = $this->getConfigData("totype");
		$params["partenerid"] = $this->getConfigData("partenerid");
        $params["ipaddr"] = $this->getConfigData("ipaddr");
        $params["pctype"] = "1_1|1_2";
        $params["paymenttype"] = "";
        $params["cardtype"] = "";
        $params["reservedField1"] = "";
        $params["reservedField2"] = "";
		$params["TMPL_CURRENCY"] = "USD";
		$params["currency"] = "USD";
                
                //$params["TMPL_CURRENCY"] = "RUB";
		//$params["currency"] = "RUB";
                
        $params["amount"] = round($order->getBaseGrandTotal(), 2);		
		$params["TMPL_AMOUNT"] = round($order->getBaseGrandTotal(), 2);
        $params["description"] = $this->checkoutSession->getLastRealOrderId();
		
        $params["firstname"] = $billing_address->getFirstName();
        $params["lastname"] = $billing_address->getLastname();
        $params["TMPL_city"]                 = $billing_address->getCity();
        $params["TMPL_state"]                = $billing_address->getRegion();
        $params["TMPL_zip"]                  = $billing_address->getPostcode();
        $params["TMPL_COUNTRY"]              = $billing_address->getCountryId();
        $params["TMPL_emailaddr"] = $order->getCustomerEmail();
        $params["phone"] = $billing_address->getTelephone();
		
		//$params["redirecturl"] = $this->getCancelUrl();
        $params["redirecturl"] = $this->getReturnUrl();
        //$params["redirecturl"] = $this->getReturnUrl();
		
		$checksum = MD5(trim($params['toid']) . "|" . trim($params['totype']) . "|" . trim($params['amount']) . "|" . trim($params['description']) . "|" . trim($params['redirecturl']) . "|" . trim($params['key']));
		
        $params["checksum"]=$checksum;

        //$params["hash"] = $this->generatePayuHash($params['toid'],
               /*  $params['amount'], $params['productinfo'], $params['firstname'],
                $params['email']);
 */
        return $params;
    }

    public function generateTransactworldHash($amount, $productInfo, $name,
            $email) {
        $SALT = $this->getConfigData('salt');

        $posted = array(
            'key' => $this->getConfigData("merchant_key"),
            'toid' => $this->getConfigData("toid"),
            'totype' => $this->getConfigData("totype"),
			'partenerid' => $this->getConfigData("partenerid"),
            'ipaddr' => $this->getConfigData("ipaddr"),
            'pctype' => "1_1|1_2",
            'amount' => $amount,
            'productinfo' => $productInfo,
            'firstname' => $name,
            'email' => $email,
        );

        $hashVarsSeq = explode('|', $hashSequence);
        $hash_string = '';
        foreach ($hashVarsSeq as $hash_var) {
            $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
            $hash_string .= '|';
        }
        $hash_string .= $SALT;
        return strtolower(hash('sha512', $hash_string));
    }

    //validate response
    public function validateResponse($returnParams) {
        if ($returnParams['status'] == 'Y' || $returnParams['status'] =='P'){
            return true;
        }
        else {
            return false;
        }

    }
    
    public function postProcessingSuccess(\Magento\Sales\Model\Order $order,
            \Magento\Framework\DataObject $payment, $response) {
        if($response['status']=='Y'){
         $order->setStatus('processing');   
         $order->save();
        }
        
    }
    
    
     public function postProcessingFail(\Magento\Sales\Model\Order $order,
            \Magento\Framework\DataObject $payment, $response) {
 
         $order->setStatus('canceled');   
         $order->save();
            
    }

}
