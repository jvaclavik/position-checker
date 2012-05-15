<?php

namespace Drosera\PositionCheckerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use Drosera\PositionCheckerBundle\Entity\Keywords;
use Drosera\PositionCheckerBundle\Entity\Websites;
use Drosera\PositionCheckerBundle\Entity\Positions;
use Drosera\PositionCheckerBundle\Entity\Container;

class SearchController extends Controller {
//posleme
//  web
//  KW
//  search

  public $dataContainer;
  public $outputFotActualSearching;

  /**
   * Transform URL for search engine. It replaces some variables like keyword or counting start. 
   * @param type $url
   * @param type $keyword
   * @param type $countFrom 
   */
  public function transformSearchUrl($urlSearch, $keyword, $countFrom) {
    $count = 10;
    $urlSearch = str_replace("[SEARCH_PHRASE]", urlencode($keyword), $urlSearch);
    $urlSearch = str_replace("[COUNT_FROM]", $countFrom, $urlSearch);
    $urlSearch = str_replace("[COUNT]", $count, $urlSearch);

    return $urlSearch;
  }

  public function parseAction() {


    if ($this->getRequest()->getMethod() === 'POST') {
      if ($data = $this->getRequest()->get("showStats")) {
        $repositoryW = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Websites');
        $website = $repositoryW->findOneBy(array('id' => $data["webId"]));

        $repositoryS = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Search');
        $search = $repositoryS->findAll();
//        var_dump($data);
        
        
        foreach ($search as $s) {
          
          if (isset($data["search"][$s->getId()])) {
            
            //echo "<h3>" . $s->getSearch() . "</h3>";
            $this->getKeywordsPosition($s, $data["keywords"], $website->getUrl());
          }
        }
      }
    }
//    var_dump($this->dataContainer);
    return $this->render('DroseraPositionCheckerBundle:Search:parse.html.twig', array("data"=>$this->outputFotActualSearching));
  }

  public function cronScanAction() {
    $repositoryW = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Websites');
    $websites = $repositoryW->findAll();
    $repositoryK = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Keywords');
    $repositoryS = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Search');
    $search = $repositoryS->findAll();

    foreach ($websites as $w) {
      if ($w->getActive() != 0) {
        //echo $w->getId();
        $keywords = $repositoryK->findBy(array('websites' => $w->getId()));
        $keywordsActive = NULL;
        foreach ($keywords as $k) {
          if ($k->getActive() == 1) {
            $keywordsActive[] = $k->getKeyword();
          }
        }
//          echo "<br>";
//          echo "<br>";
//          echo $w->getUrl()."<br>";
//          var_dump($keywordsActive);
//          echo "<br>";
//          echo "<br>";
        foreach ($search as $n => $s) {
          $this->getKeywordsPosition($s, $keywordsActive, $w->getUrl());
        }
      }
    }
//    echo "<pre>";
//    var_dump($this->dataContainer);
//    echo "</pre>";

    $this->saveCronScanAction();
    return $this->render('DroseraPositionCheckerBundle:Search:parse.html.twig', array());
  }

  public function getKeywordsPosition($s, $keywords, $url) {
    $maxcount = $this->container->getParameter('max_count_scan');
    $container = NULL;

    $em = $this->getDoctrine()->getEntityManager();

    foreach ($keywords as $n => $k) {
      $sucess = false;

      $query = $em->createQuery("
          SELECT p FROM 
            Drosera\PositionCheckerBundle\Entity\Positions p 
          JOIN 
            p.keywords k
          JOIN 
            k.websites w
          JOIN 
            p.search s
          WHERE
              (
                k.keyword = :keyword
              )
            AND 
              (
                s.id = :sid
              )
            AND
              w.url = :url
            AND
              p.date >= :date
          ");
      $query->setParameters(array(
          'keyword' => $k,
          'sid' => $s->getId(),
          'url' => $url,
          'date' => date("Y-m-d", strtotime('-1 day', strtotime(Date("Y-m-d")))),
      ));
      $dbData = $query->getResult();

//      var_dump($dbData);
//      $keywordsYesterday = $repositoryK->findBy(array(
//          'keyword' => $k,
//          'search' => $s,
//          'date' => $k,
//          'keyword' => $k,
//          
//          ));
//      $repositoryK
      $x = 0;

      if (count($dbData) > 0 && ($lastPosition = $dbData[0]->getPosition()) != $maxcount) { //efektivnejsi vyhledavani pro pozice co se tak nemeni, vychazi se z historie vyhledavani
        $page = floor(($lastPosition - 1) / 10);
        //echo $page."<br>";
        $numberOfScannedPage = 0; //pocet stranek co jsem prosel
        $start = $page * 10;
        $borderMin = 0;
        $borderMax = 0;
        while (true) {
          $start = (($start + $numberOfScannedPage * 10));
          //echo "$start + $numberOfScannedPage*10 = ";
          //echo $start."<br>";
          $numberOfScannedPage += ($numberOfScannedPage >= 0) ? 1 : -1;
          $numberOfScannedPage *= -1;
          if ($start < 0 || $start > $maxcount - 10) {
            if ($start < 0)
              $borderMin++;
            else
              $borderMax++;
            //echo "start = $start; bMax = $borderMax; bMin = $borderMin<br>";
            if ($borderMin > 0 && $borderMax > 0)
              break;
            continue;
          }
          $countFrom = $start;
//          $this->position = $start + 1;
          $x++;

          //echo "efektivni";
          if (($sucess = $this->getPosition($s, $k, $url, $countFrom)) === true)
            break;
        }
        //echo $x . "<br>";
      } else {


        $x = 0;
        for ($countFrom = 0; $maxcount > $countFrom; $countFrom += 10) {
          //echo "/";
          $x++;
          if (($sucess = $this->getPosition($s, $k, $url, $countFrom)) !== false) {
            break;
          }
        }
        //echo $x . "<br>";
      }
      //if ($sucess === false)
        //echo "nenalezeno<br>";
    }
  }

  public function getPosition($search, $keyword, $url, $countFrom) {
//echo $search->getId();

    $urlSearch = $this->transformSearchUrl($search->getUrl(), $keyword, $countFrom);


    $html = file_get_contents($urlSearch);
    $crawler = new Crawler($html);
    $nodes = $crawler->filterXPath($search->getXPath())->extract(array('_text'));
    foreach ($nodes   as $key => $node) {
      //echo $node." - ".$url."<br>";
			
      if (strpos($node, str_replace("http://", "", $url)) !== false) {
        $position = $countFrom + $key + 1;
        $this->outputFotActualSearching[$urlSearch] = "$url, {$search->getSearch()}, $keyword, $position";
        //echo "--";
        $this->dataContainer[] = new Container($url, $search->getSearch(), $keyword, $position);

        return true;
      }
    }


    return false;
  }

  public function saveCronScanAction() {
    //TODO: ošetřit zda dnes už je save!!!
    try {
      $repositoryW = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Websites');
      $repositoryK = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Keywords');
      $repositoryS = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Search');
      $repositoryP = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Positions');
      $em = $this->getDoctrine()->getEntityManager();

      foreach ($this->dataContainer as $d) {
//      var_dump($d);
//      echo "<br>";
        $dataW = $repositoryW->findOneByUrl($d->getUrl());
//      var_dump($dataW);
        $dataK = $repositoryK->findOneBy(array(
            "websites" => $dataW->getId(),
            "keyword" => $d->getKeyword()
                ));
//      echo "OK";


        $dataS = $repositoryS->findOneBySearch($d->getSearch());


        $dataP = $repositoryP->findBy(array(
            "date" => $d->getDate(),
            "search" => $dataS->getId(),
            "keywords" => $dataK->getId(),
                ));
        if (count($dataP) == 0) { //duplicate?
          $positions = new Positions();
          $positions->setPosition($d->getPosition());
          $positions->setDate($d->getDate());
          $positions->setSearch($dataS);
          $positions->setKeywords($dataK);
          $em->persist($positions);
        }
      }
      $em->flush();
    } catch (\Exception $exc) {
      $this->get('session')->setFlash('my_flash_key', "Chyba.");
    }
//       
//
//        $websites = new Websites();
//        $websites->setActive(1);
//        $websites->setUrl($val["url"]);
//        $em->persist($websites);
//
//
//        $keyword = explode("\n", $val["keywords"]);
//        foreach ($keyword as $k) {
//          $keywords = new Keywords();
//
//          $keywords->setActive(1);
//          $keywords->setKeyword($k);
//          $keywords->setWebsites($websites);
//
//          $em->persist($keywords);
//        }
//        $em->flush();
  }

}
