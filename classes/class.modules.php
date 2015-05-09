<?php

define('MODULE_PATH', 'modules');
define('MODULE_CONFIG_FILE', 'mod_conf.php');

/**
 * Module methods
 */
class Module
{
    /**
     * @var array   stores module configuration
     */
    private $aModuleConfiguration = array();

    /**
     * @var string  stores module output
     */
    private $sModuleContent = '';

    /**
     * @var string  stores module name
     */
    private $sModuleName = '';

    function __construct()
    {
        $this->aModuleConfiguration = array();
        $this->registerModules();
    }

    /**
     * Checks modules directory and registers all available and valid modules
     *
     * @return bool
     */
    private function registerModules()
    {
        // traverse modules directory
        foreach (new DirectoryIterator(MODULE_PATH) as $oModuleDir)
        {
            if ($oModuleDir->isDir())
            {
                $sModuleConfigFile = $oModuleDir->getRealPath() . '/' . MODULE_CONFIG_FILE;
                if (is_file($sModuleConfigFile))
                {
                    include_once($sModuleConfigFile);
                }
            }
        }

        return true;
    }

    /**
     * Checks if requested module exists and sets it
     *
     * @param $sModule
     * @return bool
     */
    public function setModule($sModule)
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

    public function getModuleFilePath()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/' . MODULE_PATH . '/' . $this->getModule() . '/' . $this->aModuleConfiguration[$this->sModuleName]['file'];
    }

    public function getModuleClass()
    {
        return $this->aModuleConfiguration[$this->sModuleName]['object'];
    }

    public function setModuleContent($sContent)
    {
        $this->sModuleContent = $sContent;
        return true;
    }

    public function getModuleContent()
    {
        return $this->sModuleContent;
    }
}