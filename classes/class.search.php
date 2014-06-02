<?php

class Search
{
	private $oDb = null;
	private $sSearchWord = '';
	private $iResultCount = 0;
	private $aResultsContent = array();
	private $bFilterEinladungen = false;
    private $bFilterNiederschriften = false;

	function __construct($aSearch)
	{
		$this->setSearchWord($aSearch);
		
		if (isset($aSearch['filter_remove_einladungen']) && (string) $aSearch['filter_remove_einladungen'] == 'true')
		{
			$this->bFilterEinladungen = true;
		}

        if (isset($aSearch['filter_reduce_niederschriften']) && (string) $aSearch['filter_reduce_niederschriften'] == 'true')
        {
            $this->bFilterNiederschriften = true;
        }

		$this->oDb = new DB();
		$this->aResultsContent = $this->oDb->searchFilesContent($this->sSearchWord, $this->bFilterEinladungen, $this->bFilterNiederschriften);
	}

    public function isFilterNiederschriftenActive()
    {
        return $this->bFilterNiederschriften;
    }

    public function isFilterEinladungenActive()
    {
        return $this->bFilterEinladungen;
    }

	protected function setSearchWord($aSearchWord)
	{
        $sSearchWord = $aSearchWord['s'];

		$sSearchWord = filter_var($sSearchWord, FILTER_SANITIZE_STRING);
		$sSearchWord = (string) utf8_decode($sSearchWord);
		
		$this->sSearchWord = $sSearchWord;
		return true;
	}
	
	public function getSearchWord()
	{
		return utf8_encode($this->sSearchWord);
	}
	
	public function getResults() 
	{
		return $this->createResult($this->aResultsContent);
	}
	
	public function getResultCount()
	{
		return sizeof($this->aResultsContent);
	}
	
	private function createResult($aResultsContent)
	{
		$aResultData = array();
		foreach ($aResultsContent as $aItem)
		{
			$sItemName = str_replace('_', ' ', $aItem['filename']);
			$sItemName = preg_replace('/\.\w{3,4}/', '', $sItemName);
			$sItemFileName = $aItem['filename'];

			$aResultSet = array(
				'id' => $aItem['id'],
				'name' => $sItemName,
				'filename' => $sItemFileName,
				'size' => $this->human_filesize(filesize($_SERVER['DOCUMENT_ROOT'] . '/downloads/' . $aItem['filename']), 0),
			);
			
			$aResultData[] = $aResultSet;
		}
		
		return $aResultData;
	}
	
	function human_filesize($bytes, $decimals = 2) 
	{
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}
}