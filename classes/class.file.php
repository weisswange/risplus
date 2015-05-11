<?php

/**
 *  Represents a single file
 */
class File
{
    /**
     * @var int file id
     */
    private $iId = 0;
    /**
     * @var string  file content
     */
    private $sContent = '';
    /**
     * @var string  filename
     */
    private $sFilename = '';
    /**
     * @var int     filesize
     */
    private $sFilesize = 0;
    /**
     * @var int     score for searchstring
     */
    private $iScore = 0;

    /**
     * Constructor
     *
     * @return bool
     */
    public function _construct()
    {
        return true;
    }

    /**
     * Sets file id
     *
     * @param int $iId
     */
    public function setId($iId)
    {
        $this->iId = $iId;
    }

    /**
     * Returns file id
     *
     * @return int
     */
    public function getId()
    {
        return $this->iId;
    }

    /**
     * Sets file content
     *
     * @param string $sContent
     */
    public function setContent($sContent)
    {
        $this->sContent = $sContent;
    }

    /**
     * Returns file content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->sContent;
    }

    /**
     * Sets file name
     *
     * @param string $sFilename
     */
    public function setFilename($sFilename)
    {
        $this->sFilename = $sFilename;
    }

    /**
     * Returns file name
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->sFilename;
    }

    /**
     * Sets file size
     *
     * @param $sFilename
     */
    public function setSize($sFilename)
    {
        $this->sFilesize = $this->formatFilesize(filesize($_SERVER['DOCUMENT_ROOT'] . '/downloads/' . $sFilename), 0);
    }

    /**
     * Returns file size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->sFilesize;
    }

    /**
     * Creates filesize as formated string
     *
     * @param $iBytes
     * @param int $iDecimals
     * @return string
     */
    private function formatFilesize($iBytes, $iDecimals = 2)
   	{
   		$sAvailableUnits = 'BKMGTP';
   		$iUnitFactor = (int) floor((strlen($iBytes) - 1) / 3);
   		return sprintf("%.{$iDecimals}f", $iBytes / pow(1024, $iUnitFactor)) . @$sAvailableUnits[$iUnitFactor];
   	}

    /**
     * Sets file content score
     *
     * @param $sScore
     */
    public function setScore($iScore)
    {
        $this->iScore = round($iScore, 2);
    }

    /**
     * Returns file content score
     *
     * @return int
     */
    public function getScore()
    {
        return $this->iScore;
    }
}