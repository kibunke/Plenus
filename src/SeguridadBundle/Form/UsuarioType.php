<?php

namespace SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use CommonBundle\Form\PersonaType;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array("attr" => array('placeholder' => 'Ingrese un nombre de usuario')))
            ->add('persona', PersonaType::class)
            ->add('perfil', EntityType::class, array(
                                        'class' => 'SeguridadBundle:Perfil',
                                        'query_builder' => function (EntityRepository $er) {
                                            return $er->createQueryBuilder('p')
                                                ->where('p.availableForNewUsers = 1')
                                                ->orderBy('p.name', 'ASC');
                                        },
                                        'choice_label' => 'legend',
                                        'placeholder'  => 'Seleccione su perfil de usuario',
                                        'attr'         => array('onchange' => 'Login.validarMunicipio(this.value);')
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