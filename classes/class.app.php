<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/components/smarty/Smarty.class.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/class.db.php');

class App
{
    // module config
    private $aModuleConfiguration = array();
    private $sModulesPath = 'modules';
    private $sModuleConfigFile = 'mod_conf.php';
    private $sModuleContent = '';
    private $sModuleName = '';

    // GET / POST PARAMETER
    private $iParameter = '';

    // template
    public $oSmarty = null;

    // database
    protected $oDb = null;

    function __construct()
    {
        $this->registerModules();
        $this->setRoute();
        $this->setSmarty();
        $this->oDb = new DB();
    }

    private function registerModules()
    {
        // traverse modules directory
        foreach (new DirectoryIterator($this->sModulesPath) as $oModuleDir)
        {
            if ($oModuleDir->isDir())
            {
                $sModuleConfigFile = $oModuleDir->getRealPath() . '/' . $this->sModuleConfigFile;
                if (is_file($sModuleConfigFile))
                {
                    include_once($sModuleConfigFile);
                }
            }
        }

        return true;
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

        if (! array_key_exists($sModule, $this->aModuleConfiguration))
        {
            return false;
        }

        $this->sModuleName = $sModule;
        return true;
    }

    public function getModule()
    {
        return $this->sModuleName;
    }

    public function run()
    {
        $sClassIncludePath = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->sModulesPath . '/' . $this->getModule() . '/' . $this->aModuleConfiguration[$this->sModuleName]['file'];
        if (! include_once($sClassIncludePath))
        {
            die('Class could not be loaded');
        }

        $sClass = $this->aModuleConfiguration[$this->sModuleName]['object'];

        $o = new $sClass($this->getParameter());
        $o->run();
        $this->sModuleContent = $o->show();

        $fi = new FilesystemIterator($_SERVER['DOCUMENT_ROOT'] . '/downloads', FilesystemIterator::SKIP_DOTS);
        $iStatsFilesCount = iterator_count($fi);

        $sDbSize = $this->oDb->getDatabaseSize();

        // collect variables
        $this->oSmarty->assign('stats_count_files', $iStatsFilesCount);
        $this->oSmarty->assign('stats_count_dbsize', $sDbSize);

        $this->show();
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
        $this->oSmarty->assign('content', $this->sModuleContent);
        $this->oSmarty->display('frame.tpl');
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