<?php

namespace SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder
            ->add('usuario')
            ->add('password')
            ->add('nombre')
            ->add('apellido')
            //->add('salt')
            //->add('diagrama', new CommonBundle\Form\AdjuntoType(), array(
            //                                'required' => false
            //                        )
            //)
            ->add('activo', 'choice', array('choices'   => array('1' => 'Si', '0' => 'No'),'required'  => true, 'data' => $entity->getActivo()))
            //->add('aperfil')
            //->add('logueado')
            //->add('ip_login')
            //->add('ultimoLogin')
            //->add('ultimaOperacion')
            //->add('passwordGenerada')
            //->add('createdAt')
            //->add('updatedAt')
            ->add('localidad', 'entity', array(
                                                'class' => 'CommonBundle:Localidad',
                                                'property' => 'nombre',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                        return $er->createQueryBuilder('l')
                                                                                    ->join('l.partido','p')
                                                                                    ->where('p.provincia = :prov')
                                                                                    ->setParameter('prov', '1');
                                                                    },
                                                'multiple' => false,
                                            )
                  )
            //->add('roles')
            ->add('perfil')
            //->add('usuarioCreo')
            //->add('usuarioModifico')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SeguridadBundle\Entity\Usuario'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_seguridadbundle_usuario';
    }
}
