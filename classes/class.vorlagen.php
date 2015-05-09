<?php

/**
 *  Methods for vorlagen
 */
class Vorlagen
{
    /**
     * @var DB|null database object
     */
    private $oDb = null;
    /**
     * @var string  search word
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
   	}

    /**
     * Returns vorlagen objects for a search
     *
     * @param $aSearch
     * @return array
     */
    public function getVorlagenBySearch($aSearch)
    {
        $this->setSearchWord($aSearch);
        $aResultsRaw = $this->oDb->searchVorlagen($this->sSearchWord);
        $aResults = array();

        $iResultCount = 0;
        foreach ($aResultsRaw as $aVorlage)
        {
            $aResults[] = $this->getVorlageObject($aVorlage);
            $iResultCount++;
        }

        $this->setResultCount($iResultCount);

        return $aResults;
    }

    /**
     * Sets search word
     *
     * @param $aSearchWord
     * @return bool
     */
    private function setSearchWord($aSearchWord)
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
     * Sets result count
     *
     * @param $iResultCount
     */
    private function setResultCount($iResultCount)
    {
        $this->iResultCount = $iResultCount;
    }

    /**
     * Returns result count
     *
     * @return int
     */
    public function getResultCount()
   	{
   		return $this->iResultCount;
   	}

    /**
     * Returns a vorlage object for id
     *
     * @param $iId
     * @return Vorlage
     * @throws Exception
     */
    public function getVorlageById($iId)
    {
        if (! $aVorlage = $this->oDb->getVorlageById($iId))
        {
            throw new Exception('vorlage id not valid');
        }

        return $this->getVorlageObject($aVorlage);
    }

    /**
     * Creates a vorlage object
     *
     * @param $aVorlage
     * @return Vorlage
     */
    private function getVorlageObject($aVorlage)
    {
        $oVorlage = new Vorlage();

        $oVorlage->setId($aVorlage['id']);
        $oVorlage->setDate($aVorlage['date']);
        $oVorlage->setName($aVorlage['name']);
        $oVorlage->setSubject($aVorlage['subject']);
        $oVorlage->setType($aVorlage['type']);

        return $oVorlage;
    }
}