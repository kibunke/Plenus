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
            ->add('maxIntegrantes', IntegerType::class, array("attr" => array('min' => 0)))
            ->add('minIntegrantes', IntegerType::class, array("attr" => array('min' => 0)))
            ->add('maxReemplazos', IntegerType::class, array("attr" => array('min' => 0)))
            ->add('minFechaNacimiento', DateType::class, array(
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => true,
                //'data' => new \DateTime('01/01/2000')
            ))
            ->add('maxFechaNacimiento', DateType::class, array(
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => true,
                //'data' => new \DateTime('01/01/2000')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InscripcionBundle\Entity\Segmento',
        ));
    }
}