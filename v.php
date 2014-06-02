<?php

include_once('components/smarty/Smarty.class.php');
include_once('classes/class.db.php');
include_once('classes/class.vorlagen.php');

// create object
$smarty = new Smarty;

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
if (isset($_GET) && sizeof($_GET) > 0)
{

	if (! isset($_GET['id']) || (int) $_GET['id'] == 0)
	{
        $sVorlageErrorEmptyParameter = 'Keine Vorlage-ID übergeben';
	}
	else
	{
		include('classes/class.vorlagedetails.php');

        try
        {
            $oVorlage = new Vorlagedetails($_GET['id']);
            $sVorlageName = $oVorlage->getVorlageName();
            $iVorlageId = $oVorlage->getVorlageId();
            $sVorlageSubject = $oVorlage->getVorlageSubject();
            $iVorlageDate = $oVorlage->getVorlageDate();

            $oDb = new DB();
            $aVorlagen = $oDb->getFilesForVorlage($iVorlageId);
        }
        catch (Exception $e)
        {
            $sVorlageErrorWrongId = 'Keine Vorlage zur übergebenen ID gefunden';
        }
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

$oDb = new DB();
$sDbSize = $oDb->getDatabaseSize();

// collect variables
$smarty->assign('stats_count_files', $iStatsFilesCount);
$smarty->assign('stats_count_dbsize', $sDbSize);
$smarty->assign('vorlage_details_name', $sVorlageName);
$smarty->assign('vorlage_details_id', $iVorlageId);
$smarty->assign('vorlage_details_date', $iVorlageDate);
$smarty->assign('vorlage_details_subject', $sVorlageSubject);
$smarty->assign('vorlagen_files', $aVorlagen);
$smarty->assign('vorlage_error_empty_parameter', $sVorlageErrorEmptyParameter);
$smarty->assign('vorlage_error_wrong_id', $sVorlageErrorWrongId);
$smarty->assign('vorlage_show_form', $sShowForm);
$smarty->assign('search_error_input_empty', $sSearchErrorInputEmpty);
$smarty->assign('search_input_s', $sSearchWord);
$smarty->assign('search_results_count', $iResultCount);
$smarty->assign('search_results_data', $aResultData);

// display it
$smarty->display('frame.v.tpl');