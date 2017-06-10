<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;

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
                ->add('descripcion')
                ;
        if ($entity->getZona())
        {
            $builder
                    ->add('plaza1', EntityType::class, array(
                                                                'class'         => 'ResultadoBundle:PlazaZona',
                                                                'property'      => 'getNombreCompleto',
                                                                'query_builder' => function(EntityRepository $er )use($entity)
                                                                                   {
                                                                                        return $er->createQueryBuilder('p')
                                                                                                    ->join('p.zona','z')
                                                                                                    ->where('z = ?1')
                                                                                                    ->setParameter(1, $entity->getZona())
                                                                                        ;                                                          
                                                                                    },
                                                                'multiple'    => false,
                                                                'required'    => false,
                                                                'placeholder' => 'Seleccione'
                                                            )
                          )
                    ->add('plaza2', EntityType::class, array(
                                                                'class'         => 'ResultadoBundle:PlazaZona',
                                                                'property'      => 'getNombreCompleto',
                                                                'query_builder' => function(EntityRepository $er )use($entity)
                                                                                   {
                                                                                        return $er->createQueryBuilder('p')
                                                                                                  ->join('p.zona','z')
                                                                                                  ->where('z = ?1')
                                                                                                  ->setParameter(1, $entity->getZona())
                                                                                                  ;                                                          
                                                                                    },
                                                                'multiple'    => false,
                                                                'required'    => false,
                                                                'placeholder' => 'Seleccione'
                                                            )
                          )
                    ->add('zona', EntityType::class, array(
                                                            'class'         => 'ResultadoBundle:Zona',
                                                            'property'      => 'nombre',
                                                            'query_builder' => function(EntityRepository $er )use($entity)
                                                                               {
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
    public function configureOptions(OptionsResolverInterface $resolver)
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
