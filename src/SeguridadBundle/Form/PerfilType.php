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
            ->add('orden', TextType::class, array('label' => 'Orden'))
            ->add('roles', EntityType::class, array(
                                                        'class' => 'SeguridadBundle:Role',
                                                        'query_builder' => function (EntityRepository $er) {
                                                            return $er->createQueryBuilder('u')
                                                                      ->where('u.isActive = 1')
                                                                      ->orderBy('u.name', 'ASC');
                                                        },
                                                        'choice_label' => 'name',
                                                        'multiple' => true,
                                                        'expanded' => true,
                                                    )
                 )
            ->add('isActive', CheckboxType::class, array('label' => 'Activo', 'required' => false))
            ->add('muestraMunicipio', CheckboxType::class, array('label' => 'Obligar a seleccionar Municipio', 'required' => false))
            ->add('municipio', EntityType::class, array(
                                                        'label' => 'Municipio por defecto',
                                                        'class' => 'CommonBundle:Municipio',
                                                        'query_builder' => function (EntityRepository $er) {
                                                            return $er->createQueryBuilder('u')
                                                                      ->orderBy('u.nombre', 'ASC');
                                                        },
                                                        //'placeholder'  => 'Cualquiera',
                                                        // use the User.username property as the visible option string
                                                        'choice_label' => 'nombre',
                                                    )
                 )
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