<?php

namespace SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PerfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('label' => 'Nombre'))
            ->add('legend', TextType::class, array('label' => 'Leyenda'))
            ->add('description', TextType::class, array('label' => 'Descripción'))
            ->add('roles', EntityType::class, array(
                                                        // query choices from this entity
                                                        'class' => 'SeguridadBundle:Role',
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
            ->add('availableForNewUsers', CheckboxType::class, array('label' => 'Disponible para Nuevos Usuarios', 'required' => false))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SeguridadBundle\Entity\Perfil',
        ));
    }
    
    public function getName()
    {
        return 'seguridadbundle_perfiltypeform';
    }
}