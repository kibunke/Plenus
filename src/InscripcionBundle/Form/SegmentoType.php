<?php

namespace InscripcionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Doctrine\ORM\EntityRepository;

use CommonBundle\Form\PersonaType;

class SegmentoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array("attr" => array('placeholder' => 'Ingrese el nombre del segmento')))
            ->add('descripcion', TextareaType::class, array("attr" => array('placeholder' => 'Ingrese la descripción del segmento')))
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
            ->add('maxIntegrantes', IntegerType::class, array(
                                                              "attr" => array(
                                                                                'min' => 0,
                                                                                'placeholder' => 'Máximo integrantes'
                                                                        ),
                                                              "label" => "Máximo"
                                                            )
                  )
            ->add('minIntegrantes', IntegerType::class, array(
                                                              "attr" => array(
                                                                                'min' => 0,
                                                                                'placeholder' => 'Mínimo integrantes'
                                                                        ),
                                                              "label" => "Mínimo"
                                                            )
                  )
            ->add('maxReemplazos', IntegerType::class, array(
                                                             "attr" => array(
                                                                                'min' => 0,
                                                                                'placeholder' => 'Máximo reemplazos'
                                                                        ),
                                                             
                                                             "label" => "Reemplazos"
                                                            )
                  )
            ->add('minFechaNacimiento', DateType::class, array(
                "label" => "Fecha de nacimiento inicio",
                'widget' => 'single_text',
                'html5' => false,
                'format'   => 'dd/MM/yyyy',
                'attr' => array('class' => 'datetimepicker')
            ))
            ->add('maxFechaNacimiento', DateType::class, array(
                "label" => "Fecha de nacimiento fin",
                'widget' => 'single_text',
                'html5' => false,
                'format'   => 'dd/MM/yyyy',
                'attr' => array('class' => 'datetimepicker')
            ))
            ->add('coordinadores', EntityType::class, array(
                                                'class' => 'SeguridadBundle:Usuario',
                                                'choice_label' => 'nombreCompleto',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                        return $er->createQueryBuilder('u')
                                                                                    ->join('u.persona','p')
                                                                                    ->join('u.perfil','perf')
                                                                                    ->where('perf.name = :perf AND u.isActive = :active')
                                                                                    ->orderby('p.apellido','ASC')
                                                                                    ->setParameter('perf', 'Coordinador')
                                                                                    ->setParameter('active', true);
                                                                    },
                                                'multiple' => true,
                                                'required' => false,
                                                'empty_data'  => null
                                            )
                  )            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InscripcionBundle\Entity\Segmento',
        ));
    }
}