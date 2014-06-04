<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/components/smarty/Smarty.class.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/class.db.php');

class App
{
    private $sModule = '';
    private $iParameter = '';
    private $aValidRoutes = array(
        'vorlagen' => array('file' => 'v.php', 'object' => 'Vorlage'),
        'dokumente' => array('file' => 'f.php', 'object' => 'Dokument'),
        'index' => array('file' => 'i.php', 'object' => 'Index'),
    );

    public $oSmarty = null;

    protected $oDb = null;

    function __construct()
    {
        $this->setRoute();
        $this->setSmarty();
        $this->oDb = new DB();
    }

    private function setSmarty()
    {
        // create template object
        $this->oSmarty = new Smarty;

        return true;
    }

    private function setRoute()
    {
        $sRoute = $_SERVER['REQUEST_URI'];

        // first page
        if ($sRoute == '' || $sRoute == '/')
        {
            $sRoute = '/index/';
        }

        $aRoute = explode('/', $sRoute);

        if (! isset($aRoute[1]) && $aRoute[1] == '')
        {
            return null;
        }

        if (! $this->setModule($aRoute[1]))
        {
            return null;
        }

        if (isset($aRoute[2]) && $aRoute[2] != '')
        {
            $this->setParameter($aRoute[2]);
        }
    }

    private function setModule($sModule)
    {
        $sModule = filter_var($sModule, FILTER_SANITIZE_STRING);

        if (! array_key_exists($sModule, $this->aValidRoutes))
        {
            return false;
        }

        $this->sModule = $sModule;
        return true;
    }

    public function getModule()
    {
        return $this->sModule;
    }

    public function run()
    {
        include($this->aValidRoutes[$this->sModule]['file']);

        $sClass = $this->aValidRoutes[$this->sModule]['object'];
        $o = new $sClass($this->getParameter());
        $o->run();
        $o->show();
    }

    private function setParameter($sParameter)
    {
        $iParameter = filter_var($sParameter, FILTER_SANITIZE_NUMBER_INT);

        $this->iParameter = $iParameter;
        return true;
    }

    public function getParameter()
    {
        return $this->iParameter;
    }

    public function show()
    {
        //$this->oSmarty->display('frame.s.tpl');
    }

    protected function getDb()
    {
        if ($this->oDb == null)
        {
            $this->oDb = new DB();
        }

        return $this->oDb;
    }

    protected function getSmarty()
    {
        if ($this->oSmarty == null)
        {
            $this->oSmarty = new Smarty();
        }

        return $this->oSmarty;
    }
}