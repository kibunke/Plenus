<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlazaMedalleroType extends PlazaType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array('data_class' => 'ResultadoBundle\Entity\PlazaMedallero'))
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_plaza';
    }
}
