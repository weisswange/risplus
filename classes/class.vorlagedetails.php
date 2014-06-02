<?php

class Vorlagedetails
{
	private $iVorlageId = 0;
	private $sVorlageName = '';
    private $sVorlageSubject = '';
    private $iVorlageDate = 0;

	private $oDb = null;
	private $aVorlageData = array();

	function __construct($iId = null)
	{
		$this->setVorlageId($iId);
		$this->oDb = new DB();

        if ($iId === null)
        {
            throw new Exception('vorlage id not given');
        }

        if (! $this->aVorlageData = $this->oDb->getVorlageData($this->iVorlageId))
        {
            throw new Exception('vorlage id not valid');
        }

		$this->setVorlageName($this->aVorlageData['name']);
        $this->setVorlageSubject($this->aVorlageData['subject']);
        $this->setVorlageDate($this->aVorlageData['date']);

        return true;
	}

	private function setVorlageId($iId)
	{
		$this->iVorlageId = filter_var($iId, FILTER_SANITIZE_NUMBER_INT);

		return true;
	}

	public function getVorlageId()
	{
		return $this->iVorlageId;
	}

	private function setVorlageName($sVorlageName)
	{
		$this->sVorlageName= $sVorlageName;

        return true;
	}

	public function getVorlageName()
	{
		return $this->sVorlageName;
	}

    private function setVorlageSubject($sSubject)
    {
        $this->sVorlageSubject = $sSubject;

        return true;
    }

    public function getVorlageSubject()
    {
        return $this->sVorlageSubject;
    }

    private function setVorlageDate($iDate)
    {
        $this->iVorlageDate = $iDate;

        return true;
    }

    public function getVorlageDate()
    {
        return $this->iVorlageDate;
    }
}