<?php

namespace Drosera\PositionCheckerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Drosera\PositionCheckerBundle\Entity\Keywords;
use Drosera\PositionCheckerBundle\Entity\Websites;
use Drosera\PositionCheckerBundle\Entity\Positions;
use Symfony\Component\HttpFoundation\Response;

class RenderController extends Controller {

  public function removeLineBreaks($str) {
    $str = str_replace("\n", "", $str);
    $str = str_replace("\r", "", $str);

    return $str;
  }
  
  public function getDataAction($webId, $from, $to, $keywordsList = null, $searchList = null) {
    $em = $this->getDoctrine()->getEntityManager(); //TODO: upravit EM jako globalní načítání
//    echo "$webId, $from, $to, $keywordsList = null, $searchList = null";

    $keywordsString = "1=1";
    $i = 0;
    if ($keywordsList != null) {
      $keywordsString = "";

      foreach ($keywordsList as $keyword) {
        $keywordsString .= " k.keyword = '$keyword'";
        if ($i + 1 < count($keywordsList))
          $keywordsString .= " OR ";
        $i++;
      }
    }


    $searchString = "1=1";
    $i = 0;
    if ($searchList != null) {
      $searchString = "";
      foreach ($searchList as $search) {
        $searchString .= " s.id = '$search'";
        if ($i + 1 < count($searchList))
          $searchString .= " OR ";
        $i++;
      }
    }

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
                $searchString
              )
            AND 
              (
                $keywordsString
              )
            AND
              w.id = :id
            AND
              k.active = 1
            AND
              p.date >= :from
            AND
              p.date <= :to
          ");
    $query->setParameters(array(
        'id' => $webId,
        'from' => $from,
        'to' => $to,
    ));
    $dbData = $query->getResult();


    $container = NULL;

    foreach ($dbData as $pos) {

      for ($dateCheck = $from; date("Y-m-d", strtotime('-1 day', strtotime($dateCheck))) != $to; $dateCheck = date("Y-m-d", strtotime('+1 day', strtotime($dateCheck)))) {
        $container[$pos->getSearch()->getSearch()][$this->removeLineBreaks($pos->getKeywords()->getKeyword())][$dateCheck] = 0;
      }
    }
    foreach ($dbData as $pos) {
      $container[$pos->getSearch()->getSearch()][$this->removeLineBreaks($pos->getKeywords()->getKeyword())][$pos->getDate()->format('Y-m-d')] = $pos->getPosition();
    }
//    echo "<pre>";
//
//    var_dump($container);
//    echo "</pre>";

    return new Response(serialize($container));
  }

//  public function indexAction() {
//          
//    if ($this->getRequest()->getMethod() === 'POST') {
//      if ($data = $this->getRequest()->get("showStats")) {
//        $container = $this->getDataAction($data["webId"], $data["from"], $data["to"], (isset($data["keywords"])) ? $data["keywords"] : null, (isset($data["search"])) ? $data["search"] : null);
//      }
//    }
//
//
//
//    return $this->render('DroseraPositionCheckerBundle:Render:index.html.twig', array(
//                'types' => $data["types"],
//                'container' => $container,
//                'dateInterval' => $this->dateInterval($data["from"], $data["to"]),
//            ));
//  }

  public function websiteAction($id = null) {
    if ($this->getRequest()->getMethod() === 'POST') {


      if ($data = $this->getRequest()->get("showStats")) {
        if (isset($data["show"]) and $data["show"] == "actual") {
          //if(!count($data > 0)) return 0;

          $response = $this->forward('DroseraPositionCheckerBundle:Search:parse', array(
              'val' => $data,
                  ));
          return $response;
        }

        $id = $data["webId"];
        $from = $data["from"];
        $to = $data["to"];
        $keywords = (isset($data["keywords"])) ? $data["keywords"] : null;
        $search = (isset($data["search"])) ? $data["search"] : null;
      }
    } else {

      $from = date("Y-m-d", strtotime('-14 day', strtotime(Date("Y-m-d"))));
      $to = date("Y-m-d");
      $keywords = null;
      $search = null;
    }


    $container = unserialize($this->getDataAction($id, $from, $to, $keywords, $search)->getContent());

    if (count($container) == 0)
      $this->get('session')->setFlash('my_flash_key', "Nedostatek dat");

//    echo ($to < $from);
//    die();
    if ($to < $from) {
      $this->get('session')->setFlash('my_flash_key', "Špatný interval zobrazení dat.");
      $response = $this->forward('DroseraPositionCheckerBundle:Manage:showWebsite', array("id" => $id));
      return $response;
    }


    return $this->render('DroseraPositionCheckerBundle:Render:index.html.twig', array(
                'types' => (isset($data["types"])) ? $data["types"] : array("table" => 1, "plot" => 1),
                'show' => (isset($data["show"])) ? $data["show"] : "",
                'url' => (isset($data["url"])) ? $data["url"] : "",
                'container' => $container,
                'id' => $id,
                'dateInterval' => unserialize($this->dateIntervalAction($from, $to)->getContent()),
            ));
  }

  /**
   * Function for saving date interval between two days
   * @param type $from
   * @param type $to
   * @return array with dates 
   */
  public function dateIntervalAction($from, $to) {
    $last = $from;
    $startDay = strtotime($from);
    $days = 0;
    do {
      $last = date("Y-m-d", strtotime('+' . $days . ' day', $startDay));
      $interval[$days] = $last;
      $days++;
    } while ($to != $last);

//    echo "<pre>";
//    print_r($interval);
//    echo "</pre>";
    return new Response(serialize($interval));
  }

}
