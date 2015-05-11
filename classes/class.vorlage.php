<?php

/**
 *  Represents a vorlage
 */
class Vorlage
{
    /**
     * @var int     vorlage id
     */
    private $iId = 0;
    /**
     * @var int     vorlage data
     */
    private $iDate = 0;
    /**
     * @var string  vorlage name
     */
    private $sName = '';
    /**
     * @var string  vorlage type
     */
    private $sType = '';
    /**
     * @var string  vorlage subject
     */
    private $sSubject = '';

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
     * Sets date for vorlage
     *
     * @param int $iDate
     */
    public function setDate($iDate)
    {
        $this->iDate = $iDate;
    }

    /**
     * Returns date for vorlage
     *
     * @return int
     */
    public function getDate()
    {
        return $this->iDate;
    }

    /**
     * Sets vorlage id
     *
     * @param int $iId
     */
    public function setId($iId)
    {
        $this->iId = $iId;
    }

    /**
     * Returns vorlage id
     *
     * @return int
     */
    public function getId()
    {
        return $this->iId;
    }

    /**
     * Sets vorlage name
     *
     * @param string $sName
     */
    public function setName($sName)
    {
        $this->sName = $sName;
    }

    /**
     * Returns vorlage name
     *
     * @return string
     */
    public function getName()
    {
        return $this->sName;
    }

    /**
     * Sets vorlage subject
     *
     * @param string $sSubject
     */
    public function setSubject($sSubject)
    {
        $this->sSubject = utf8_encode($sSubject);
    }

    /**
     * Returns vorlage subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->sSubject;
    }

    /**
     * Sets vorlage type
     *
     * @param string $sType
     */
    public function setType($sType)
    {
        $this->sType = utf8_encode($sType);
    }

    /**
     * Returns vorlage type
     *
     * @return string
     */
    public function getType()
    {
        return $this->sType;
    }
}