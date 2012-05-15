<?php

namespace Drosera\PositionCheckerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ShowStatsType extends AbstractType {

  public $webId;
  public $url;
  public $keywords;
  public $search;

  public function buildForm(FormBuilder $builder, array $options) {
if (count($this->keywords) > 0) {
    $builder->add('keywords', 'choice', array(
        
        'choices' => $this->keywords,
        
        'expanded' => true,
        'multiple' => true,

        'label' => 'Klíčová slova',
    ));



    $builder->add('search', 'choice', array(
        'choices' => $this->search,
        'expanded' => true,
        'multiple' => true,
        'label' => 'Vyhledávače',
    ));


    $builder->add('show', 'choice', array(
        'choices' => array('history' => 'Historie', 'actual' => 'Aktuálně', 'export' => 'Export'),
        'data' => 'history',
        'expanded' => true,
        'multiple' => false,
        'label' => 'Zobrazit',
    ));

    $builder->add('from', 'text', array(
        'data' => date("Y-m-d", strtotime('-14 day', strtotime(Date("Y-m-d")))),
        'label' => 'Od',
    ));
    $builder->add('to', 'text', array(
        'data' => date("Y-m-d"),
        'label' => 'Do',
    ));

    $builder->add('types', 'choice', array(
        'choices' => array('plot' => 'Graf', 'table' => 'Tabulku'),
        'expanded' => true,
        'multiple' => true,
        'label' =>  ' ',
    ));
    $builder->add('webId', 'hidden', array(
        'data' => $this->webId,
    ));
    $builder->add('url', 'hidden', array(
        'data' => $this->url,
    ));
    }
//    var_dump($options);
  }

  public function getName() {
    return 'showStats';
  }

  public function loadData($webId, $url, $keywords, $searchData) {
    $this->webId = $webId;
    $this->url = $url;
    $this->keywords = $keywords;
    $this->search = $searchData;

    
  }


}