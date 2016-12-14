<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AcreditacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AcreditacionBundle\Form\AvatarType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use SeguridadBundle\Entity\Usuario;

/**
 * Description of CredencialEspecialType
 *
 * @author kibunke
 */
class CredencialEspecialType extends AbstractType {

    /**
     * var Usuario $user;
     */
    protected $user = null;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $security = $options['security'];
        $builder
                ->add('nombre')
                ->add('apellido')
                ->add('documentoNro')
                ->add('documentoTipo', 'entity', array(
                    'class' => 'CommonBundle:TipoDocumento',
                    'placeholder' => 'Seleccione'
                ))
                ->add('area', 'entity', array(
                    'class' => 'AcreditacionBundle:Area',
                    'label' => 'Area',
                    'placeholder' => 'Seleccione',
                    'query_builder' => function(EntityRepository $er) use ($security) {
                        if ($security->isGranted('ROLE_ADMIN')) {
                            return $er->createQueryBuilder('area');
                        }
                        return $er->createQueryBuilder('area')
                                ->innerJoin('area.usuariosResponsables', 'user')
                                ->where('user.id = :param')
                                ->setParameter(':param', $this->getUser()->getId());
                    }
                ))
                ->add('funcion', 'entity', array(
                    'class' => 'AcreditacionBundle:FuncionJuegos',
                    'label' => 'Función',
                    'placeholder' => 'Seleccionar área',
                ))
                ->add('accesoSector1', 'checkbox', array(
                    'required' => false,
                ))
                ->add('accesoSector2', 'checkbox', array(
                    'required' => false,
                ))
                ->add('accesoSector3', 'checkbox', array(
                    'required' => false,
                ))
                ->add('accesoSector4', 'checkbox', array(
                    'required' => false,
                ))
                ->add('accesoSector5', 'checkbox', array(
                    'required' => false,
                ))
                ->add('letraIdentificacion', 'choice', array(
                    'label' => 'Letra',
                    'placeholder' => 'Seleccione',
                    'required' => true,
                    'choices' => array(
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                        'J' => 'J',
                        'M' => 'M',
                        'P' => 'P',
                        '+' => '+',
                    )
                ))
                ->add('avatar', new AvatarType(), array(
                    'required' => false,
                ))
                ->add('avatarCapture', 'textarea', array(
                    'mapped' => false,
                    'required' => false,
        ));
    }

    public function __construct(Usuario $user) {
        $this->setUser($user);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AcreditacionBundle\Entity\CredencialEspecial',
            'security' => null
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'juegosba_acreditacionbundle_credencialespecial';
    }

    function getUser() {
        return $this->user;
    }

    function setUser($user) {
        $this->user = $user;
    }

}
