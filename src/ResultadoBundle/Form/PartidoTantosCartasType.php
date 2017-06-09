<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PartidoTantosCartasType extends PartidoPuntosType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
                                array('data_class' => 'ResultadoBundle\Entity\PartidoTantosCartas')
                              )
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_partido_tantos_cartas';
    }
}
