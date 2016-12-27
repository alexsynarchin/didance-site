<?php

require_once dirname(__FILE__) . '/mspyacassapaymenthandler.class.php';

class mspYaCassaPaymentHoldHandler extends mspYaCassaPaymentHandler implements msPaymentInterface
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

    const STATUS_ON_HOLD = 21;
    const STATUS_WRITE_OFF = 22;
    const STATUS_TO_CANCEL = 24;

    /**
     * @param xPDOObject $object
     * @param array      $config
     */
    function __construct(xPDOObject $object, $config = array())
    {
        parent::__construct($object, $config);

        $this->mspyacassa->config['mws_cert'] = $this->getOption('hold_mws_cert', null);
        $this->mspyacassa->config['mws_private_key'] = $this->getOption('hold_mws_private_key', null);
        $this->mspyacassa->config['mws_cert_password'] = $this->getOption('hold_mws_cert_password', null);

    }

    public function receive(msOrder $order, array $params = array())
    {
        $action = strtolower($this->getOption('action', $params, '', true));

        switch ($action) {
            case 'paymentaviso':
                $this->changeOrderStatus($order, self::STATUS_ON_HOLD);
                exit($this->getPaymentSuccessAnswer($params));
                break;
            default:
                exit($this->getPaymentFailureAnswer($params));
                break;
        }
    }

    public function confirmPayment(msOrder $order)
    {
        $confirm = false;
        $payment = $this->getPaymentProperties($order);
        if (!$invoiceId = $this->modx->getOption('invoiceId', $payment)) {
            $this->mspyacassa->log("[" . __CLASS__ . "] could not get invoiceId", $order->toArray(), true);

            return $confirm;
        }

        if (!$response = $this->mspyacassa->sendConfirmPayment($invoiceId, $order->get('cost'))) {
            return $confirm;
        }

        $status = (string)$response['status'];
        $error = (string)$response['error'];

        switch (true) {
            case $status == 0 AND empty($error):
                $confirm = $this->changeOrderStatus($order, self::STATUS_PAID);
                break;
            case $status == 1 AND empty($error):
                $confirm = $this->changeOrderStatus($order, self::STATUS_ON_HOLD);
                break;
            case $status == 3:
                $this->mspyacassa->log("[" . __CLASS__ . "] Error ", $error, true);
                $confirm = $this->changeOrderStatus($order, self::STATUS_CANCELED);
                break;
        }

        return $confirm;
    }

    public function cancelPayment(msOrder $order)
    {
        $confirm = false;
        $payment = $this->getPaymentProperties($order);
        if (!$invoiceId = $this->modx->getOption('invoiceId', $payment)) {
            $this->mspyacassa->log("[" . __CLASS__ . "] could not get invoiceId", $order->toArray(), true);

            return $confirm;
        }

        if (!$response = $this->mspyacassa->sendCancelPayment($invoiceId)) {
            return $confirm;
        }

        $status = (string)$response['status'];
        $error = (string)$response['error'];

        switch (true) {
            case $status == 0 AND empty($error):
            case $status == 3:
                $confirm = $this->changeOrderStatus($order, self::STATUS_CANCELED);
                break;
            case $status == 1 AND empty($error):
                $confirm = $this->changeOrderStatus($order, self::STATUS_ON_HOLD);
                break;
        }

        return $confirm;
    }


    protected function _getPaymentShopId(
        array $params = array(),
        array $form = array(),
        array $options = array()
    ) {
        return $this->getOption('hold_payment_shop_id', null);
    }

    protected function _getPaymentScid(
        array $params = array(),
        array $form = array(),
        array $options = array()
    ) {
        return $this->getOption('hold_payment_sc_id', null);
    }
    
}