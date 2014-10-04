<?php

namespace GescanPim\Bundle\ConnectorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Type for color custom entity
 */
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of referentialType
 *
 * @author ecoisne
 */
class ReferentialType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name');
        
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'gescan_connector_form_referential';
    }
}
