<?php

namespace Drosera\PositionCheckerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Collection; 
use Symfony\Component\Validator\Constraints\Url; 

class AddNewsletterType extends AbstractType {

  public $web;

  public function buildForm(FormBuilder $builder, array $options) {
$collectionConstraint = new Collection(array(
                'email' => new Email(array('message' => 'Invalid email address')),
            ));
    $builder->add('url', 'choice', array(
        'choices' => ($this->web),
        'data' => 'history',
        'expanded' => false,
        'multiple' => false,
        'label' => 'Přidat sledování webu',

    ));
    
//    $builder->add('url', ($this->web) ? 'hidden' : 'text', array(
//        'label' => 'Přidat sledování webu',
//        'data' => ($this->web) ? $this->web : ''
//            )
//    );

    $builder->add('email', 'text', array(
        'label' => 'E-mail',
            )
    );
  }

  public function getName() {
    return 'newsletter';
  }

  public function loadData($websites) {
    foreach ($websites as $website) {
      $this->web[$website->getUrl()] = $website->getUrl();
    }
  }
  
    public function getDefaultOptions(array $options)
    {
        return array(

            'validation_constraint' => new Collection(array(
                'fields' => array(
                    'email' => array(
                        new Email(array("message"=>"Zadejte správný e-mail")),
                    ),
                    'url' => array(
                        new Url(array("message"=>"Špatně vyplněná URL. Musí obsahovat i \"http://\".")),
                    ),
                ),
            )),
        );
    }
  
//public function getDefaultOptions(array $options)
//{
//    return array(
//        'data_class' => 'Drosera\PositionCheckerBundle\Entity\Newsletter',
//    );
//}
}