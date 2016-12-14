<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartidoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder
            ->add('nombre')
            ->add('descripcion');
        if ($entity->getZona()){
            $builder
            ->add('plaza1', 'entity', array(
                                                'class' => 'ResultadoBundle:PlazaZona',
                                                'property' => 'getNombreCompleto',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er ) use($entity){
                                                                        return $er->createQueryBuilder('p')
                                                                                    ->join('p.zona','z')
                                                                                    ->where('z = ?1')
                                                                                    ->setParameter(1, $entity->getZona())
                                                                        ;                                                          
                                                                    },
                                                'multiple' => false,
                                                'required' => false,
                                                'placeholder' => 'Seleccione'
                                            )
                  )
            ->add('plaza2', 'entity', array(
                                                'class' => 'ResultadoBundle:PlazaZona',
                                                'property' => 'getNombreCompleto',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er ) use($entity){
                                                                        return $er->createQueryBuilder('p')
                                                                                    ->join('p.zona','z')
                                                                                    ->where('z = ?1')
                                                                                    ->setParameter(1, $entity->getZona())
                                                                        ;                                                          
                                                                    },
                                                'multiple' => false,
                                                'required' => false,
                                                'placeholder' => 'Seleccione'
                                            )
                  )
            ->add('zona', 'entity', array(
                                            'class' => 'ResultadoBundle:Zona',
                                            'property' => 'nombre',
                                            'query_builder' => function(\Doctrine\ORM\EntityRepository $er ) use($entity){
                                                                    return $er->createQueryBuilder('z')
                                                                                ->where('z = ?1')
                                                                                ->setParameter(1, $entity->getZona())
                                                                    ;                                                          
                                                                },
                                            'multiple' => false,
                                            'required' => true
                                        )
                 )
            ;
        }
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array('data_class' => 'ResultadoBundle\Entity\Partido'))
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_partido';
    }
}
