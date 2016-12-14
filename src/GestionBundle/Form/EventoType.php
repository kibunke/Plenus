<?php

namespace GestionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('inscribe')
            ->add('soloInscribe')
            ->add('eventoAdaptado')
            ->add('orden')
            //->add('inscriptos')
            ->add('torneo', 'entity', array(
                                                'class' => 'ResultadoBundle:Torneo',
                                                'property' => 'nombre',
                                                'multiple' => false,
                                                'required' => true,
                                                'empty_data'  => null
                                            )
                  )
            ->add('disciplina', 'entity', array(
                                                'class' => 'ResultadoBundle:Disciplina',
                                                'property' => 'nombre',
                                                'multiple' => false,
                                                'required' => true,
                                                'empty_data'  => null
                                            )
                  )            
            ->add('categoria', 'entity', array(
                                                'class' => 'ResultadoBundle:Categoria',
                                                'property' => 'nombre',
                                                'multiple' => false,
                                                'required' => true,
                                                'empty_data'  => null
                                            )
                  )
            ->add('genero', 'entity', array(
                                                'class' => 'ResultadoBundle:Genero',
                                                'property' => 'nombre',
                                                'multiple' => false,
                                                'required' => true,
                                                'empty_data'  => null
                                            )
                  )            
            ->add('modalidad', 'entity', array(
                                                'class' => 'ResultadoBundle:Modalidad',
                                                'property' => 'nombre',
                                                'multiple' => false,
                                                'required' => true,
                                                'empty_data'  => null
                                            )
                  )
            //->add('coordinadores')
            ->add('coordinadores', 'entity', array(
                                                'class' => 'SeguridadBundle:Usuario',
                                                'property' => 'nombreCompleto',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                        return $er->createQueryBuilder('u')
                                                                                    ->join('u.funcion','f')
                                                                                    ->where('f.nombre <> :fun')
                                                                                    ->orderby('u.apellido','ASC')
                                                                                    ->setParameter('fun', 'Consulta municipal');
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
        return 'juegosba_resultadobundle_evento';
    }
}
