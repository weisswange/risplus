<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/i.module.php');

/**
 * Path for modules directory
 */
define('MODULE_PATH', 'modules');

/**
 * Name of module config file
 */
define('MODULE_CONFIG_FILE', 'mod_conf.php');

/**
 * ModuleHandler methods
 */
class ModuleHandler
{
    /**
     * @var object  Current module
     */
    private $o;

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
            if (! $oModuleDir->isDir())
            {
                continue;
            }

            $sModuleConfigFile = $oModuleDir->getRealPath() . '/' . MODULE_CONFIG_FILE;
            if (! is_file($sModuleConfigFile))
            {
                continue;
            }

            include_once($sModuleConfigFile);
        }

        return true;
    }

    public function executeCurrentModule($iParameter)
    {
        $sClassIncludePath = $this->getModuleFilePath();
        if (! include_once($sClassIncludePath))
        {
            die('Class could not be loaded');
        }

        $sClass = $this->getModuleClass();

        $this->o = new $sClass($iParameter);

        // check if module implements interface
        $aClassInterfaces = class_implements($this->o);

        if (! array_key_exists('Module', $aClassInterfaces))
        {
            die($this->getModuleFilePath() . ' is missing the Module interface');
        }

        $this->o->run();
        $this->setModuleContent();

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
    private function getModuleFilePath()
    {
        return $_SERVER['DOCUMENT_ROOT'] . '/' . MODULE_PATH . '/' . $this->getModule() . '/' . $this->aModuleConfiguration[$this->sModuleName]['file'];
    }

    /**
     * Returns ModuleHandler Class Name
     *
     * @return string
     */
    private function getModuleClass()
    {
        return $this->aModuleConfiguration[$this->sModuleName]['object'];
    }

    /**
     * Sets module content
     *
     * @param $sContent
     * @return bool
     */
    public function setModuleContent()
    {
        $this->sModuleContent = $this->o->show();
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