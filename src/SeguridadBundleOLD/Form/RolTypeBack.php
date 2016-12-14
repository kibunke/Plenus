<?php

namespace Equilatero\SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class RolType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $admin = $this->sc->isGranted('ROLE_ADMIN');
        
        $builder
            ->add('nombre','text', array(
                                          'attr' => array('widget_col' => 4)
                                          )
                  )
            ->add('perfiles','entity',array('multiple'      =>true,
                                            'expanded'      =>true,
                                            'class'         =>'SeguridadBundle:Perfil',
                                            'query_builder' => function(EntityRepository $er)
                                                             {
                                                                return $er->createQueryBuilder('e')->orderBy('e.nombre', 'ASC');
                                                              },
                                           'attr' => array('widget_col' => 4)                  
                                       )
                  )
            ->add('ordenGrupo','number', array(
                                          'attr' => array('widget_col' => 1)
                                          )
                  )
            ->add('ordenDentroGrupo','number', array(
                                          'attr' => array('widget_col' => 1)
                                          )
                  )

        ;
        
        if($admin)
        {
            $builder->add('soloAdmin','checkbox', array('label'=>'Solo para Administradores',
                                                        'required' => false,
                                                        'attr' => array('align_with_widget' => true),
                                                        )
                         );
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Equilatero\SeguridadBundle\Entity\Rol',
        ));
    }
    
    public function getName()
    {
        return 'equilatero_seguridadbundle_roltype';
    }
    
    private $sc;
    
    public function __construct($securityContext)
    {
      $this->sc = $securityContext;    
    }
}
