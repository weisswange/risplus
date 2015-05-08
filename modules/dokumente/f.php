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
        $bShowForm = false;
        $sSearchErrorInputEmpty = '';
        $sSearchWord = '';
        $iResultCount = 0;
        $aResultData = array();
        $bFilterEinladungen = false;
        $bFilterNiederschriften = false;

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
        elseif (isset($_POST) && sizeof($_POST) > 0)
        {
            $bShowForm = true;
            if (! isset($_POST['s']) || (string) $_POST['s'] == '')
            {
                $sSearchErrorInputEmpty = 'has-error';
            }
            else
            {
                $oFiles = new Files();
                $aResultData = $oFiles->getFilesBySearch($_POST);
                $sSearchWord = $oFiles->getSearchWord();
                $iResultCount = $oFiles->getResultCount();
                $bFilterNiederschriften = $oFiles->isFilterNiederschriftenActive();
                $bFilterEinladungen = $oFiles->isFilterEinladungenActive();
            }
        }
        else
        {
            $bShowForm = true;
        }

        // collect variables
        $this->oSmarty->assign('file_show_form', $bShowForm);
        $this->oSmarty->assign('file', $oFile);
        $this->oSmarty->assign('file_vorlagen', $aVorlagen);
        $this->oSmarty->assign('file_error_empty_parameter', $sFileErrorEmptyParameter);
        $this->oSmarty->assign('file_error_wrong_id', $sFileErrorWrongId);
        $this->oSmarty->assign('search_error_input_empty', $sSearchErrorInputEmpty);
        $this->oSmarty->assign('search_input_s', $sSearchWord);
        $this->oSmarty->assign('search_results_count', $iResultCount);
        $this->oSmarty->assign('search_results_data', $aResultData);
        $this->oSmarty->assign('filter_remove_einladungen', $bFilterEinladungen);
        $this->oSmarty->assign('filter_reduce_niederschriften', $bFilterNiederschriften);

        return true;
    }

    public function show()
    {
        return $this->oSmarty->fetch(__DIR__ . '/templates/content.f.tpl');
    }
}