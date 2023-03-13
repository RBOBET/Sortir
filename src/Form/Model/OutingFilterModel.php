<?php

namespace App\Form\Model;

use App\Entity\Campus;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Date;

class OutingFilterModel
{

    private Campus $campus;
    private string $nameContains;
    private ?\DateTime $startDate;

    #[Assert\GreaterThanOrEqual(propertyPath: 'startDate', message: 'Cette date ne peut pas être postérieure à la date de début de recherche')]
    private ?\DateTime $endDate;
    private bool $isPlanner;
    private bool $isRegistered;
    private bool $isNotRegistered;
    private bool $outingIsPast;

    /**
     * @return Campus
     */
    public function getCampus(): Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus $campus
     * @return OutingFilterModel
     */
    public function setCampus(Campus $campus): OutingFilterModel
    {
        $this->campus = $campus;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameContains(): string
    {
        return $this->nameContains;
    }

    /**
     * @param string $nameContains
     * @return OutingFilterModel
     */
    public function setNameContains(string $nameContains): OutingFilterModel
    {
        $this->nameContains = $nameContains;
        return $this;
    }

    /**
     * @return Date
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param Date $startDate
     * @return OutingFilterModel
     */
    public function setStartDate(?\DateTime $startDate): OutingFilterModel
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return Date
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param Date $endDate
     * @return OutingFilterModel
     */
    public function setEndDate(?\DateTime $endDate): OutingFilterModel
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPlanner(): bool
    {
        return $this->isPlanner;
    }

    /**
     * @param bool $isPlanner
     * @return OutingFilterModel
     */
    public function setIsPlanner(bool $isPlanner): OutingFilterModel
    {
        $this->isPlanner = $isPlanner;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRegistered(): bool
    {
        return $this->isRegistered;
    }

    /**
     * @param bool $isRegistered
     * @return OutingFilterModel
     */
    public function setIsRegistered(bool $isRegistered): OutingFilterModel
    {
        $this->isRegistered = $isRegistered;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNotRegistered(): bool
    {
        return $this->isNotRegistered;
    }

    /**
     * @param bool $isNotRegistered
     * @return OutingFilterModel
     */
    public function setIsNotRegistered(bool $isNotRegistered): OutingFilterModel
    {
        $this->isNotRegistered = $isNotRegistered;
        return $this;
    }

    /**
     * @return bool
     */
    public function isOutingIsPast(): bool
    {
        return $this->outingIsPast;
    }

    /**
     * @param bool $outingIsPast
     * @return OutingFilterModel
     */
    public function setOutingIsPast(bool $outingIsPast): OutingFilterModel
    {
        $this->outingIsPast = $outingIsPast;
        return $this;
    }


}