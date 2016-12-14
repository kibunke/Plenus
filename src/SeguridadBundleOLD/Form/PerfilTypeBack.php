<?php

namespace Equilatero\SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PerfilType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre','text', array(
                                          'attr' => array('widget_col' => 4)
                                        )
                  )
            ->add('roles','entity',array('label' => 'Roles de Usuario',
                                                    'multiple' =>true,
                                                    'expanded' =>true,
                                                    'class'    =>'SeguridadBundle:Rol',
                                                    'query_builder' => function(EntityRepository $er)
                                                                               {
                                                                                return $er->createQueryBuilder('e')
                                                                                          ->addOrderBy('e.ordenGrupo')
                                                                                          ->addOrderBy('e.ordenDentroGrupo')
                                                                                          ;
                                                                               },
                                     )
                  )
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Equilatero\SeguridadBundle\Entity\Perfil'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'equilatero_seguridadbundle_perfil';
    }
}
