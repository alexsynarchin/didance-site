<?php
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var mspyacassa $mspyacassa */
$mspyacassa = $modx->getService('mspyacassa', 'mspyacassa', $modx->getOption('mspyacassa_core_path', null,
        $modx->getOption('core_path') . 'components/mspyacassa/') . 'model/mspyacassa/');
$modx->lexicon->load('mspyacassa:default');

// handle request
$corePath = $modx->getOption('mspyacassa_core_path', null, $modx->getOption('core_path') . 'components/mspyacassa/');
$path = $modx->getOption('processorsPath', $mspyacassa->config, $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location'        => '',
));