<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AcreditacionBundle\Form;

/**
 * Description of AreaCategoriaPagoType
 *
 * @author kibunke
 */
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use TesoreriaBundle\Form\CategoriaPagoType;
use AcreditacionBundle\Form\AreaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AreaCategoriaPagoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('cupoMax', 'text')
               
                ->add('categoria', 'entity', array(
                    'class' => 'TesoreriaBundle:CategoriaPago',
                    'choice_label' => 'nombre',
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AcreditacionBundle\Entity\AreaCategoriaPago'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'juegosba_acreditacionbundle_areacategoriapago';
    }

}
