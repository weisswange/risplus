<?php

class Vorlagen
{
    private $oDb = null;
    private $aResultsContent = array();
    private $sSearchWord = '';

    function __construct($aSearch)
   	{
   		$this->setSearchWord($aSearch);

   		$this->oDb = new DB();
   		$this->aResultsContent = $this->oDb->searchVorlagen($this->sSearchWord);
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
        return $this->aResultsContent;
   	}

   	public function getResultCount()
   	{
   		return sizeof($this->aResultsContent);
   	}
}