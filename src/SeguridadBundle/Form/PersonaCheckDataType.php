<?php

namespace SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PersonaCheckDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array(
                                                      'attr' => array('placeholder' => 'Nombre'),
                                                      'label'    => 'Nombre(*)'
                                                     )
                  )
            ->add('apellido', TextType::class, array(
                                                      'attr' => array('placeholder' => 'Apellido'),
                                                      'label'    => 'Apellido(*)'
                                                     )
                  )
            ->add('fNacimiento', DateType::class, array(
                                                         'attr'    => array(
                                                                            'placeholder' => 'Fecha de Nacimiento',
                                                                            'class'       => 'datetimepicker'
                                                                           ),
                                                         'widget'   => 'single_text',
                                                         'format'   => 'dd/MM/yyyy',
                                                         'label'    => 'Nacimiento(*)'
                                                        )
                 )
            ->add('telefono', TextType::class, array(
                                                      'attr' => array('placeholder' => 'Teléfono'),
                                                      'label'    => 'Teléfono(*)'
                                                     )
                  )
            ->add('facebook', TextType::class, array(
                                                     'attr' => array('placeholder' => 'Cuenta de Facebook'),
                                                     'required' => false
                                                     )
                  )
            ->add('skype', TextType::class, array(
                                                  'attr' => array('placeholder' => 'Cuenta de Skype'),
                                                  'required' => false,
                                                  )
                  )
            ->add('twitter', TextType::class, array(
                                                    'attr' => array('placeholder' => 'Cuenta de Twitter'),
                                                    'required' => false
                                                    )
                  )
            ->add('linkedin', TextType::class, array(
                                                     'attr' => array('placeholder' => 'Cuenta de Linkedin'),
                                                     'required' => false)
                  )
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CommonBundle\Entity\Persona',
        ));
    }
}