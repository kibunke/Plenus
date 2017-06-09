<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;

class PlazaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder
                ->add('nombre')
                ->add('descripcion')
                ->add('equipo',EntityType::class, array(
                                                        'class'         => 'ResultadoBundle:Equipo',
                                                        'query_builder' => function(EntityRepository $er)use($entity)
                                                                           {
                                                                                //$param = $entity->getEquipo() ? $entity->getEquipo() : 0;
                                                                                //return $er->createQueryBuilder('eq')
                                                                                //          ->join('eq.evento','ev')
                                                                                //          ->join('eq.municipio','mu')
                                                                                //          ->where('ev = ?1 AND eq.id NOT IN (
                                                                                //                                            SELECT eq1.id
                                                                                //                                            FROM ResultadoBundle:Equipo eq1
                                                                                //                                            JOIN eq1.plazas com
                                                                                //                                            WHERE com.competencia = ?2 AND eq1 <> ?3
                                                                                //                                        )'
                                                                                //                  )
                                                                                //           ->orderBy('mu.regionDeportiva')
                                                                                //           ->setParameter(1, $entity->getCompetencia()->getEtapa()->getEvento())
                                                                                //           ->setParameter(2, $entity->getCompetencia())
                                                                                //           ->setParameter(3, $param)
                                                                                //           ;
                                                                            },
                                                        'multiple'    => false,
                                                        'required'    => false,
                                                        'placeholder' => 'Seleccione'                                    
                                                       )
                   )          
                ;         
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array('data_class' => 'ResultadoBundle\Entity\Plaza'))
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_plaza';
    }
}
