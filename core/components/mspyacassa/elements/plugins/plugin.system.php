<?php

/** @var array $scriptProperties */
$corePath = $modx->getOption('mspyacassa_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/mspyacassa/');
$mspyacassa = $modx->getService('mspyacassa', 'mspyacassa', $corePath . 'model/mspyacassa/',
    array('core_path' => $corePath));
if (!$mspyacassa) {
    return;
}

$className = 'mspYaCassa' . $modx->event->name;
$modx->loadClass('mspYaCassaPlugin', $mspyacassa->getOption('modelPath') . 'mspyacassa/systems/', true, true);
$modx->loadClass($className, $mspyacassa->getOption('modelPath') . 'mspyacassa/systems/', true, true);
if (class_exists($className)) {
    /** @var $mspyacassa $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}
return;
