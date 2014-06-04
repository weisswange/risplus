<?php

class File
{
    private $iId = 0;
    private $sContent = '';
    private $sFilename = '';

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
}