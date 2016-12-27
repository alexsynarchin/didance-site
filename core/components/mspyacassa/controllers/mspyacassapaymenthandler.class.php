<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

if (!class_exists('msPaymentInterface')) {
    require_once MODX_CORE_PATH . 'components/minishop2/model/minishop2/mspaymenthandler.class.php';
}

class mspYaCassaPaymentHandler extends msPaymentHandler implements msPaymentInterface
{
    /** @var modX $modx */
    public $modx;
    /** @var miniShop2 $ms2 */
    public $ms2;
    /** @var mspyacassa $mspyacassa */
    public $mspyacassa;
    /** @var array $config */
    public $config = array();

    /** @var array $params */
    public $params;

    const STATUS_PAID = 2;
    const STATUS_CANCELED = 4;

    /**
     * @param xPDOObject $object
     * @param array      $config
     */
    function __construct(xPDOObject $object, $config = array())
    {
        parent::__construct($object, $config);

        $fqn = $this->modx->getOption('mspyacassa_class', null, 'mspyacassa.mspyacassa', true);
        $path = $this->modx->getOption('mspyacassa_core_path', null,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/mspyacassa/');
        if (!$this->mspyacassa = $this->modx->getService($fqn, '', $path . 'model/',
            array('core_path' => $path))
        ) {
            return false;
        }

        $this->mspyacassa->initialize($this->modx->context->key, $this->config);
    }

    public function __call($n, array$p)
    {

        echo __METHOD__ . ' says: ' . $n;
    }

    public function send(msOrder $order)
    {
        $link = $this->getPaymentLink($order);

        return $this->success('', array('redirect' => $link));
    }

    public function getPaymentLink(msOrder $order)
    {
        $params = $this->loadParams($order);
        $form = $this->getPaymentForm($params);

        $options = array(
            'msorder' => $this->_getPaymentApiOrder($params),
            'mshash'  => $this->getOrderHash($order)
        );

        return $this->mspyacassa->getPaymentLink($form, $options);
    }

    public function process(array $params = array())
    {
        if (!$isSignature = $this->isPaymentSignature($params)) {
            $this->mspyacassa->log("[" . __CLASS__ . "] Signature failure", $params, true);

            return false;
        }
        /** @var msOrder $order */
        if (!$order = $this->modx->getObject('msOrder', (int)$params['api_order'])) {
            $this->mspyacassa->log("[" . __CLASS__ . "] Order failure", $params, true);

            return false;
        }

        if ($this->getOption('payment_check_hash') AND $params['mshash'] != $this->getOrderHash($order)) {
            $this->mspyacassa->log("[" . __CLASS__ . "] Order hash failure", $params, true);

            return false;
        }

        $this->savePaymentProperties($order, $params);

        return $this->receive($order, $params);
    }

    public function savePaymentProperties(msOrder $order, array $params = array())
    {
        /* save payment to properties */
        $properties = array_merge((array)$order->get('properties'), array('payment' => $params));
        $order->set('properties', $properties);

        return $order->save();
    }

    public function getPaymentProperties(msOrder $order)
    {
        /* get payment from properties */
        $payment = $this->modx->getOption('payment', (array)$order->get('properties'), array(), true);

        return $payment;
    }

    public function receive(msOrder $order, array $params = array())
    {
        $action = strtolower($this->getOption('action', $params, '', true));

        switch ($action) {
            case 'checkorder':
                exit($this->getPaymentSuccessAnswer($params));
                break;
            case 'paymentaviso':
                $this->changeOrderStatus($order, self::STATUS_PAID);
                exit($this->getPaymentSuccessAnswer($params));
                break;
            case 'cancelorder':
                $this->changeOrderStatus($order, self::STATUS_CANCELED);
                exit($this->getPaymentSuccessAnswer($params));
                break;
            default:
                exit($this->getPaymentFailureAnswer($params));
                break;
        }
    }


    public function isPaymentSignature(array $params = array())
    {
        return $this->mspyacassa->getPaymentSignature($params) == $this->getOption('md5', $params);
    }

    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        return $this->mspyacassa->getOption($key, $config, $default, $skipEmpty);
    }

    protected function loadParams(msOrder $order, $load = false)
    {
        if (!$this->params OR $load) {
            $this->params = $order->toArray('order_');
            if ($payment = $order->getOne('Payment')) {
                $this->params = array_merge($this->params, $payment->toArray('payment_'));
            }
            if ($profile = $order->getOne('UserProfile')) {
                $this->params = array_merge($this->params, $profile->toArray('profile_'));
            }
        }

        return $this->params;
    }

    protected function getMethodName($name = '')
    {
        return $this->mspyacassa->getMethodName($name);
    }

    public function isPaymentParams(array $params = array())
    {
        return $this->mspyacassa->isPaymentParams($params);
    }

    public function getPaymentForm(array $params = array(), array $options = array())
    {
        $form = $options;
        $paymentKeys = $this->mspyacassa->getPaymentKeysPaymentForm();
        foreach ($paymentKeys as $apiKey => $dataKey) {
            if (isset($params[$apiKey])) {
                $value = $params[$apiKey];
            } elseif (!$value = $this->getOption($dataKey, $params)) {
                $getMethod = $this->getMethodName($apiKey);
                if (method_exists($this, $getMethod)) {
                    $value = $this->$getMethod($params, $form);
                }
            }
            if (!is_null($value)) {
                $form[$apiKey] = $value;
            }
        }

        return $form;
    }

    protected function _getPaymentOrderNumber(array $params = array(), array $form = array())
    {
        return trim($this->getOption('order_num', $params, 0, true));
    }

    protected function _getPaymentPaymentType(array $params = array(), array $form = array())
    {
        $properties = $this->getOption('payment_properties', $params, array(), true);

        return trim($this->getOption('payment.type', $properties));
    }

    protected function _getPaymentSum(array $params = array(), array $form = array())
    {
        return number_format($this->getOption('order_cost', $params, 0, true), 2, '.', '');
    }

    protected function _getPaymentCustomerNumber(array $params = array(), array $form = array())
    {
        return trim($this->getOption('profile_email', $params));
    }

    protected function _getPaymentCpsEmail(array $params = array(), array $form = array())
    {
        return trim($this->getOption('profile_email', $params));
    }

    protected function _getPaymentCpsPhone(array $params = array(), array $form = array())
    {
        return trim($this->getOption('profile_phone', $params));
    }

    protected function _getPaymentApiOrder(array $params = array(), array $form = array(), array $options = array())
    {
        return trim($this->getOption('order_id', $params, 0, true));
    }

    public function getPaymentSuccessAnswer(
        array $params = array(),
        $code = 0,
        $message = null
    ) {
        return $this->mspyacassa->getPaymentSuccessAnswer($params, $code, $message);
    }

    public function getPaymentFailureAnswer(
        array $params = array(),
        $code = 100,
        $message = null
    ) {
        return $this->mspyacassa->getPaymentFailureAnswer($params, $code, $message);
    }

    protected function changeOrderStatus(msOrder $order, $status)
    {
        if (!$this->ms2) {
            $this->ms2 = $this->modx->getService('miniShop2');
        }


        return $this->ms2->changeOrderStatus($order->get('id'), $status);
    }

    public function getOrderHash(msOrder $order)
    {
        return md5(
            $order->get('id') .
            $order->get('num') .
            $order->get('cost') .
            $order->get('createdon')
        );
    }

}