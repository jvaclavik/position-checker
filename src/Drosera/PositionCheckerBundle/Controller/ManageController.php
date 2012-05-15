<?php

namespace Drosera\PositionCheckerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drosera\PositionCheckerBundle\Entity\Keywords;
use Drosera\PositionCheckerBundle\Entity\Websites;
use Drosera\PositionCheckerBundle\Entity\Positions;
use Drosera\PositionCheckerBundle\Entity\Search;
use Drosera\PositionCheckerBundle\Form\Type\ShowStatsType;
use Drosera\PositionCheckerBundle\Form\Type\ChangeWebsiteType;
use Drosera\PositionCheckerBundle\Form\Type\ImportType;

class ManageController extends Controller {

  public function indexAction() {
    $repository = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Websites');
    $data = $repository->findAll();


    $changeWebsiteForm = new ChangeWebsiteType();
    $form = $this->createForm($changeWebsiteForm, array());

    if ($this->getRequest()->getMethod() === 'POST') {
      $form->bindRequest($this->getRequest());

      if ($form->isValid()) {
        try { // TODO: pořešit try catch při duplicitě
          $val = $form->getData();
          $em = $this->getDoctrine()->getEntityManager(); //TODO: upravit EM jako globalní načítání
          
    $repositoryW = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Websites');
    $websites = $repositoryW->findOneByUrl($val["url"]);
    if (count($websites) == 0) {
      $websites = new Websites();
      $websites->setActive(1);
      $websites->setUrl($val["url"]);
      $em->persist($websites);
      $em->flush();
    }
          $keyword = explode("\n", $val["keywords"]);
          foreach ($keyword as $k) {
            $keywords = new Keywords();
            $keywords->addKeyword($em, $websites, $k);
          }
          $em->flush();
          $this->get('session')->setFlash('my_flash_key', "Zápis proběhl v pořádku.");
          
        } catch (\PDOException $e) {
          $this->get('session')->setFlash('my_flash_key', "Chyba při zápisu do databáze.");
        }
      }
    }




    return $this->render('DroseraPositionCheckerBundle:Manage:index.html.twig', array(
                'data' => $data,
                'form' => $form->createView(),
            ));
  }

  public function showWebsiteAction($id) {

    $repositoryW = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Websites');
//    $repositoryK = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Keywords');
    $repositoryS = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Search');
    $websites = $repositoryW->findOneById($id);
//    $dataK = $repositoryK->findBy(array("websites" => $id));
    $search = $repositoryS->findAll();





    $changeWebsiteForm = new ChangeWebsiteType();
    $changeWebsiteForm->loadData($websites->getUrl());

    $formAddKeywords = $this->createForm($changeWebsiteForm, array());


    $keywords = array();
    foreach ($websites->getKeywords() as $row) {

      if ($row->getActive() == 1)
        $keywords[$row->getKeyword()] = $row->getKeyword();
    }


    foreach ($search as $s) {
      $search = str_replace("_", ".", $s->getSearch());
      $searchData[$s->getId()] = $search;
    }
    $showStatsForm = new ShowStatsType();
    $showStatsForm->loadData($websites->getId(), $websites->getUrl(), $keywords, $searchData);
    $form = $this->createForm($showStatsForm, array());

    if ($this->getRequest()->getMethod() === 'POST') {
      $form->bindRequest($this->getRequest());

      if ($form->isValid()) {
        try {
          $val = $form->getData();
          $em = $this->getDoctrine()->getEntityManager(); //TODO: upravit EM jako globalní načítání
          $websites = new Websites();
          $websites->addWebsite($em, $val);
        } catch (\PDOException $e) {
          $this->get('session')->setFlash('my_flash_key', "Chyba při zápisu do databáze.");
        }
      }
    }

    
    

    
//    if ($this->getRequest()->getMethod() === 'POST') {
//      $form->bindRequest($this->getRequest());
//
//      if ($form->isValid()) {
//        $val = $form->getData();
//        echo $val["show"];
//        if ($val["show"] == "actual") {
//
//          $response = $this->forward('DroseraPositionCheckerBundle:Search:parse', array(
//              'val' => $val,
//                  ));
//          
//          return $response;
//        }
//        $response = $this->forward('DroseraPositionCheckerBundle:Render:index', array(
//            'val' => $val,
//                ));
//        return $response;
//      }
//    }





    return $this->render('DroseraPositionCheckerBundle:Manage:showWebsite.html.twig', array(
                'websites' => $websites,
                'form' => $form->createView(),
                'formAddKeywords' => $formAddKeywords->createView(),
            ));
  }

  public function addWebsiteAction() {
    $changeWebsiteForm = new changeWebsiteType();
    $form = $this->createForm($changeWebsiteForm, array());

    if ($this->getRequest()->getMethod() === 'POST') {
      $form->bindRequest($this->getRequest());

      if ($form->isValid()) {
        try { // TODO: pořešit try catch při duplicitě
          $val = $form->getData();
          $em = $this->getDoctrine()->getEntityManager(); //TODO: upravit EM jako globalní načítání
          $websites = new Websites();
          if($websites->addWebsite($em, $val) == 1){
            $this->get('session')->setFlash('my_flash_key', "Data byla uložena.");
            return $this->redirect($this->generateUrl('showWebsites'));
          }
        } catch (\PDOException $e) {
          $this->get('session')->setFlash('my_flash_key', "Chyba při zápisu do databáze. Možná duplicita.");
        }
      }
    }
    return $this->render('DroseraPositionCheckerBundle:Manage:addWebsite.html.twig', array(
                'form' => $form->createView(),
            ));
  }

  public function deleteWebsiteAction($websiteId) {
    ini_set('memory_limit', '-1');


    $em = $this->getDoctrine()->getEntityManager();
    $repositoryW = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Websites');
    $website = $repositoryW->findOneById($websiteId);

    if (method_exists($website, "getNewsletter"))
      foreach ($website->getNewsletter() as $newsletter)
        $em->remove($newsletter);
    if (method_exists($website, "getKeywords"))
      foreach ($website->getKeywords() as $keyword) {
        if (method_exists($keyword, "getPositions")) {
          foreach ($keyword->getPositions() as $position)
            $em->remove($position);
          $em->remove($keyword);
        }
        $em->remove($website);
      }

    $em->flush();
    $this->get('session')->setFlash('my_flash_key', "Web byl smazán včetně všech dat.");
    return $this->redirect($this->generateUrl('showWebsites'));
  }

  public function deleteNewsletterAction($newsletterId) {
    $em = $this->getDoctrine()->getEntityManager();
    $repositoryN = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Newsletter');
    $newsletter = $repositoryN->findOneById($newsletterId);
    $em->remove($newsletter);
    $em->flush();
    $this->get('session')->setFlash('my_flash_key', "Rozesílka byla smazána.");
    return $this->redirect($this->generateUrl('newsletterList'));
  }

  public function deleteKeywordAction($websiteId, $keywordId) {
    $em = $this->getDoctrine()->getEntityManager();
    $repositoryK = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Keywords');
    $keyword = $repositoryK->findOneById($keywordId);
    foreach ($keyword->getPositions() as $position)
      $em->remove($position);
    $em->remove($keyword);
    $em->flush();
    $this->get('session')->setFlash('my_flash_key', "Klíčové slovo bylo smazáno.");
    return $this->redirect($this->generateUrl('showWebsite', array('id' => $websiteId)));
  }

  public function changeActiveWebsiteAction($id) {

    try {
      $em = $this->getDoctrine()->getEntityManager();
      $repositoryW = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Websites');
      $website = $repositoryW->findOneById($id);
      $website->setActive(($website->getActive() == 1) ? 0 : 1);
      $em->persist($website);
      $em->flush();
    } catch (Exception $exc) {
      $this->get('session')->setFlash('my_flash_key', "Chyba.");
    }



return $this->redirect($this->generateUrl('showWebsites'));
  }

  public function changeActiveKeywordAction($websiteId, $keywordId) {
    try {
    $em = $this->getDoctrine()->getEntityManager();
    $repositoryK = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Keywords');
    $keyword = $repositoryK->findOneById($keywordId);
    $keyword->setActive(($keyword->getActive() == 1) ? 0 : 1);
    $em->persist($keyword);
    $em->flush();
    } catch (Exception $exc) {
      $this->get('session')->setFlash('my_flash_key', "Chyba.");
    }


    $response = $this->forward('DroseraPositionCheckerBundle:Manage:showWebsite', array("id" => $websiteId));
    return $response;
  }

  public function importAction() {
    ini_set('memory_limit', '-1');
    $import = new ImportType();
    $form = $this->createForm($import, array());

    $em = $this->getDoctrine()->getEntityManager();
    $repositoryW = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Websites');
    $repositoryK = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Keywords');
    $repositoryP = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Positions');
    $repositoryS = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Search');


    if ($this->getRequest()->getMethod() === 'POST') {
      $form->bindRequest($this->getRequest());

      if ($form->isValid()) {
        try {
          $data = $form->getData();
          $em = $this->getDoctrine()->getEntityManager();

          $block = explode("#", $data["import"]);
          $url = trim($block[0]);
//          echo $url . "<br>";

          $websites = $repositoryW->findOneByUrl($url);
          if (count($websites) == 0) {
            $websites = new Websites();
            $websites->setUrl($url);
            $websites->setActive(1);
            $em->persist($websites);
            $em->flush();
          }

//          echo "$url";
          for ($i = 1; $i < count($block) - 1; $i++) {
            if ($i % 2 == 1) {
              $searchAndKeyword = explode(":", $block[$i]);
              $search = $searchAndKeyword[0];
              $searchDB = $repositoryS->findOneBySearch($search);
//              if (count($searchDB) == 0)
//                echo"vyhledávač neexistuje";
              $keyword = $searchAndKeyword[1];
              //echo $search . ":";

//              echo $keyword . "<br>";

              $keywordDB = $repositoryK->findOneBy(array("websites" => $websites->getId(), "keyword" => $keyword));
              //var_dump($websites);
//              echo count($keywordDB);
              if (count($keywordDB) == 0) {
                $keywordDB = new Keywords();
                $keywordDB->setKeyword($keyword);
                $keywordDB->setWebsites($websites);
                $keywordDB->setActive(1);
                $em->persist($keywordDB);
                $em->flush();
              }
            } else {
              $block2 = explode("\n", $block[$i]);

              for ($j = 1; $j < count($block2) - 1; $j++) {

                $dateAndPosition = explode(",", $block2[$j]);

                $date = new \DateTime($dateAndPosition[0]);
                $position = trim($dateAndPosition[1]);

//                echo "'".$date . "',";
                //echo "'".$position . "'<br>";

                $positions = $repositoryP->findOneBy(array(
                    "keywords" => $keywordDB->getId(),
                    "date" => $date,
                    "search" => $searchDB->getId())
                );
                //echo count($positions)."///".$this->container->getParameter('max_count_scan');
                if (count($positions) == 0 && $position < $this->container->getParameter('max_count_scan')) {
                  $positions = new Positions();
                  $positions->setKeywords($keywordDB);
                  $positions->setSearch($searchDB);
                  $positions->setDate($date);
                  $positions->setPosition($position);
                  $em->persist($positions);
                  //echo "OK";
                }
              }
//              echo $positions[1];
            }
            //liche - search:kw
            //sude data - delit po radcich
          }
          $em->flush();
          $this->get('session')->setFlash('my_flash_key', "Data byla importována.");
          return $this->redirect($this->generateUrl('showWebsites'));
        } catch (\PDOException $e) {
          $this->get('session')->setFlash('my_flash_key', "Chyba.");
        }
      }
    }

//    $em = $this->getDoctrine()->getEntityManager();
//    $repositoryK = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Keywords');
//    $keyword = $repositoryK->findOneById($keywordId);
//    $keyword->setActive(($keyword->getActive() == 1) ? 0 : 1);
//    $em->persist($keyword);
//    $em->flush();
    return $this->render('DroseraPositionCheckerBundle:Manage:import.html.twig', array(
                'form' => $form->createView(),
            ));
  }

}
