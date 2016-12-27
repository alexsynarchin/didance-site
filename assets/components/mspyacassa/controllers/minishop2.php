<?php

ini_set('apc.cache_by_default', 'Off');

define('MODX_API_MODE', true);
require dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');

//$modx->log(1, var_export($_REQUEST, 1));

$class = 'mspYaCassaPaymentHandler';
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
if (!class_exists($class)) {
    exit("Error: could not load payment class '{$class}'");
}

/** @var msPaymentInterface|mspYaCassaPaymentHandler $handler */
$handler = new $class($modx->newObject('msOrder'));

if ($handler->mspyacassa->getOption('payment_show_log', null)) {
    $handler->mspyacassa->log("[{$class}] Request", $_REQUEST, true);
}

if (!$handler->isPaymentParams($_REQUEST)) {
    $handler->mspyacassa->log("[{$class}] Failed to get the data", $_REQUEST, true);
    exit($handler->getPaymentFailureAnswer($_REQUEST));
}

if (!$handler->process($_REQUEST)) {
    exit($handler->getPaymentFailureAnswer($_REQUEST));
}