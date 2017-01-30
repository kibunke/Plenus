<?php

namespace InscripcionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('maxIntegrantes', NumberType::class, array("attr" => array('placeholder' => 'Ingrese la descripción del segmento')))
            ->add('minIntegrantes', NumberType::class, array("attr" => array('placeholder' => 'Ingrese la descripción del segmento')))
            ->add('maxReemplazos', NumberType::class, array("attr" => array('placeholder' => 'Ingrese la descripción del segmento')))
            ->add('minFechaNacimiento', DateType::class, array("attr" => array('placeholder' => 'Ingrese la descripción del segmento')))
            ->add('maxFechaNacimiento', DateType::class, array("attr" => array('placeholder' => 'Ingrese la descripción del segmento')))
            ->add('eventos', EntityType::class, array(
                                        'class' => 'ResultadoBundle:Evento',
            //                            'query_builder' => function (EntityRepository $er) {
            //                                return $er->createQueryBuilder('p')
            //                                    ->where('p.availableForNewUsers = 1')
            //                                    ->orderBy('p.name', 'ASC');
            //                            },
                                        'choice_label' => 'getNombreCompleto',
                                        'placeholder'  => 'Eventos del segmento'
                                    )
                )
            //->add('eventos', CheckboxType::class, array('label' => 'Activo', 'required' => false))            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InscripcionBundle\Entity\Segmento',
        ));
    }
}