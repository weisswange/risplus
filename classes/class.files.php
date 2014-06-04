<?php

class Files
{
	private $iFileId = 0;
	private $sFileName = '';
	private $oDb = null;

	function __construct()
	{
		$this->oDb = new DB();

        return true;
	}

    public function getFileById($iId)
    {
        $this->setFileId($iId);

        if (! $aFile = $this->oDb->getFileData($this->iFileId))
        {
            throw new Exception('file id not valid');
        }

		return $this->getFileObject($aFile);
    }
	
	private function setFileId($iId)
	{
		$this->iFileId = filter_var($iId, FILTER_SANITIZE_NUMBER_INT);
		
		return true;
	}
	
    private function getFileObject($aFile)
    {
        $oFile = new File();

        $oFile->setId($aFile['id']);
        $oFile->setContent($aFile['content']);
        $oFile->setFilename($aFile['filename']);

        return $oFile;
    }

}