id: 12
source: 1
name: pdoTools
category: pdoTools
properties: null
static_file: core/components/pdotools/elements/plugins/plugin.pdotools.php

-----

/** @var modX $modx */
switch ($modx->event->name) {

    case 'OnMODXInit':
        $fqn = $modx->getOption('pdoTools.class', null, 'pdotools.pdotools', true);
        $path = $modx->getOption('pdotools_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
        $modx->loadClass($fqn, $path, false, true);

        $fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
        $path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
        $modx->loadClass($fqn, $path, false, true);
        break;

    case 'OnBeforeSaveWebPageCache':
        if (!empty($modx->config['fenom_jscripts'])) {
            foreach ($modx->config['fenom_jscripts'] as $key => $value) {
                unset($modx->resource->_jscripts[$key]);
            }
        }
        if (!empty($modx->config['fenom_sjscripts'])) {
            foreach ($modx->config['fenom_sjscripts'] as $key => $value) {
                unset($modx->resource->_sjscripts[$key]);
            }
        }
        if (!empty($modx->config['fenom_loadedscripts'])) {
            foreach ($modx->config['fenom_loadedscripts'] as $key => $value) {
                unset($modx->resource->_loadedjscripts[$key]);
            }
        }
        break;

    case 'OnSiteRefresh':
        /** @var pdoTools $pdoTools */
        if ($pdoTools = $modx->getService('pdoTools')) {
            if ($pdoTools->clearFileCache()) {
                $modx->log(modX::LOG_LEVEL_INFO, $modx->lexicon('refresh_default') . ': pdoTools');
            }
        }
        break;
}