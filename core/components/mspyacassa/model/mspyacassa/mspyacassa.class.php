<?php


/**
 * The base class for mspyacassa.
 */
class mspyacassa
{
    /* @var modX $modx */
    public $modx;

    /** @var mixed|null $namespace */
    public $namespace = 'mspyacassa';
    /** @var string $partner */
    public $partner = 'MODX.VGRISH';
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();

    /** @var miniShop2 $miniShop2 */
    public $miniShop2;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/mspyacassa/');
        $assetsPath = $this->getOption('assets_path', $config,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/mspyacassa/');
        $assetsUrl = $this->getOption('assets_url', $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/mspyacassa/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'namespace'       => $this->namespace,
            'connectorUrl'    => $connectorUrl,
            'assetsBasePath'  => MODX_ASSETS_PATH,
            'assetsBaseUrl'   => MODX_ASSETS_URL,
            'assetsPath'      => $assetsPath,
            'assetsUrl'       => $assetsUrl,
            'actionUrl'       => $assetsUrl . 'action.php',
            'cssUrl'          => $assetsUrl . 'css/',
            'jsUrl'           => $assetsUrl . 'js/',
            'corePath'        => $corePath,
            'modelPath'       => $corePath . 'model/',
            'handlersPath'    => $corePath . 'handlers/',
            'processorsPath'  => $corePath . 'processors/',
            'templatesPath'   => $corePath . 'elements/templates/mgr/',
            'jsonResponse'    => true,
            'prepareResponse' => true,
            'showLog'         => false,
            'replacePattern'  => "#[\r\n\t]+#is",

        ), $config);

        $this->modx->addPackage('mspyacassa', $this->getOption('modelPath'));
        $this->modx->lexicon->load('mspyacassa:default');
        $this->namespace = $this->getOption('namespace', $config, 'mspyacassa');

        $level = $modx->getLogLevel();
        $modx->setLogLevel(xPDO::LOG_LEVEL_FATAL);
        if ($this->miniShop2 = $modx->getService('miniShop2')) {
            if (!($this->miniShop2 instanceof miniShop2)) {
                $this->miniShop2 = false;
            }
        }
        $modx->setLogLevel($level);
    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     *
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    /**
     * Initializes component into different contexts.
     *
     * @param string $ctx The context to load. Defaults to web.
     * @param array  $scriptProperties
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        $this->modx->error->reset();
        $this->config = array_merge($this->config, $scriptProperties, array('ctx' => $ctx));

        if (!empty($this->initialized[$ctx])) {
            return true;
        }

        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') OR !MODX_API_MODE) {

                    $this->initialized[$ctx] = true;
                }
                break;
        }

        return true;
    }

    /**
     * @param string $action
     * @param array  $data
     *
     * @return array|modProcessorResponse|string
     */
    public function runProcessor($action = '', $data = array())
    {
        $this->modx->error->reset();
        $processorsPath = $this->getOption('processorsPath', null, MODX_CORE_PATH, true);
        $prepareResponse = $this->getOption('prepareResponse', null, false, true);
        /* @var modProcessorResponse $response */
        $response = $this->modx->runProcessor($action, $data, array(
            'processors_path' => $processorsPath
        ));

        return $prepareResponse ? $this->prepareResponse($response) : $response;
    }

    /**
     * This method returns prepared response
     *
     * @param mixed $response
     *
     * @return array|string $response
     */
    public function prepareResponse($response)
    {
        if ($response instanceof modProcessorResponse) {
            $output = $response->getResponse();
        } else {
            $message = $response;
            if (empty($message)) {
                $message = $this->lexicon('err_unknown');
            }
            $output = $this->failure($message);
        }
        if ($this->config['jsonResponse'] AND is_array($output)) {
            $output = $this->modx->toJSON($output);
        } elseif (!$this->config['jsonResponse'] AND !is_array($output)) {
            $output = $this->modx->fromJSON($output);
        }

        return $output;
    }

    /**
     * return lexicon message if possibly
     *
     * @param string $message
     *
     * @return string $message
     */
    public function lexicon($message, $placeholders = array())
    {
        $key = '';
        if ($this->modx->lexicon->exists($message)) {
            $key = $message;
        } elseif ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
            $key = $this->namespace . '_' . $message;
        }
        if ($key !== '') {
            $message = $this->modx->lexicon->process($key, $placeholders);
        }

        return $message;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function failure($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => true,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param bool   $showLog
     * @param bool   $writeLog
     */
    public function log($message = '', $data = array(), $showLog = false)
    {
        if ($this->getOption('showLog', null, $showLog, true)) {
            if (!empty($message)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($message, 1));
            }
            if (!empty($data)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($data, 1));
            }
        }
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        return $array;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * @return string
     */
    public function getVersionMiniShop2()
    {
        return isset($this->miniShop2->version) ? $this->miniShop2->version : '2.2.0';
    }

    /**
     * @return array|mixed
     */
    public function getPaymentIds($class = '')
    {
        if (empty($class)) {
            $class = $this->getPaymentClass();
        }
        if (!is_array($class)) {
            $class = array($class);
        }

        $data = array();
        $q = $this->modx->newQuery('msPayment');
        $q->where(array('class:IN' => $class));
        $q->select('id');
        $q->limit(0);
        if ($q->prepare() && $q->stmt->execute()) {
            $data = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $data;
    }

    /**
     * @return mixed|null
     */
    public function getPaymentClass()
    {
        return $this->explodeAndClean($this->getOption('payment_class', null,
            'mspYaCassaPaymentHandler,mspYaCassaPaymentHoldHandler', true));
    }

    /** @return array Inject Payment Tabs */
    public function getInjectPaymentTabs()
    {
        $fields = $this->getOption('inject_payment_tabs', null,
            'add', true);
        $fields .= ',add';
        $fields = $this->explodeAndClean($fields);

        return $fields;
    }

    /** @return array Inject Order Tabs */
    public function getInjectOrderTabs()
    {
        $fields = $this->getOption('inject_order_tabs', null,
            'payment', true);
        $fields .= ',payment';
        $fields = $this->explodeAndClean($fields);

        return $fields;
    }


    /**
     * @param modManagerController $controller
     * @param array                $setting
     */
    public function loadControllerJsCss(modManagerController &$controller, array $setting = array())
    {
        $controller->addLexiconTopic('mspyacassa:default');

        $config = $this->config;
        foreach (array('controller') as $key) {
            if (isset($config[$key])) {
                unset($config[$key]);
            }
        }

        $config['inject_payment_tabs'] = $this->getInjectPaymentTabs();
        $config['inject_order_tabs'] = $this->getInjectOrderTabs();

        $config['miniShop2']['version'] = $this->getVersionMiniShop2();
        $config['miniShop2']['payment']['ids'] = $this->getPaymentIds();
        $config['miniShop2']['payment']['class'] = $this->getPaymentClass();

        if (!empty($setting['config'])) {
            $controller->addHtml("<script type='text/javascript'>mspyacassa.config={$this->modx->toJSON($config)}</script>");
        }

        if (!empty($setting['tools'])) {
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/mspyacassa.js');
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/tools.js');
        }

        if (!empty($setting['payment/inject'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/payment/inject/inject.tab.js');
        }

        if (!empty($setting['order/inject'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/order/inject/inject.tab.js');
        }


    }

    public function formatDate(\DateTime $date)
    {
        $performedDatetime = $date->format("Y-m-d") . "T" . $date->format("H:i:s") . ".000" . $date->format("P");

        return $performedDatetime;
    }

    public function formatDateForMWS(\DateTime $date)
    {
        $performedDatetime = $date->format("Y-m-d") . "T" . $date->format("H:i:s") . ".000Z";

        return $performedDatetime;
    }

    public function getPaymentPaymentTestMode(array $params = array(), array $form = array())
    {
        return (bool)$this->getOption('payment_test_mode', null, false, true);
    }

    public function getPaymentPaymentUrl(array $params = array(), array $form = array())
    {
        if ($this->getPaymentPaymentTestMode()) {
            $url = $this->getOption('payment_test_url', null, 'https://demomoney.yandex.ru/eshop.xml', true);
        } else {
            $url = $this->getOption('payment_url', null, 'https://money.yandex.ru/eshop.xml', true);
        }

        return $url;
    }

    public function getOperationUrl()
    {
        if ($this->getPaymentPaymentTestMode()) {
            $url = $this->getOption('operation_test_url', null,
                'https://penelope-demo.yamoney.ru:8083/webservice/mws/api/', true);
        } else {
            $url = $this->getOption('operation_url', null, 'https://penelope.yamoney.ru:443/webservice/mws/api/', true);
        }

        return $url;
    }

    /*
     * https://tech.yandex.ru/money/doc/payment-solution/payment-form/payment-form-http-docpage/
     */
    public function getPaymentKeysPaymentForm()
    {
        return array(
            'shopId' => null,
            'scid'   => null,

            'orderNumber'    => null,
            'sum'            => null,
            'paymentType'    => null,
            'customerNumber' => null,

            'shopSuccessURL' => 'payment_success_url',
            'shopFailURL'    => 'payment_failure_url',
            'shopDefaultUrl' => 'payment_default_url',

            'cps_email'   => null,
            'cps_phone'   => null,
            'api_partner' => null,
            'api_order'   => null,
        );
    }


    protected function _getPaymentShopId(
        array $params = array(),
        array $form = array(),
        array $options = array()
    ) {
        return $this->getOption('payment_shop_id', null);
    }

    protected function _getPaymentScid(
        array $params = array(),
        array $form = array(),
        array $options = array()
    ) {
        return $this->getOption('payment_sc_id', null);
    }

    protected function _getPaymentShopSuccessURL(
        array $params = array(),
        array $form = array(),
        array $options = array()
    ) {
        $id = $this->getOption('payment_success_id', null, $this->modx->getOption('site_start'), true);
        $options = array_merge($options, array('action' => 'success'));

        return $this->modx->makeUrl($id, '', $options, 'full', array('xhtml_urls' => false));
    }

    protected function _getPaymentShopFailURL(
        array $params = array(),
        array $form = array(),
        array $options = array()
    ) {
        $id = $this->getOption('payment_failure_id', null, $this->modx->getOption('site_start'), true);
        $options = array_merge($options, array('action' => 'failure'));

        return $this->modx->makeUrl($id, '', $options, 'full', array('xhtml_urls' => false));
    }

    protected function _getPaymentShopDefaultUrl(
        array $params = array(),
        array $form = array(),
        array $options = array()
    ) {
        $id = $this->getOption('payment_default_id', null, $this->modx->getOption('site_start'), true);
        $options = array_merge($options, array('action' => 'default'));

        return $this->modx->makeUrl($id, '', $options, 'full', array('xhtml_urls' => false));
    }

    protected function _getPaymentApiPartner(array $params = array(), array $form = array(), array $options = array())
    {
        return $this->getOption('payment_partner', null, $this->partner, true);
    }


    protected function _getPaymentPassword(array $params = array(), array $form = array())
    {
        return $this->getOption('payment_password', null, '', true);
    }

    public function getMethodName($name = '')
    {
        $name = '_getPayment' . ucfirst(str_replace(array('_', '.'), array('', ''), $name));

        return $name;
    }

    public function getPaymentForm(array $params = array(), array $options = array())
    {
        $form = $options;
        $paymentKeys = $this->getPaymentKeysPaymentForm();
        foreach ($paymentKeys as $apiKey => $dataKey) {
            if (isset($params[$apiKey])) {
                $value = $params[$apiKey];
            } elseif (!$value = $this->getOption($dataKey, $params)) {
                $getMethod = $this->getMethodName($apiKey);

                if (method_exists($this, $getMethod)) {
                    $value = $this->$getMethod($params, $form, $options);
                }
            }
            if (!is_null($value)) {
                $form[$apiKey] = $value;
            }
        }

        return $form;
    }

    public function getPaymentLink(array $params = array(), array $options = array())
    {
        $form = $this->getPaymentForm($params, $options);
        $url = $this->getPaymentPaymentUrl() . '?' . http_build_query($form);

        return $url;
    }

    public function getPaymentSignature(array $params = array())
    {
        $keys = array(
            'action',
            'orderSumAmount',
            'orderSumCurrencyPaycash',
            'orderSumBankPaycash',
            'shopId',
            'invoiceId',
            'customerNumber',
        );

        $signature = array();
        foreach ($keys as $key) {
            $signature[] = (isset($params[$key])) ? trim($params[$key]) : '';
        }

        $signature[] = $this->_getPaymentPassword();
        $signature = strtoupper(md5(implode(';', $signature)));

        return $signature;
    }

    public function isPaymentParams(array $params = array())
    {
        return isset(
            $params['action'],
            $params['shopId'],
            $params['invoiceId'],
            $params['orderSumAmount'],
            $params['orderSumCurrencyPaycash'],
            $params['orderSumBankPaycash'],
            $params['customerNumber'],
            $params['api_order']
        );
    }

    public function getPaymentSuccessAnswer(
        array $params = array(),
        $code = 0,
        $message = null
    ) {
        $action = isset($params['action']) ? $params['action'] : '';
        $shopId = isset($params['shopId']) ? $params['shopId'] : '';
        $invoiceId = isset($params['invoiceId']) ? $params['invoiceId'] : '';

        return $this->paymentResponse($action, $shopId, $invoiceId, $code, $message);
    }


    public function getPaymentFailureAnswer(
        array $params = array(),
        $code = 100,
        $message = null
    ) {
        $action = isset($params['action']) ? $params['action'] : '';
        $shopId = isset($params['shopId']) ? $params['shopId'] : '';
        $invoiceId = isset($params['invoiceId']) ? $params['invoiceId'] : '';

        return $this->paymentResponse($action, $shopId, $invoiceId, $code, $message);
    }

    protected function paymentResponse($action, $shopId, $invoiceId, $code, $message = null)
    {
        header("Content-type: text/xml; charset=utf-8");

        $response = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<' . $action . 'Response performedDatetime="' . $this->formatDate(new DateTime()) .
            '" code="' . $code . '" ' . ($message != null ? 'message="' . $message . '"' : "") .
            ' invoiceId="' . $invoiceId . '" shopId="' . $shopId . '"/>';

        return $response;
    }

    /*****************************************************************************/

    public function sendConfirmPayment($orderId, $amount)
    {
        $requestParams = array(
            'requestDT' => $this->formatDateForMWS(new DateTime()),
            'orderId'   => $orderId,
            'amount'    => $amount,
            'currency'  => 'RUB'
        );
        $result = $this->sendRequest('confirmPayment', $requestParams);

        return $result;
    }

    public function sendCancelPayment($orderId)
    {
        $requestParams = array(
            'requestDT' => $this->formatDateForMWS(new DateTime()),
            'orderId'   => $orderId
        );
        $result = $this->sendRequest('cancelPayment', $requestParams);

        return $result;
    }

    protected function sendRequest($action, $data, $contentType = 'x-www-form-urlencoded')
    {
        $ch = curl_init();
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL            => $this->getOperationUrl() . $action,
                CURLOPT_HTTPHEADER     => array('Content-type: application/' . $contentType),
                CURLOPT_CONNECTTIMEOUT => 20,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_VERBOSE        => 1,
                CURLOPT_POST           => 0,
                CURLOPT_HEADER         => 0,

                CURLOPT_SSLCERT       => $this->getOption('mws_cert', null),
                CURLOPT_SSLKEY        => $this->getOption('mws_private_key', null),
                CURLOPT_SSLCERTPASSWD => $this->getOption('mws_cert_password', null),

                CURLOPT_POSTFIELDS => http_build_query($data),
            )
        );

        $data = null;
        try {
            $data = curl_exec($ch);
            if (!$data) {
                trigger_error(curl_error($ch));
            }
            curl_close($ch);
        } catch (HttpException $ex) {
            echo $ex;
        }

        libxml_use_internal_errors(true);
        $data = new SimpleXMLElement($data);

        $this->log($data, null);

        return $data;
    }

}