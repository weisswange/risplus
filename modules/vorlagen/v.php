<?php

include_once('classes/class.vorlagen.php');
include_once('classes/class.vorlage.php');

class Vorlageview extends App implements Module
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
        $aFilesForVorlage = array();
        $sVorlageErrorEmptyParameter = '';
        $sVorlageErrorWrongId = '';
        $bShowForm = false;
        $sSearchErrorInputEmpty = '';
        $sSearchWord = '';
        $iResultCount = 0;
        $aResultData = array();
        $oVorlage = null;

        // search
        if (isset($this->iParameter) && $this->iParameter != 0)
        {
            try
            {
                $oVorlagen = new Vorlagen();
                $oVorlage = $oVorlagen->getVorlageById($this->iParameter);

                $aFilesForVorlage = $this->oDb->getFilesForVorlage($oVorlage->getId());
            }
            catch (Exception $e)
            {
                $sVorlageErrorWrongId = 'Keine Vorlage zur übergebenen ID gefunden';
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
                $oVorlagen = new Vorlagen();
                $aResultData = $oVorlagen->getVorlagenBySearch($_POST);
                $sSearchWord = $oVorlagen->getSearchWord();
                $iResultCount = $oVorlagen->getResultCount();
            }
        }
        else
        {
            $bShowForm = true;
        }

        // collect variables
        $this->oSmarty->assign('vorlage', $oVorlage);
        $this->oSmarty->assign('vorlagen_files', $aFilesForVorlage);
        $this->oSmarty->assign('vorlage_error_empty_parameter', $sVorlageErrorEmptyParameter);
        $this->oSmarty->assign('vorlage_error_wrong_id', $sVorlageErrorWrongId);
        $this->oSmarty->assign('vorlage_show_form', $bShowForm);
        $this->oSmarty->assign('search_error_input_empty', $sSearchErrorInputEmpty);
        $this->oSmarty->assign('search_input_s', $sSearchWord);
        $this->oSmarty->assign('search_results_count', $iResultCount);
        $this->oSmarty->assign('search_results_data', $aResultData);

        return true;
    }

    public function show()
    {
        return $this->oSmarty->fetch(__DIR__ . '/templates/content.v.tpl');
    }
}