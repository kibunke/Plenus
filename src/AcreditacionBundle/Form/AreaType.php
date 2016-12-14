<?php

namespace AcreditacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use AcreditacionBundle\Form\AreaCategoriaPagoType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AreaType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nombre')
                ->add('descripcion')
                ->add('cupoMaxPersonal', 'text')
                ->add('cupoMaxHoteleria','text')
                ->add('cupoMaxTransporte','text')
                ->add('cupoMaxPresupuesto','text')
                ->add('cuposCategoriasPago', 'collection', array(
                    'type' => new AreaCategoriaPagoType(),
                    'allow_add' => true,
                    'by_reference' => false,
                ))
                ->add('funcionesPermitidas', 'entity', array(
                    'class' => 'AcreditacionBundle:FuncionJuegos',
                    'label' => 'Funciones Habilitadas',
                    'placeholder' => 'Búsque un responsable de área...',
                    'multiple' => true,
                ))
                ->add('usuariosResponsables', 'entity', array(
                    'class' => 'SeguridadBundle:Usuario',
                    'label' => 'Coordinadores',
                    'placeholder' => 'Búsque un responsable de área...',
                    'multiple' => true,
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AcreditacionBundle\Entity\Area'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'juegosba_acreditacionbundle_area';
    }

}
