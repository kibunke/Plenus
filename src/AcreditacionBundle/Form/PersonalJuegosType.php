<?php

namespace AcreditacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CommonBundle\Form\PersonaType;
use AcreditacionBundle\Form\AvatarType;
use AcreditacionBundle\Form\DatosOperativoType;
use TesoreriaBundle\Form\DatosTesoreriaType;
use Doctrine\ORM\EntityRepository;
use AcreditacionBundle\Entity\PersonalJuegos;
use SeguridadBundle\Entity\Usuario;

class PersonalJuegosType extends AbstractType {

    /**
     * var Usuario $user;
     */
    protected $user = null;

    /**
     * var PersonalJuegos $personalJuegosl;
     */
    protected $personalJuegos = null;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $security = $options['security'];
        $builder
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
                ->add('activo', 'checkbox', array(
                    'required' => false,
                ))
                ->add('avatar', new AvatarType())
                ->add('avatarCapture', 'textarea', array(
                    'mapped' => false,
                    'required' => false,
                ))
                ->add('datosPersonales', new PersonaType())
                ->add('datosOperativo', new DatosOperativoType())
                ->add('datosTesoreria', new DatosTesoreriaType($this->getPersonalJuegos()))
        ;
    }

    public function __construct(Usuario $user, $personalJuegos) {
        $this->user = $user;
        $this->personalJuegos = $personalJuegos;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AcreditacionBundle\Entity\PersonalJuegos',
            'security' => null
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'juegosba_acreditacionbundle_personaljuegos';
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    function getPersonalJuegos() {
        return $this->personalJuegos;
    }

    function setPersonalJuegos($personalJuegos) {
        $this->personalJuegos = $personalJuegos;
    }

}
