<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlazaCopaType extends AbstractType
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
                                                            $partido = $entity->getPartidos()[0];
                                                            $otraPlaza = $partido->getContrincante($entity);
                                                            //echo $partido->getNivel()."=".$otraPlaza->getId()."-".$entity->getId();die();
                                                            return $er->createQueryBuilder('eq')
                                                                        ->join('eq.evento','ev')
                                                                        ->join('eq.municipio','mu')
                                                                        ->where('ev = ?1 AND eq.id NOT IN (
                                                                                                        SELECT eq1.id
                                                                                                        FROM ResultadoBundle:PlazaCopa plz
                                                                                                        JOIN plz.equipo eq1
                                                                                                        LEFT JOIN plz.partidosLocal parl
                                                                                                        LEFT JOIN plz.partidosVisitante parv
                                                                                                        WHERE (parl.nivel = ?2 OR parv.nivel = ?2)
                                                                                                    )'
                                                                                                    )
                                                                        ->orderBy('mu.regionDeportiva')
                                                                        ->setParameter(1, $entity->getCompetencia()->getEtapa()->getEvento())
                                                                        ->setParameter(2,$partido->getNivel())
                                                                        //->setParameter(3, $param)
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
            array('data_class' => 'ResultadoBundle\Entity\PlazaCopa'))
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
