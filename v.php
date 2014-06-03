<?php

include_once('classes/class.vorlagen.php');

class Vorlage extends App
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
        $sVorlageName = '';
        $iVorlageId = 0;
        $sVorlageSubject = '';
        $iVorlageDate = '';
        $aVorlagen = array();
        $sVorlageErrorEmptyParameter = '';
        $sVorlageErrorWrongId = '';
        $sShowForm = false;
        $sSearchErrorInputEmpty = '';
        $sSearchWord = '';
        $iResultCount = 0;
        $aResultData = array();

        // search
        if (isset($this->iParameter) && $this->iParameter != 0)
        {
            include('classes/class.vorlagedetails.php');

            try
            {
                $oVorlage = new Vorlagedetails($this->iParameter);
                $sVorlageName = $oVorlage->getVorlageName();
                $iVorlageId = $oVorlage->getVorlageId();
                $sVorlageSubject = $oVorlage->getVorlageSubject();
                $iVorlageDate = $oVorlage->getVorlageDate();

                $aVorlagen = $this->oDb->getFilesForVorlage($iVorlageId);
            }
            catch (Exception $e)
            {
                $sVorlageErrorWrongId = 'Keine Vorlage zur übergebenen ID gefunden';
            }
        }
        elseif (isset($_POST) && sizeof($_POST) > 0)
        {
            $sShowForm = true;
            if (! isset($_POST['s']) || (string) $_POST['s'] == '')
            {
                $sSearchErrorInputEmpty = 'has-error';
            }
            else
            {
                include('classes/class.search.php');

                $oVorlagen = new Vorlagen($_POST);

                $iResultCount = $oVorlagen->getResultCount();
                $aResultData = $oVorlagen->getResults();
                $sSearchWord = $oVorlagen->getSearchWord();
            }
        }
        else
        {
            $sShowForm = true;
        }

        $fi = new FilesystemIterator(__DIR__ . '/downloads', FilesystemIterator::SKIP_DOTS);
        $iStatsFilesCount = iterator_count($fi);

        $sDbSize = $this->oDb->getDatabaseSize();

        // collect variables
        $this->oSmarty->assign('stats_count_files', $iStatsFilesCount);
        $this->oSmarty->assign('stats_count_dbsize', $sDbSize);
        $this->oSmarty->assign('vorlage_details_name', $sVorlageName);
        $this->oSmarty->assign('vorlage_details_id', $iVorlageId);
        $this->oSmarty->assign('vorlage_details_date', $iVorlageDate);
        $this->oSmarty->assign('vorlage_details_subject', $sVorlageSubject);
        $this->oSmarty->assign('vorlagen_files', $aVorlagen);
        $this->oSmarty->assign('vorlage_error_empty_parameter', $sVorlageErrorEmptyParameter);
        $this->oSmarty->assign('vorlage_error_wrong_id', $sVorlageErrorWrongId);
        $this->oSmarty->assign('vorlage_show_form', $sShowForm);
        $this->oSmarty->assign('search_error_input_empty', $sSearchErrorInputEmpty);
        $this->oSmarty->assign('search_input_s', $sSearchWord);
        $this->oSmarty->assign('search_results_count', $iResultCount);
        $this->oSmarty->assign('search_results_data', $aResultData);

        return true;
    }

    public function show()
    {
        $this->oSmarty->display('frame.v.tpl');
    }
}