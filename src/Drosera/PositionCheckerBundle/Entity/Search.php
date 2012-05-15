<?php

namespace Drosera\PositionCheckerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Drosera\PositionCheckerBundle\Entity\Search
 *
 * @ORM\Table(name="search")
 * @ORM\Entity
 */
class Search {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @var string $search
   *
   * @ORM\Column(name="search", type="string", length=100, nullable=false)
   */
  private $search;

  /**
   * @var text $url
   *
   * @ORM\Column(name="url", type="text", nullable=false)
   */
  private $url;

  /**
   * @var text $xpath
   *
   * @ORM\Column(name="xpath", type="text", nullable=false)
   */
  private $xpath;

  /**
   * @var Positions
   *
   * @ORM\oneToMany(targetEntity="Positions", mappedBy="search")
   */
  private $positions;
  

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set search
   *
   * @param string $search
   */
  public function setSearch($search) {
    $this->search = $search;
  }

  /**
   * Get search
   *
   * @return string 
   */
  public function getSearch() {
    return $this->search;
  }

  /**
   * Set url
   *
   * @param text $url
   */
  public function setUrl($url) {
    $this->url = $url;
  }

  /**
   * Get url
   *
   * @return text 
   */
  public function getUrl() {
    return $this->url;
  }

  /**
   * Set xpath
   *
   * @param text $xpath
   */
  public function setXpath($xpath) {
    $this->xpath = $xpath;
  }

  /**
   * Get xpath
   *
   * @return text 
   */
  public function getXpath() {
    return $this->xpath;
  }
  public function getPositions() {
    return $this->positions;
  }

  public function setPositions($positions) {
    $this->positions = $positions;
  }


}