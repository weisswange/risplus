<?php

class Files
{
	private $iFileId = 0;
	private $oDb = null;
    private $bFilterEinladungen = false;
    private $bFilterNiederschriften = false;
    private $sSearchWord = '';
    private $iResultCount = 0;

	function __construct()
	{
		$this->oDb = new DB();

        return true;
	}

    public function getFileById($iId)
    {
        $this->setFileId($iId);

        if (! $aFile = $this->oDb->getFileById($this->iFileId))
        {
            throw new Exception('file id not valid');
        }

		return $this->getFileObject($aFile);
    }

    public function getFilesBySearch($aSearch)
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

        $aResultsRaw = $this->oDb->searchFilesContent($this->sSearchWord, $this->bFilterEinladungen, $this->bFilterNiederschriften);
        $aResults = array();

        $iResultCount = 0;
        foreach ($aResultsRaw as $aFile)
        {
            $aResults[] = $this->getFileObject($aFile);
            $iResultCount++;
        }

        $this->setResultCount($iResultCount);

        return $aResults;
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

	private function setFileId($iId)
	{
		$this->iFileId = filter_var($iId, FILTER_SANITIZE_NUMBER_INT);
		
		return true;
	}

    private function setResultCount($iResultCount)
    {
        $this->iResultCount = $iResultCount;
    }

   	public function getResultCount()
   	{
   		return $this->iResultCount;
   	}

    private function getFileObject($aFile)
    {
        $oFile = new File();

        $oFile->setId($aFile['id']);
        $oFile->setContent($aFile['content']);
        $oFile->setFilename($aFile['filename']);
        $oFile->setSize($aFile['filename']);
        $oFile->setScore($aFile['score']);

        return $oFile;
    }

}