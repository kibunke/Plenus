<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartidoTantosType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder
            ->add('resultadoLocal','integer',array())
            ->add('resultadoVisitante','integer',array())
            ->add('resultadoSecundarioLocal','integer',array())
            ->add('resultadoSecundarioVisitante','integer',array())            
            ->add('tanteador','text',array(
                                            'attr' => array(
                                                            'style'=>'text-align: center;',
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
            array('data_class' => 'ResultadoBundle\Entity\PartidoTantos'))
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_partido_tantos';
    }
}
