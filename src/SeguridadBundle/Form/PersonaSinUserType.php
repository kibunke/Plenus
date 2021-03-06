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

class PersonaSinUserType extends PersonaCheckDataType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->add('email', EmailType::class, array(
                                                            'attr' => array('placeholder' => 'casilla@email.com')
                                                          )
                          
                         )
                    ->add('genero', EntityType::class, array(
                                                                'class' => 'ResultadoBundle:Genero',
                                                                'query_builder' => function (EntityRepository $er)
                                                                {
                                                                    return $er->createQueryBuilder('p')
                                                                              ->orderBy('p.nombre', 'ASC')
                                                                              ;
                                                                },
                                                                'choice_label' => 'nombre',
                                                                'placeholder'  => 'Seleccione su género',
                                                                'label'        => 'Género'
                                                            )
                         )
            ;
            
            return parent::buildForm($builder,$options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'CommonBundle\Entity\Persona'));
    }
}