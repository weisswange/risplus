<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/components/smarty/Smarty.class.php');

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
     * @var string  stores module parameter
     */
    private $iParameter = '';

    /**
     * @var Smarty|null  Smarty library object
     */
    public $oSmarty = null;

    /**
     * @var DB|null Database object
     */
    protected $oDb = null;

    /**
     * @var ModuleHandler|null ModuleHandler object
     */
    private $oMod = null;

    /**
     *  Constructor
     *
     * @return \App
     */
    function __construct()
    {
        $this->oDb = new DB();
        $this->oMod = new ModuleHandler();

        $this->setRoute();
        $this->setSmarty();

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
            $this->oMod->setModule('index');
            return true;
        }

        $aRoute = explode('/', $sRoute);

        if (! isset($aRoute[1]) && $aRoute[1] == '')
        {
            return null;
        }

        if (! $this->oMod->setModule($aRoute[1]))
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
     * Runs the application
     *
     * @return bool
     */
    public function run()
    {
        // run current module
        $this->oMod->executeCurrentModule($this->getParameter());

        $fi = new FilesystemIterator($_SERVER['DOCUMENT_ROOT'] . '/downloads', FilesystemIterator::SKIP_DOTS);
        $iStatsFilesCount = iterator_count($fi);

        $sDbSize = $this->oDb->getDatabaseSize();

        // collect variables
        $this->oSmarty->assign('content', $this->oMod->getModuleContent());
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
        // collect variables
        $this->oSmarty->assign('subtitle', $this->oMod->getModuleTitle());
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