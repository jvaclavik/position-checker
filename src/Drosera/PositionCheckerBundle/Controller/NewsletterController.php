<?php

namespace Drosera\PositionCheckerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drosera\PositionCheckerBundle\Entity\Newsletter;
use Drosera\PositionCheckerBundle\Form\Type\AddNewsletterType;

class NewsletterController extends Controller {

  public function indexAction() {
    $repository = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Newsletter');
    $data = $repository->findAll();


    return $this->render('DroseraPositionCheckerBundle:Newsletter:index.html.twig', array(
                'data' => $data
            ));
  }

  public function changeActiveNewsletterAction($id) {
    try {
      $em = $this->getDoctrine()->getEntityManager();
      $repositoryN = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Newsletter');
      $newsletter = $repositoryN->findOneById($id);
      $newsletter->setActive(($newsletter->getActive() == 1) ? 0 : 1);
      $em->persist($newsletter);
      $em->flush();
    } catch (\PDOException $e) {
      $this->get('session')->setFlash('my_flash_key', "Chyba při změně aktivity u rozesílky.");
    }


return $this->redirect($this->generateUrl('newsletterList'));
  }

  public function addNewsletterAction() {
    $doctrine = $this->getDoctrine();
    $repositoryW = $doctrine->getRepository('DroseraPositionCheckerBundle:Websites');
    $websites = $repositoryW->findAll();
//    $urls = $websites->getUrlNames();

    $addNewsletterForm = new AddNewsletterType();
    $addNewsletterForm->loadData($websites);
    $newsletter = new Newsletter();



    $form = $this->createForm($addNewsletterForm);

    if ($this->getRequest()->getMethod() === 'POST') {
      $form->bindRequest($this->getRequest());

      if ($form->isValid()) {
        try {
          $val = $form->getData();
          $doctrine = $this->getDoctrine(); //TODO: upravit EM jako globalní načítání
          $newsletter = new Newsletter();
          if ($newsletter->addNewsletter($doctrine, $val) == 1)
            $this->get('session')->setFlash('my_flash_key', "E-mail přidán do rozesílky.");
          $response = $this->forward('DroseraPositionCheckerBundle:Newsletter:index');
          return $response;
        } catch (\PDOException $e) {
          $this->get('session')->setFlash('my_flash_key', "Chyba. E-mail nebyl přidán do rozesílky.");
        }
      }
    }
    return $this->render('DroseraPositionCheckerBundle:Newsletter:addNewsletter.html.twig', array(
                'form' => $form->createView(),
            ));
  }

  public function cronNewsletterAction() {

    $repositoryN = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Newsletter');
//    $repositoryS = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Newsletter');
    $dataN = $repositoryN->findBy(array('active' => 1));
//    $dataS = $repositoryS->findAll();
    foreach ($dataN as $d) {
      //echo $d->getWebsites()->getUrl() . "<br>";


      $from = date("Y-m-d", strtotime('-14 day', strtotime(Date("Y-m-d"))));
      $to = date("Y-m-d");

      $response = $this->forward('DroseraPositionCheckerBundle:Render:getData', array(
          'webId' => $d->getWebsites()->getId(),
          'from' => $from,
          'to' => $to,
              ));
      $container = unserialize($response->getContent());
      
      $responseDateInterval = $this->forward('DroseraPositionCheckerBundle:Render:dateInterval', array(
          'from' => $from,
          'to' => $to,
              ));
      $dateInterval = unserialize($responseDateInterval->getContent());
//      $container = $response->getContent();
//        var_dump($container); 
//        return $response;
//        foreach ($container as $k=>$c){
//          $kk= $k;
//        var_dump($container); 
//        }
//echo "<pre>";
//echo($container->getContent());
//echo "</pre>";
//      return $this->render('DroseraPositionCheckerBundle:Newsletter:emailTemplate.html.twig', array('container' => $container));
      $message = \Swift_Message::newInstance()
              ->setSubject('Pozice ve vyhledavacich: ' . $d->getWebsites()->getUrl())
              ->setFrom(array('position_checker@drosera.cz' => 'Drosera'))
              ->setTo($d->getEmail())
              ->setBody("Dobry den, <br>zasilame Vam pozice ve vyhledavacich stranky: <a href='".$d->getWebsites()->getUrl()."'>".$d->getWebsites()->getUrl()."</a>:<br>".$this->renderView('DroseraPositionCheckerBundle:Newsletter:emailTemplate.html.twig', array(
                  'container' => $container,
                  'email' => true,
                  'dateInterval' => $dateInterval
              )), 'text/html', 'utf-8')
      ;
      //->setBody('Dobry den, <br>zasilame Vam pozice ve vyhledavacich stranky: '.$d->getWebsites()->getUrl().'<br><br><br>S pozdravem<br><br>http://drosera.cz<br><br>'.$tables)
      $this->get('mailer')->send($message);
      
    }


    $this->get('session')->setFlash('my_flash_key', "Rozesílka poslána");



    $repository = $this->getDoctrine()->getRepository('DroseraPositionCheckerBundle:Newsletter');
    $data = $repository->findAll();


    return $this->render('DroseraPositionCheckerBundle:Newsletter:index.html.twig', array(
                'data' => $data,
            ));
  }


}
