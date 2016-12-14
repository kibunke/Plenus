<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlazaZonaType extends AbstractType
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
            ->add('equipo',
                  'entity', array(
                                    'class' => 'ResultadoBundle:Equipo',
                                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er ) use($entity){
                                                            $param = $entity->getEquipo() ? $entity->getEquipo() : 0;
                                                            return $er->createQueryBuilder('eq')
                                                                        ->join('eq.evento','ev')
                                                                        ->join('eq.municipio','mu')
                                                                        ->where('ev = ?1 AND eq.id NOT IN (
                                                                                                        SELECT eq1.id
                                                                                                        FROM ResultadoBundle:Equipo eq1
                                                                                                        JOIN eq1.plazas com
                                                                                                        WHERE com.competencia = ?2 AND eq1 <> ?3
                                                                                                    )'
                                                                                )
                                                                        ->orderBy('mu.regionDeportiva')
                                                                        ->setParameter(1, $entity->getCompetencia()->getEtapa()->getEvento())
                                                                        ->setParameter(2, $entity->getCompetencia())
                                                                        ->setParameter(3, $param)
                                                            ;
                                                        },
                                    'multiple' => false,
                                    'required' => false,
                                    'placeholder' => 'Seleccione'                                    
                )
            )          
            ;         
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array('data_class' => 'ResultadoBundle\Entity\PlazaZona'))
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
