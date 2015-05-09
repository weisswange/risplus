<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/components/smarty/Smarty.class.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/class.db.php');

/**
 * Main risplus class
 *
 * - loads components (smarty, database)
 * - checks for module, loads and executes module
 *
 */
class App
{
    /**
     * @var array   stores module configuration
     */
    private $aModuleConfiguration = array();

    /**
     * @var string  path to modules-directory
     */
    private $sModulesPath = 'modules';

    /**
     * @var string  module config file name
     */
    private $sModuleConfigFile = 'mod_conf.php';

    /**
     * @var string  stores module output
     */
    private $sModuleContent = '';

    /**
     * @var string  stores module name
     */
    private $sModuleName = '';

    /**
     * @var string  stores module parameter
     */
    private $iParameter = '';

    /**
     * @var null    Smarty library object
     */
    public $oSmarty = null;

    /**
     * @var DB|null Database object
     */
    protected $oDb = null;

    /**
     *  Constructor
     *
     * @return \App
     */
    function __construct()
    {
        $this->registerModules();
        $this->setRoute();
        $this->setSmarty();
        $this->oDb = new DB();

        return true;
    }

    /**
     * Checks modules directory and registers all available and valid modules
     *
     * @return bool
     */
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

    /**
     * Creates smarty object
     *
     * @return bool
     */
    private function setSmarty()
    {
        // create template object
        $this->oSmarty = new Smarty;

        return true;
    }

    /**
     * Sets the route for current request
     *
     * @return null|true
     */
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

        return true;
    }

    /**
     * Checks if requested module exists and sets it
     *
     * @param $sModule
     * @return bool
     */
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

    /**
     * returns current module name
     *
     * @return string
     */
    public function getModule()
    {
        return $this->sModuleName;
    }

    /**
     * Runs the application
     *
     * @return bool
     */
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
        $this->oSmarty->assign('content', $this->sModuleContent);

        $fi = new FilesystemIterator($_SERVER['DOCUMENT_ROOT'] . '/downloads', FilesystemIterator::SKIP_DOTS);
        $iStatsFilesCount = iterator_count($fi);

        $sDbSize = $this->oDb->getDatabaseSize();

        // collect variables
        $this->oSmarty->assign('stats_count_files', $iStatsFilesCount);
        $this->oSmarty->assign('stats_count_dbsize', $sDbSize);

        $this->show();

        return true;
    }

    /**
     * Sets request parameter
     *
     * @param $sParameter
     * @return bool
     */
    private function setParameter($sParameter)
    {
        $iParameter = filter_var($sParameter, FILTER_SANITIZE_NUMBER_INT);

        $this->iParameter = $iParameter;
        return true;
    }

    /**
     * Returns current parameter
     *
     * @return string
     */
    public function getParameter()
    {
        return $this->iParameter;
    }

    /**
     * Renders application content
     *
     * @return bool
     */
    public function show()
    {
        $this->oSmarty->display('frame.tpl');
        return true;
    }

    /**
     * Returns database object
     *
     * @return DB|null
     */
    protected function getDb()
    {
        if ($this->oDb == null)
        {
            $this->oDb = new DB();
        }

        return $this->oDb;
    }

    /**
     * Returns smarty object
     *
     * @return Smarty
     */
    protected function getSmarty()
    {
        if ($this->oSmarty == null)
        {
            $this->oSmarty = new Smarty();
        }

        return $this->oSmarty;
    }
}