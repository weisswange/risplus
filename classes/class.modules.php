<?php

/**
 * Path for modules directory
 */
define('MODULE_PATH', 'modules');

/**
 * Name of module config file
 */
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

    /**
     * Constructor
     */
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

    /**
     * Returns path to module core file
     *
     * @return string
     */
    public function getModuleFilePath()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/' . MODULE_PATH . '/' . $this->getModule() . '/' . $this->aModuleConfiguration[$this->sModuleName]['file'];
    }

    /**
     * Returns Module Class Name
     *
     * @return string
     */
    public function getModuleClass()
    {
        return $this->aModuleConfiguration[$this->sModuleName]['object'];
    }

    /**
     * Sets module content
     *
     * @param $sContent
     * @return bool
     */
    public function setModuleContent($sContent)
    {
        $this->sModuleContent = $sContent;
        return true;
    }

    /**
     * returns module content
     *
     * @return string
     */
    public function getModuleContent()
    {
        return $this->sModuleContent;
    }
}