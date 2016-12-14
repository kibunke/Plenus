<?php

namespace TesoreriaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FondoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('abreviatura')
            ->add('monto', 'integer', array(
                                            'attr' => array(
                                                'step'=>'1',
                                                'min'=>'0'
                                            )
                                        )
                )
            ->add('entidad')
            ->add('descripcion')
            ->add('modeloRecibo')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TesoreriaBundle\Entity\Fondo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_tesoreriabundle_fondo';
    }
}
