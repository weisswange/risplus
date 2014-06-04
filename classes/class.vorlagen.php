<?php

class Vorlagen
{
    private $oDb = null;
    private $sSearchWord = '';
    private $iResultCount = 0;

    function __construct()
   	{
   		$this->oDb = new DB();
   	}

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

    private function setSearchWord($aSearchWord)
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

    private function setResultCount($iResultCount)
    {
        $this->iResultCount = $iResultCount;
    }

   	public function getResultCount()
   	{
   		return $this->iResultCount;
   	}

    public function getVorlageById($iId)
    {
        if (! $aVorlage = $this->oDb->getVorlageById($iId))
        {
            throw new Exception('vorlage id not valid');
        }

        return $this->getVorlageObject($aVorlage);
    }

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