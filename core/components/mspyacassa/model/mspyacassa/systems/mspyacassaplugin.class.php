<?php


abstract class mspYaCassaPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var mspyacassa $mspyacassa */
    protected $mspyacassa;
    /** @var array $scriptProperties */
    protected $scriptProperties;

    public function __construct($modx, &$scriptProperties)
    {
        /** @var modX $modx */
        $this->modx = &$modx;
        $this->scriptProperties =& $scriptProperties;

        if (!$this->mspyacassa = &$this->modx->mspyacassa) {
            return;
        }

        $this->mspyacassa->initialize($this->modx->context->key);
    }

    abstract public function run();
}