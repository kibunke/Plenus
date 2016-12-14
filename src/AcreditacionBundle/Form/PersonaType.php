<?php

namespace CommonBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use CommonBundle\Form\EmailType;
use CommonBundle\Form\DireccionType;
use CommonBundle\Form\TelefonoType;

class PersonaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nombre', 'text', array(
                    'label' => 'Nombre',
                ))
                ->add('apellido', 'text', array(
                    'label' => 'Apellido',
                ))
                ->add('email', 'email', array(
                    'label' => 'Email',
                ))
                ->add('documentoNro', 'integer', array(
                    'label' => 'Número',
                ))
                ->add('fechaNacimiento', 'birthday', array(
                    'label' => 'Fecha de Nacimiento',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy'
                ))
                ->add('documentoTipo', 'entity', array(
                    'class' => 'CommonBundle:TipoDocumento',
                    'label' => 'Tipo',
                    'placeholder' => 'Seleccione',
                ))
                ->add('sexo', 'choice', array(
                    'choices' => array(
                        'm' => 'Masculino',
                        'f' => 'Femenino',
                    ),
                    'label' => 'Género',
                    'placeholder' => 'Seleccione',
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CommonBundle\Entity\Persona'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'juegosba_commonbundle_persona';
    }

}
