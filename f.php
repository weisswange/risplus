<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/class.files.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/class.file.php');

class Dokumentview extends App
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
        // template variables
        $oFile = null;
        $aVorlagen = array();
        $sFileErrorEmptyParameter = '';
        $sFileErrorWrongId = '';

        // search
        if (isset($this->iParameter) && $this->iParameter != 0)
        {
            try
            {
                $oFiles = new Files();
                $oFile = $oFiles->getFileById($this->iParameter);

                $aVorlagen = $this->oDb->getVorlagenForFile($oFile->getId());
            }
            catch (Exception $e)
            {
                $sFileErrorWrongId = 'Keine Datei zur übergebenen ID gefunden';
            }
        }

        $fi = new FilesystemIterator(__DIR__ . '/downloads', FilesystemIterator::SKIP_DOTS);
        $iStatsFilesCount = iterator_count($fi);

        $sDbSize = $this->oDb->getDatabaseSize();

        // collect variables
        $this->oSmarty->assign('stats_count_files', $iStatsFilesCount);
        $this->oSmarty->assign('stats_count_dbsize', $sDbSize);
        $this->oSmarty->assign('file', $oFile);
        $this->oSmarty->assign('file_vorlagen', $aVorlagen);
        $this->oSmarty->assign('file_error_empty_parameter', $sFileErrorEmptyParameter);
        $this->oSmarty->assign('file_error_wrong_id', $sFileErrorWrongId);

        return true;
    }

    public function show()
    {
        $this->oSmarty->display('frame.f.tpl');
    }
}