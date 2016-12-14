<?php

namespace AcreditacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AcreditacionBundle\Form\EventListener\RangoTrabajo;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AcreditacionType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fechaIniTrabajo', 'date', array(
                    'input' => 'datetime',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'label' => 'Fecha de inicio de tarea',
                    'required' => false,
                ))
                ->add('fechaFinTrabajo', 'date', array(
                    'input' => 'datetime',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'label' => 'Fecha fin de tarea',
                    'required' => false,
                ))
                ->add('fechaLimiteAcreditacion', 'date', array(
                    'input' => 'datetime',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'label' => 'Fecha límite de acreditación',
                ))
                ->add('submit', 'submit', array(
                    'attr' => array('class' => 'btn-primary btn'),
                    'label' => 'Actualizar'
                ))
                ->add('rangoFechaTrabajo', 'text', array(
                    'mapped' => false,
                    'label' => 'Dias',
                ))->addEventSubscriber(new RangoTrabajo());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AcreditacionBundle\Entity\JuegosParametros'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'juegosba_acreditacionbundle_juegosparametros';
    }

}
