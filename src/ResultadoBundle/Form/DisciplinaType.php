<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DisciplinaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array('required' => true))
            ->add('abreviatura')
            ->add('descripcion')
            ->add('armarNombreRecursivo')
            ->add('eventos')
            ->add('parametros')
            ->add('padre',  EntityType::class, array(
                                                'class' => 'ResultadoBundle:Disciplina',
                                                'choice_label' => 'nombreCompleto',
                                                //'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                //                    {
                                                //                        return $er->createQueryBuilder('u')
                                                //                                    ->join('u.funcion','f')
                                                //                                    ->where('f.nombre <> :fun')
                                                //                                    ->orderby('u.apellido','ASC')
                                                //                                    ->setParameter('fun', 'Consulta municipal');
                                                //                    },
                                                'multiple' => false,
                                                'required' => false,
                                                'empty_data'  => null
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
            'data_class' => 'ResultadoBundle\Entity\Disciplina'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'plenus_resultadobundle_disciplina';
    }
}
