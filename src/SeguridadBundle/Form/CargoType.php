<?php

namespace SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class CargoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Nombre'))
            ->add('description', TextType::class, array('label' => 'Descripción'))
            ->add('perfiles', EntityType::class, array(
                                                        // query choices from this entity
                                                        'class' => 'SeguridadBundle:Perfil',
                                                        'query_builder' => function (EntityRepository $er) {
                                                            return $er->createQueryBuilder('u')
                                                                      ->where('u.isActive = 1')
                                                                      ->orderBy('u.name', 'ASC');
                                                        },
                                                        // use the User.username property as the visible option string
                                                        'choice_label' => 'name',
                                                    
                                                        // used to render a select box, check boxes or radios
                                                         'multiple' => true,
                                                         'expanded' => true,
                                                    )
                 )
            ->add('isActive', CheckboxType::class, array('label' => 'Activo', 'required' => false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SeguridadBundle\Entity\Cargo',
        ));
    }
    
    public function getName()
    {
        return 'seguridadbundle_cargotypeform';
    }
}