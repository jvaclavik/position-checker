<?php

namespace Drosera\PositionCheckerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\Collection; 
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Regex;

class ChangeWebsiteType extends AbstractType {

  public $web;

  public function buildForm(FormBuilder $builder, array $options) {
    //$builder->add('id', 'textarea');
//    foreach ($this->dataK as $row) {
//      $builder->add("[]", 'checkbox', array(
//          'label' => $row->getKeyword(),
//      ));

    $builder->add('url', ($this->web) ? 'hidden': 'text', array(
        'label' => 'Přidat web',
        'data' => ($this->web) ? $this->web : 'http://' 
//      'validation_constraint' => $collectionConstraint,
            )
    );

    $builder->add('keywords', 'textarea', array(
        'label' => 'Přidat klíčová slova (na každý řádek jedno)',
        'attr' =>  array('class' => 'textarea-small')
            )
    );
  }

  public function getName() {
    return 'ChangeWebsite';
  }

  public function loadData($web) {
    $this->web = $web;
  }
  
    public function getDefaultOptions(array $options)
    {
        return array(

            'validation_constraint' => new Collection(array(
                'fields' => array(
                    'url' => array(
                        new Url(array("message"=>"Špatně vyplněná URL. Musí obsahovat i \"http://\".")),
                    ),
                    'keywords' => array(
                        new Regex(array("message"=>"Zadáváte klíčová slova ve špatném tvaru. Povolené jsou pouze znaky a čísla. Jednotlivá klíčová slova oddělujte řádky.",
                            "pattern" => "/^[\wá-žÁ-Ž0-9\ \n\r]+$/",
                            "match" => "false",
                            )),
                    ),
                ),
            )),
        );
    }  
  
  
//    public function getDefaultOptions(array $options)
//    {
//        $collectionConstraint = new Collection(array(
//            'name' => new MinLength(5),
//            'url' => new Url(array('message' => 'Špatně vyplněná URL.')),
//        ));
//
//        return array('validation_constraint' => $collectionConstraint);
//    }  

}