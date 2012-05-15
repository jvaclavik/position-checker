<?php

namespace Drosera\PositionCheckerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ImportType extends AbstractType {

  public $web;

  public function buildForm(FormBuilder $builder, array $options) {


    $builder->add('import', 'textarea', array(
        'label' => 'Vložte data pro import',
        'attr' =>  array('class' => 'textarea-big')
            )
    );
  }

  public function getName() {
    return 'ImportType';
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