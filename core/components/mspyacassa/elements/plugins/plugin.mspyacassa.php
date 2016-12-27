<?php

if ($modx->event->name != 'msOnChangeOrderStatus') {
    return;
}

$fqn = $modx->getOption('minishop2_class', null, 'minishop2.minishop2', true);
$corePath = $modx->getOption('minishop2_class_path', null, MODX_CORE_PATH . 'components/minishop2/', true);

/** @var miniShop2 $miniShop2 */
$miniShop2 = $modx->getService(
    $fqn,
    '',
    $corePath . 'model/minishop2/',
    array('core_path' => $corePath)
);
if (!$miniShop2) {
    exit("Error: could not load class 'miniShop2' ");
}
$miniShop2->loadCustomClasses('payment');

$class = 'mspYaCassaPaymentHoldHandler';
if (!class_exists($class)) {
    $modx->log(modX::LOG_LEVEL_ERROR, "[mspYaCassa] Error: not load payment class '{$class}'");

    return;
}

/** @var msOrder $order */
/** @var msPayment $payment */

$payment = $order->getOne('Payment');
if (!$payment OR $payment->get('class') != $class) {
    return;
}

const STATUS_ON_HOLD = 21;
const STATUS_WRITE_OFF = 22;
const STATUS_TO_CANCEL = 24;

/** @var $status */
if (!in_array($status, array(STATUS_WRITE_OFF, STATUS_TO_CANCEL))) {
    return;
}

/** @var msPaymentInterface|mspYaCassaPaymentHoldHandler $handler */
$handler = new $class($order);

$change = false;
switch ($status) {
    case STATUS_WRITE_OFF:
        /* Запрос подтверждения отложенного платежа (confirmPayment) */

        $change = $handler->confirmPayment($order);
        break;

    case STATUS_TO_CANCEL:
        /* Запрос аннулирования отложенного платежа (cancelPayment) */
        $change = $handler->cancelPayment($order);
        break;
}

$response = array(
    'success' => (int)$change == true ? true : false,
    'message' => (int)$change == true ? '' : $change,
    'data'    => array(),
);
echo $modx->toJSON($response);
exit;
