<?php


class mspYaCassaMsOnManagerCustomCssJs extends mspYaCassaPlugin
{
    public function run()
    {
        $page = $this->modx->getOption('page', $this->scriptProperties);

        switch ($page) {
            case 'settings':
                $this->mspyacassa->loadControllerJsCss($this->modx->controller, array(
                    'config'         => true,
                    'tools'          => true,
                    'payment/inject' => true,
                ));
                break;
            case 'orders':
                $this->mspyacassa->loadControllerJsCss($this->modx->controller, array(
                    'config'         => true,
                    'tools'          => true,
                    'order/inject'   => true
                ));
                break;
            default:
                break;
        }

    }
}