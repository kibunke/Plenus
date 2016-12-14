<?php

namespace InscripcionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrigenType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            //->add('descripcion')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'InscripcionBundle\Entity\Origen'))
                 ->setRequired(array())
                 ->setAllowedTypes(array())
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_inscripcionbundle_origen';
    }
}
