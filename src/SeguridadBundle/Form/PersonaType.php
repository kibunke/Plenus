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

class PersonaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('attr' => array('placeholder' => 'casilla@email.com')))
            //->add('municipio', EntityType::class, array(
            //                            'class' => 'CommonBundle:Municipio',
            //                            'query_builder' => function (EntityRepository $er) {
            //                                return $er->createQueryBuilder('p')
            //                                    ->where('p.provincia = 1')
            //                                    ->orderBy('p.nombre', 'ASC');
            //                            },
            //                            'choice_label' => 'nombre',
            //                            'placeholder' => 'Seleccione su municipio'
            //                        )
            //    )
            ->add('fNacimiento', DateType::class, array(
                                                         'attr'    => array(
                                                                            'placeholder' => 'Fecha de Nacimiento',
                                                                            'class'       => 'datetimepicker'
                                                                           ),
                                                         'widget'   => 'single_text',
                                                         'format'   => 'dd/MM/yyyy',
                                                         'required' => false
                                                        )
                 )
            ->add('telefono', TextType::class, array(
                                                      'attr' => array('placeholder' => 'Teléfono'),
                                                      'required' => false
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
            ->add('avatar', TextType::class, array(
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