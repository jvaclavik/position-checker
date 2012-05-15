<?php

namespace Drosera\PositionCheckerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Drosera\PositionCheckerBundle\Entity\Newsletter
 *
 * @ORM\Table(name="newsletter")
 * @ORM\Entity
 */
class Newsletter {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @var string $email
   *
   * @ORM\Column(name="email", type="string", length=100, nullable=false)
   */
  private $email;

  /**
   * @var boolean $active
   *
   * @ORM\Column(name="active", type="boolean", nullable=true)
   */
  private $active;

  /**
   * @var Websites
   *
   * @ORM\ManyToOne(targetEntity="Websites")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="websites_id", referencedColumnName="id")
   * })
   */
  private $websites;

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set email
   *
   * @param string $email
   */
  public function setEmail($email) {
    $this->email = $email;
  }

  /**
   * Get email
   *
   * @return string 
   */
  public function getEmail() {
    return $this->email;
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

  /**
   * Adding newsletter to storage
   * @param type $em Entity manager
   * @param type $val Array with data
   * @return int 
   */
  public function addNewsletter($doctrine, $val) {

    try {
      $em = $doctrine->getEntityManager();
      
      $repositoryW = $doctrine->getRepository('DroseraPositionCheckerBundle:Websites');
      $websites = $repositoryW->findOneByUrl($val["url"]);
      $this->setActive(1);
      $this->setEmail($val["email"]);
      $this->setWebsites($websites);
      $em->persist($this);
      $em->flush();
      return 1;
    } catch (Exception $exc) {
      echo $exc->getTraceAsString();
    }
  }
  
  

  

}