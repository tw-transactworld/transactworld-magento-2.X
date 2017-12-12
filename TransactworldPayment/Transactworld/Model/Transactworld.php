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
		
		/********************************************/
			
					$country_code = array(
					"AF"=>"093", 
					"AX"=>"358", 
					"AL"=>"355",
					"DZ"=>"231",
					"AS"=>"684",
					"AD"=>"376",
					"AO"=>"244",
					"AI"=>"001",
					"AQ"=>"000",
					"AG"=>"001",
					"AR"=>"054",
					"AM"=>"374",
					"AW"=>"297",
					"AU"=>"061",
					"AT"=>"043",
					"AZ"=>"994",
					"BS"=>"001",
					"BH"=>"973",
					"BD"=>"880",
					"BB"=>"001",
					"BY"=>"375",
					"BE"=>"032",
					"BZ"=>"501",
					"BJ"=>"229",
					"BM"=>"001",
					"BT"=>"975",
					"BO"=>"591",
					"BA"=>"387",
					"BW"=>"267",
					"BV"=>"000",
					"BR"=>"055",
					"IO"=>"246",
					"VG"=>"001",
					"BN"=>"673",
					"BG"=>"359",
					"BF"=>"226",
					"BI"=>"257",
					"KH"=>"855",
					"CM"=>"237",
					"CA"=>"001",
					"CV"=>"238",
					"KY"=>"001",
					"CF"=>"236",
					"TD"=>"235",
					"CL"=>"056",
					"CN"=>"086",
					"CX"=>"061",
					"CC"=>"061",
					"CC"=>"061",
					"CO"=>"057",
					"KM"=>"269",
					"CK"=>"682",
					"CR"=>"506",
					"CI"=>"225",
					"HR"=>"385",
					"CU"=>"053",
					"CY"=>"357",
					"CZ"=>"420",
					"CD"=>"243",
					"DK"=>"045",
					"DJ"=>"253",
					"DM"=>"001",
					"DO"=>"001",
					"EC"=>"593",
					"EG"=>"020",
					"SV"=>"503",
					"GQ"=>"240",
					"ER"=>"291",
					"EE"=>"372",
					"ET"=>"251",
					"FK"=>"500",
					"FO"=>"298",
					"FJ"=>"679",
					"FI"=>"358",
					"FR"=>"033",
					"GF"=>"594",
					"PF"=>"689",
					"TF"=>"000",
					"GA"=>"241",
					"GM"=>"220",
					"GE"=>"995",
					"DE"=>"049",
					"GH"=>"233",
					"GI"=>"350",
					"GR"=>"030",
					"GL"=>"299",
					"GD"=>"001",
					"GP"=>"590",
					"GU"=>"001",
					"GT"=>"502",
					"GG"=>"000",
					"GN"=>"224",
					"GW"=>"245",
					"GY"=>"592",
					"HT"=>"509",
					"HM"=>"672",
					"HN"=>"504",
					"HK"=>"852",
					"HU"=>"036",
					"IS"=>"354",
					"IN"=>"091",
					"ID"=>"062",
					"IR"=>"098",
					"IQ"=>"964",
					"IE"=>"353",
					"IL"=>"972",
					"IT"=>"039",
					"JM"=>"001",
					"JP"=>"081",
					"JE"=>"044",
					"JO"=>"962",
					"KZ"=>"007",
					"KE"=>"254",
					"KI"=>"686",
					"KW"=>"965",
					"KG"=>"996",
					"LA"=>"856",
					"LV"=>"371",
					"LB"=>"961",
					"LS"=>"266",
					"LR"=>"231",
					"LY"=>"218",
					"LI"=>"423",
					"LT"=>"370",
					"LU"=>"352",
					"MO"=>"853",
					"MK"=>"389",
					"MG"=>"261",
					"MW"=>"265",
					"MY"=>"060",
					"MV"=>"960",
					"ML"=>"223",
					"MT"=>"356",
					"MH"=>"692",
					"MQ"=>"596",
					"MR"=>"222",
					"MU"=>"230",
					"YT"=>"269",
					"MX"=>"052",
					"FM"=>"691",
					"MD"=>"373",
					"MC"=>"377",
					"MN"=>"976",
					"ME"=>"382",
					"MS"=>"001",
					"MA"=>"212",
					"MZ"=>"258",
					"MM"=>"095",
					"NA"=>"264",
					"NR"=>"674",
					"NP"=>"977",
					"AN"=>"599",
					"NL"=>"031",
					"NC"=>"687",
					"NZ"=>"064",
					"NI"=>"505",
					"NE"=>"227",
					"NG"=>"234",
					"NU"=>"683",
					"NF"=>"672",
					"KP"=>"850",
					"MP"=>"001",
					"NO"=>"047",
					"OM"=>"968",
					"PK"=>"092",
					"PW"=>"680",
					"PS"=>"970",
					"PA"=>"507",
					"PG"=>"675",
					"PY"=>"595",
					"PE"=>"051",
					"PH"=>"063",
					"PN"=>"064",
					"PL"=>"048",
					"PT"=>"351",
					"PR"=>"001",
					"QA"=>"974",
					"CG"=>"242",
					"RE"=>"262",
					"RO"=>"040",
					"RU"=>"007",
					"RW"=>"250",
					"BL"=>"590",
					"SH"=>"290",
					"KN"=>"001",
					"LC"=>"001",
					"MF"=>"590",
					"PM"=>"508",
					"VC"=>"001",
					"WS"=>"685",
					"SM"=>"378",
					"ST"=>"239",
					"SA"=>"966",
					"SN"=>"221",
					"RS"=>"381",
					"SC"=>"248",
					"SL"=>"232",
					"SG"=>"065",
					"SK"=>"421",
					"SI"=>"386",
					"SB"=>"677",
					"SO"=>"252",
					"ZA"=>"027",
					"GS"=>"000",
					"KR"=>"082",
					"ES"=>"034",
					"LK"=>"094",
					"SD"=>"249",
					"SR"=>"597",
					"SJ"=>"047",
					"SZ"=>"268",
					"SE"=>"046",
					"CH"=>"041",
					"SY"=>"963",
					"TW"=>"886",
					"TJ"=>"992",
					"TZ"=>"255",
					"TH"=>"066",
					"TL"=>"670",
					"TG"=>"228",
					"TK"=>"690",
					"TO"=>"676",
					"TT"=>"001",
					"TN"=>"216",
					"TR"=>"090",
					"TM"=>"993",
					"TC"=>"001",
					"TV"=>"688",
					"UG"=>"256",
					"UA"=>"380",
					"AE"=>"971",
					"GB"=>"044",
					"US"=>"001",
					"VI"=>"001",
					"UY"=>"598",
					"UZ"=>"998",
					"VU"=>"678",
					"VA"=>"379",
					"VE"=>"058",
					"VN"=>"084",
					"WF"=>"681",
					"EH"=>"212",
					"YE"=>"967",
					"ZM"=>"260",
					"ZW"=>"263"
					);
			
			/*******************************************/
		
		
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
		$params["TMPL_CURRENCY"] = $order->getOrderCurrencyCode();
		//$params["TMPL_CURRENCY"] = "USD";
		$params["currency"] = $order->getOrderCurrencyCode();
		//$params["currency"] = "USD";
                
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
		$country_value = $country_code[$billing_address->getCountryId()];
        $params["telnocc"] = $country_value;
		
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
