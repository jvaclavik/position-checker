<?php

namespace Drosera\PositionCheckerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Drosera\PositionCheckerBundle\Entity\Positions
 *
 * @ORM\Table(name="positions")
 * @ORM\Entity
 */
class Positions
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var date $date
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var integer $position
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * @var Search
     *
     * @ORM\ManyToOne(targetEntity="Search")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="search_id", referencedColumnName="id")
     * })
     */
    private $search;

    /**
     * @var Keywords
     *
     * @ORM\ManyToOne(targetEntity="Keywords", inversedBy="positions")
     * @ORM\JoinColumn(name="keywords_id", referencedColumnName="id")
     */
    private $keywords;
    



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param date $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return date 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set position
     *
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set search
     *
     * @param Drosera\PositionCheckerBundle\Entity\Search $search
     */
    public function setSearch(\Drosera\PositionCheckerBundle\Entity\Search $search)
    {
        $this->search = $search;
    }

    /**
     * Get search
     *
     * @return Drosera\PositionCheckerBundle\Entity\Search 
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set keywords
     *
     * @param Drosera\PositionCheckerBundle\Entity\Keywords $keywords
     */
    public function setKeywords(\Drosera\PositionCheckerBundle\Entity\Keywords $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get keywords
     *
     * @return Drosera\PositionCheckerBundle\Entity\Keywords 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
}