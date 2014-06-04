<?php
/**
 * Created by JetBrains PhpStorm.
 * User: stewei
 * Date: 04.06.14 (23)
 * Time: 15:49
 * To change this template use File | Settings | File Templates.
 */

class Vorlage
{
    private $iId = 0;
    private $iDate = 0;
    private $sName = '';
    private $sType = '';
    private $sSubject = '';

    public function _construct()
    {
        return true;
    }

    /**
     * @param int $iDate
     */
    public function setDate($iDate)
    {
        $this->iDate = $iDate;
    }

    /**
     * @return int
     */
    public function getDate()
    {
        return $this->iDate;
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
     * @param string $sName
     */
    public function setName($sName)
    {
        $this->sName = $sName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->sName;
    }

    /**
     * @param string $sSubject
     */
    public function setSubject($sSubject)
    {
        $this->sSubject = utf8_encode($sSubject);
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->sSubject;
    }

    /**
     * @param string $sType
     */
    public function setType($sType)
    {
        $this->sType = utf8_encode($sType);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->sType;
    }
}