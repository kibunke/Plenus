<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartidoTantosCartasType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder
            ->add('resultadoLocal','integer',array(
                                                   'attr' => array(
                                                                   'style' => 'width: 70px;float:right;text-align: center;',
                                                                   'min' => 0
                                                                   )
                                                   )
                  )
            ->add('resultadoVisitante','integer',array(
                                                       'attr' => array(
                                                                       'style'=>'width: 70px;float:left;text-align: center;',
                                                                       'min' => 0
                                                                       )
                                                       )
                  )
            ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array('data_class' => 'ResultadoBundle\Entity\PartidoTantosCartas'))
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
