<?php

include_once('components/smarty/Smarty.class.php');
include_once('classes/class.db.php');

// create object
$smarty = new Smarty;

// template variables
$sFileName = '';
$iFileId = 0;
$aVorlagen = array();
$sFileErrorEmptyParameter = '';
$sFileErrorWrongId = '';

// search
if (isset($_GET) && sizeof($_GET) > 0)
{

	if (! isset($_GET['id']) || (int) $_GET['id'] == 0)
	{
		$sFileErrorEmptyParameter = 'Keine Datei-ID übergeben';
	}
	else
	{
		include('classes/class.filedetails.php');

        try
        {
            $oFile = new Filedetails($_GET['id']);
            $sFileName = $oFile->getFileName();
            $iFileId = $oFile->getFileId();

            $oDb = new DB();
            $aVorlagen = $oDb->getVorlagenForFile($iFileId);
        }
        catch (Exception $e)
        {
            $sFileErrorWrongId = 'Keine Datei zur übergebenen ID gefunden';
        }
	}
}

$fi = new FilesystemIterator(__DIR__ . '/downloads', FilesystemIterator::SKIP_DOTS);
$iStatsFilesCount = iterator_count($fi);

$oDb = new DB();
$sDbSize = $oDb->getDatabaseSize();

// collect variables
$smarty->assign('stats_count_files', $iStatsFilesCount);
$smarty->assign('stats_count_dbsize', $sDbSize);
$smarty->assign('file_details_name', $sFileName);
$smarty->assign('file_details_id', $iFileId);
$smarty->assign('file_vorlagen', $aVorlagen);
$smarty->assign('file_error_empty_parameter', $sFileErrorEmptyParameter);
$smarty->assign('file_error_wrong_id', $sFileErrorWrongId);

// display it
$smarty->display('frame.f.tpl');