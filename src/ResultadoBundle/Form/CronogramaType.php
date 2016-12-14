<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CronogramaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder
            ->add('descripcion')
            ->add('fecha', 'datetime', array(
                                            'widget' => 'single_text',
                                            // this is actually the default format for single_text
                                            'format' => 'dd/MM/yyyy H:mm',
                                            'html5' => false,
                                        )
                )
            ->add('escenario','entity', array(
                                    'class' => 'ResultadoBundle:Escenario',
                                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er ){
                                                            return $er->createQueryBuilder('e')->orderBy('e.nombre');
                                                        },
                                    'multiple' => false,
                                    'required' => true                                   
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
            array('data_class' => 'ResultadoBundle\Entity\Cronograma'))
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_cronograma';
    }
}
