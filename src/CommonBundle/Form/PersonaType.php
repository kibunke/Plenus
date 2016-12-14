<?php

namespace CommonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PersonaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array("attr" => array('placeholder' => 'Ingrese su nombre')))
            ->add('apellido', TextType::class, array("attr" => array('placeholder' => 'Ingrese su apellido')))
            ->add('tipoDocumento', EntityType::class, array(
                                                'class' => 'CommonBundle:TipoDocumento',
                                                'choice_label' => 'nombre'
                                            )
                          )
            ->add('dni', NumberType::class, array("attr" => array('placeholder' => 'Nro documento')))
            ->add('email', EmailType::class, array("attr" => array('placeholder' => 'casilla@email.com')))
            ->add('municipio', EntityType::class, array(
                                        'class' => 'CommonBundle:Partido',
                                        'query_builder' => function (EntityRepository $er) {
                                            return $er->createQueryBuilder('p')
                                                ->where('p.provincia = 1')
                                                ->orderBy('p.nombre', 'ASC');
                                        },
                                        'choice_label' => 'nombre',
                                        'placeholder' => 'Seleccione su municipio'
                                    )
                )
            //->add('dueDate', null, array('widget' => 'single_text'))
            //->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CommonBundle\Entity\Persona',
        ));
    }    
}