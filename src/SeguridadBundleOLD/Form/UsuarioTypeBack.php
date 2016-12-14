<?php

namespace Equilatero\SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UsuarioType extends AbstractType
{
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre','text', array(
                                      'attr' => array('widget_col' => 4)
                                     )
                 )
            ->add('apellido','text', array(
                                            'attr' => array('widget_col' => 4)
                                         )
                  )

            ->add('usuario','text', array(
                                          'label'=>'Nombre de Usuario',
                                          'attr' => array('widget_col' => 4)
                                          )
                  )
            ->add('password', 'repeated', array(
                                                'required' => false,
                                                'type' => 'password',
                                                'invalid_message' => 'Las contraseñas no coinciden.',
                                                'options' => array('label' => 'Password',
                                                                   'always_empty' => false,
                                                                   ),
                                                'attr' => array('widget_col' => 4)
                                          )
                  )
  
            ->add('activo','checkbox', array(
                                             'required' => false,
                                             'attr' => array('align_with_widget' => true)
                                            )
                  )
            ->add('logueado','checkbox', array(
                                             'required' => false,
                                             'attr' => array('align_with_widget' => true)
                                            )
                  )
            ->add('roles','entity',array(  'multiple'      =>true,
                                            'expanded'      =>true,
                                            'class'         =>'SeguridadBundle:Rol',
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Equilatero\SeguridadBundle\Entity\Usuario',
        ));
    }
    
    public function getName()
    {
        return 'equilatero_seguridadbundle_usuariotype';
    }
    

}
