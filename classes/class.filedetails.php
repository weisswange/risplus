<?php

class Filedetails
{
	private $iFileId = 0;
	private $sFileName = '';
	private $oDb = null;
	private $aFileData = array();
	
	function __construct($iId)
	{
		$this->setFileId($iId);
		$this->oDb = new DB();

        if (! $this->aFileData = $this->oDb->getFileData($this->iFileId))
        {
            throw new Exception('file id not valid');
        }

		$this->setFileName($this->aFileData['filename']);

        return true;
	}
	
	private function setFileId($iId)
	{
		$this->iFileId = filter_var($iId, FILTER_SANITIZE_NUMBER_INT);
		
		return true;
	}
	
	public function getFileId()
	{
		return $this->iFileId;
	}
	
	public function setFileName($sFileName)
	{
		$this->sFileName = $sFileName;
	}
	
	public function getFileName()
	{
		return $this->sFileName;
	}	
}