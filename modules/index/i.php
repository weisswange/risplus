<?php

class Index extends App implements Module
{
    var $oDb = null;
    var $oSmarty = null;
    var $iParameter = 0;

    function __construct($iParameter)
    {
        $this->oDb = $this->getDb();
        $this->oSmarty = $this->getSmarty();
        $this->iParameter = $iParameter;
    }

    public function run()
    {
        return true;
    }

    public function show()
    {
        return $this->oSmarty->fetch(__DIR__ . '/templates/content.i.tpl');
    }
}