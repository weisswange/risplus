<?php

class File
{
    private $iId = 0;
    private $sContent = '';
    private $sFilename = '';
    private $sFilesize = 0;

    public function _construct()
    {
        return true;
    }

    /**
     * @param int $iId
     */
    public function setId($iId)
    {
        $this->iId = $iId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->iId;
    }

    /**
     * @param string $sContent
     */
    public function setContent($sContent)
    {
        $this->sContent = $sContent;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->sContent;
    }

    /**
     * @param string $sFilename
     */
    public function setFilename($sFilename)
    {
        $this->sFilename = $sFilename;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->sFilename;
    }

    public function setSize($sFilename)
    {
        $this->sFilesize = $this->human_filesize(filesize($_SERVER['DOCUMENT_ROOT'] . '/downloads/' . $sFilename), 0);
    }

    public function getSize()
    {
        return $this->sFilesize;
    }

    private function human_filesize($bytes, $decimals = 2)
   	{
   		$sz = 'BKMGTP';
   		$factor = floor((strlen($bytes) - 1) / 3);
   		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
   	}

}