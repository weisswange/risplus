<?php

// include all required stuff
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/class.app.php');

// start app
$me = new App();
$me->run();


// template variables
/*$aResultData = array();
$sSearchErrorInputEmpty = '';
$iResultCount = 0;
$sSearchWord = '';
$bFilterEinladungen = false;
$bFilterNiederschriften = false;

// search
if (isset($_POST) && sizeof($_POST) > 0)
{
	if (! isset($_POST['s']) || (string) $_POST['s'] == '')
	{
		$sSearchErrorInputEmpty = 'has-error';
	}
	else
	{
		include('classes/class.search.php');

		$oSearch = new Search($_POST);

		$iResultCount = $oSearch->getResultCount();
		$aResultData = $oSearch->getResults();
		$sSearchWord = $oSearch->getSearchWord();
        $bFilterEinladungen = $oSearch->isFilterEinladungenActive();
        $bFilterNiederschriften = $oSearch->isFilterNiederschriftenActive();
	}	
}

$fi = new FilesystemIterator(__DIR__ . '/downloads', FilesystemIterator::SKIP_DOTS);
$iStatsFilesCount = iterator_count($fi);

$oDb = new DB();
$sDbSize = $oDb->getDatabaseSize();

// collect variables
$smarty->assign('stats_count_files', $iStatsFilesCount);
$smarty->assign('stats_count_dbsize', $sDbSize);
$smarty->assign('search_input_s', $sSearchWord);
$smarty->assign('filter_remove_einladungen', $bFilterEinladungen);
$smarty->assign('filter_reduce_niederschriften', $bFilterNiederschriften);
$smarty->assign('search_results_count', $iResultCount);
$smarty->assign('search_results_data', $aResultData);
$smarty->assign('search_error_input_empty', $sSearchErrorInputEmpty);
*/
// display it
