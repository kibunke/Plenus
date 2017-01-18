<?php

namespace SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class UsuarioAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('perfil', EntityType::class, array(
                                        'class' => 'SeguridadBundle:Perfil',
                                        'query_builder' => function (EntityRepository $er) {
                                            return $er->createQueryBuilder('p')
                                                ->where('p.availableForNewUsers = 1')
                                                ->orderBy('p.name', 'ASC');
                                        },
                                        'choice_label' => 'legend',
                                        'placeholder'  => 'para que vas a usar Plenus?'
                                    )
                )
            ->add('isActive', CheckboxType::class, array('label' => 'Activo', 'required' => false))            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SeguridadBundle\Entity\Usuario',
        ));
    }
}