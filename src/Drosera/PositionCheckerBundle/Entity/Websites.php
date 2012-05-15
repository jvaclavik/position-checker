<?php

namespace Drosera\PositionCheckerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Drosera\PositionCheckerBundle\Entity\Websites
 *
 * @ORM\Table(name="websites")
 * @ORM\Entity
 */
class Websites {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="IDENTITY")
   */
  private $id;

  /**
   * @var string $url
   *
   * @ORM\Column(name="url", type="string", length=100, nullable=false)
   */
  private $url;

  /**
   * @var boolean $active
   *
   * @ORM\Column(name="active", type="boolean", nullable=true)
   */
  private $active;

  /**
   * @var Keywords
   *
   * @ORM\oneToMany(targetEntity="Keywords", mappedBy="websites")
   */
  private $keywords;

  /**
   * @var Newsletter
   *
   * @ORM\oneToMany(targetEntity="Newsletter", mappedBy="websites")
   */
  private $newsletter;

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set url
   *
   * @param string $url
   */
  public function setUrl($url) {
    $this->url = $url;
  }

  /**
   * Get url
   *
   * @return string 
   */
  public function getUrl() {
    return $this->url;
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

  public function getKeywords() {
    return $this->keywords;
  }

  public function setKeywords($keywords) {
    $this->keywords = $keywords;
  }

  public function getNewsletter() {
    return $this->newsletter;
  }

  public function setNewsletter($newsletter) {
    $this->newsletter = $newsletter;
  }

  /**
   * Adding website to storage
   * @param type $em Entity manager
   * @param type $val Array with data
   * @return int 
   */
 public function addWebsite($em, $val) {
   try {
     $this->setActive(1);
     $this->setUrl($val["url"]);
     $em->persist($this);

     $keyword = explode("\n", $val["keywords"]);
     foreach ($keyword as $k) {
       $keywords = new Keywords();
       $keywords->addKeyword($em, $this, $k);
     }
     $em->flush();
     return 1;
   } catch (\Exception $exc) {
     $this->get('session')->setFlash('my_flash_key', "Chyba při zápisu do databáze.");
   }
 }

  public function getUrlNames() {
    $urls = NULL;
    foreach ($this as $t) {
      $urls[] = $t->getUrl();
    }
    return $urls;
  }

}