<?php

/**
 *  Methods for files
 */
class Files
{
    /**
     * @var int file id
     */
    private $iFileId = 0;

    /**
     * @var DB|null database object
     */
    private $oDb = null;

    /**
     * @var bool    filter for einladungen
     */
    private $bFilterEinladungen = false;

    /**
     * @var bool    filter for niederschriften
     */
    private $bFilterNiederschriften = false;

    /**
     * @var string  search string
     */
    private $sSearchWord = '';

    /**
     * @var int     counter for datasets
     */
    private $iResultCount = 0;

    /**
     *  Constructor
     */
    function __construct()
	{
		$this->oDb = new DB();

        return true;
	}

    /**
     * Returns file object
     *
     * @param $iId
     * @return File
     * @throws Exception
     */
    public function getFileById($iId)
    {
        $this->setFileId($iId);

        if (! $aFile = $this->oDb->getFileById($this->iFileId))
        {
            throw new Exception('file id not valid');
        }

		return $this->getFileObject($aFile);
    }

    /**
     * Returns all files for a search as objects
     *
     * @param $aSearch
     * @return array
     */
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

    /**
     * Sets niederschriften filter
     *
     * @return bool
     */
    public function isFilterNiederschriftenActive()
    {
        return $this->bFilterNiederschriften;
    }

    /**
     * Sets einladungen filter
     *
     * @return bool
     */
    public function isFilterEinladungenActive()
    {
        return $this->bFilterEinladungen;
    }

    /**
     * Sets search word
     *
     * @param $aSearchWord
     * @return bool
     */
    protected function setSearchWord($aSearchWord)
	{
        $sSearchWord = $aSearchWord['s'];

		$sSearchWord = filter_var($sSearchWord, FILTER_SANITIZE_STRING);
		$sSearchWord = (string) utf8_decode($sSearchWord);

		$this->sSearchWord = $sSearchWord;
		return true;
	}

    /**
     * Returns search word
     *
     * @return string
     */
    public function getSearchWord()
	{
		return utf8_encode($this->sSearchWord);
	}

    /**
     * Sets id for a file
     *
     * @param $iId
     * @return bool
     */
    private function setFileId($iId)
	{
		$this->iFileId = filter_var($iId, FILTER_SANITIZE_NUMBER_INT);
		
		return true;
	}

    /**
     * Sets dataset counter
     *
     * @param $iResultCount
     */
    private function setResultCount($iResultCount)
    {
        $this->iResultCount = $iResultCount;
    }

    /**
     * Returns dataset counter
     *
     * @return int
     */
    public function getResultCount()
   	{
   		return $this->iResultCount;
   	}

    /**
     * returns a file object
     *
     * @param $aFile
     * @return File
     */
    private function getFileObject($aFile)
    {
        $oFile = new File();

        $oFile->setId($aFile['id']);
        $oFile->setContent($aFile['content']);
        $oFile->setFilename($aFile['filename']);
        $oFile->setSize($aFile['filename']);

        if (isset($aFile['score']))
        {
            $oFile->setScore($aFile['score']);
        }
        else
        {
            $oFile->setScore(0);
        }

        return $oFile;
    }

}