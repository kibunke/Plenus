<?php

namespace GestionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EventoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('eventoAdaptado')
            ->add('orden')
            ->add('torneo', EntityType::class, array(
                                                'class' => 'ResultadoBundle:Torneo',
                                                'choice_label' => 'nombre',
                                                'multiple' => false,
                                                'required' => true,
                                                'empty_data'  => null
                                            )
                  )
            ->add('disciplina', EntityType::class, array(
                                                'class' => 'ResultadoBundle:Disciplina',
                                                'choice_label' => 'nombre',
                                                'multiple' => false,
                                                'required' => true,
                                                'empty_data'  => null
                                            )
                  )            
            ->add('categoria', EntityType::class, array(
                                                'class' => 'ResultadoBundle:Categoria',
                                                'choice_label' => 'nombre',
                                                'multiple' => false,
                                                'required' => true,
                                                'empty_data'  => null
                                            )
                  )
            ->add('genero', EntityType::class, array(
                                                'class' => 'ResultadoBundle:Genero',
                                                'choice_label' => 'nombre',
                                                'multiple' => false,
                                                'required' => true,
                                                'empty_data'  => null
                                            )
                  )            
            ->add('modalidad', EntityType::class, array(
                                                'class' => 'ResultadoBundle:Modalidad',
                                                'choice_label' => 'nombre',
                                                'multiple' => false,
                                                'required' => true,
                                                'empty_data'  => null
                                            )
                  )
            //->add('coordinadores')
            ->add('coordinadores', EntityType::class, array(
                                                'class' => 'CommonBundle:Persona',
                                                'choice_label' => 'nombreCompleto',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                        return $er->createQueryBuilder('p')
                                                                                    ->join('p.usuario','u')
                                                                                    ->join('u.perfil','perf')
                                                                                    ->where('perf.name = :perf')
                                                                                    ->orderby('p.apellido','ASC')
                                                                                    ->setParameter('perf', 'Coordinador');
                                                                    },
                                                'multiple' => true,
                                                'required' => false,
                                                'empty_data'  => null
                                            )
                  )            
            ->add('descripcion')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ResultadoBundle\Entity\Evento'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'plenus_resultadobundle_evento';
    }
}
