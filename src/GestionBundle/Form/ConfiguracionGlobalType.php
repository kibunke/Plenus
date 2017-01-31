<?php

namespace GestionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ConfiguracionGlobalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isActive', CheckboxType::class, array('label' => 'Activa', 'required' => false))
            ->add('isResetPasswordActive', CheckboxType::class, array('label' => 'Reset Password Activa', 'required' => false))
            ->add('isNewAccountActive', CheckboxType::class, array('label' => 'Nueva Cuenta Activa', 'required' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'GestionBundle\Entity\ConfiguracionGlobal'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_gestionbundle_configuracionglobal';
    }
}
