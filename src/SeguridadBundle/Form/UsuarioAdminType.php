<?php

namespace SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\EntityRepository;

class UsuarioAdminType extends AbstractType
{
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        
        $builder
            ->add('perfil', EntityType::class, array(
                                        'class' => 'SeguridadBundle:Perfil',
                                        'query_builder' => function (EntityRepository $er) use($user) {
                                            $query = $er->createQueryBuilder('p');
                                            if(!$user->hasRole('ROLE_ADMIN'))
                                                $query->where('p.availableForNewUsers = 1');
                                            return $query->orderBy('p.name', 'ASC');
                                        },
                                        'choice_label' => 'name',
                                        'placeholder'  => 'para que vas a usar Plenus?',
                                        'attr'         => array('onchange' => '$.UsuarioEdit.actualizarCargos(this.value);')
                                    )
                  
                )
            ->add('cargo', EntityType::class, array(
                                        'class' => 'SeguridadBundle:Cargo',
                                        'query_builder' => function (EntityRepository $er) {
                                            return $er->createQueryBuilder('p')
                                                ->where('p.isActive = 1')
                                                ->orderBy('p.name', 'ASC');
                                        },
                                        'choice_label' => 'name',
                                        'placeholder' => 'Seleccionar Cargo...',
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