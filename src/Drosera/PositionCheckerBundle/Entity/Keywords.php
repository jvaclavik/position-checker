<?php

namespace Drosera\PositionCheckerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Drosera\PositionCheckerBundle\Entity\Keywords
 *
 * @ORM\Table(name="keywords")
 * @ORM\Entity
 */
class Keywords {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @var string $keyword
   *
   * @ORM\Column(name="keyword", type="string", length=100, nullable=false)
   */
  private $keyword;

  /**
   * @var boolean $active
   *
   * @ORM\Column(name="active", type="boolean", nullable=true)
   */
  private $active;

  /**
   * @var Websites
   *
   * @ORM\ManyToOne(targetEntity="Websites", inversedBy="keywords")
   * @ORM\JoinColumn(name="websites_id", referencedColumnName="id")
   */
  private $websites;

  /**
   * @var Positions
   *
   * @ORM\oneToMany(targetEntity="Positions", mappedBy="keywords")
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
   * Set keyword
   *
   * @param string $keyword
   */
  public function setKeyword($keyword) {
    $this->keyword = $keyword;
  }

  /**
   * Get keyword
   *
   * @return string 
   */
  public function getKeyword() {
    return $this->keyword;
  }

  /**
   * Set active
   *
   * @param boolean $active
   */
  public function setActive($active) {
    $this->active = $active;
  }

  /**
   * Get active
   *
   * @return boolean 
   */
  public function getActive() {
    return $this->active;
  }

  /**
   * Set websites
   *
   * @param Drosera\PositionCheckerBundle\Entity\Websites $websites
   */
  public function setWebsites(\Drosera\PositionCheckerBundle\Entity\Websites $websites) {
    $this->websites = $websites;
  }

  /**
   * Get websites
   *
   * @return Drosera\PositionCheckerBundle\Entity\Websites 
   */
  public function getWebsites() {
    return $this->websites;
  }

  public function getPositions() {
    return $this->positions;
  }

  public function setPositions($positions) {
    $this->positions = $positions;
  }

  public function addKeyword($em, $websites, $keyword) {

    $this->setActive(1);
    $this->setKeyword($keyword);
    $this->setWebsites($websites);

    $em->persist($this);

    return 1;
  }

}