<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CronogramaGeneralType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $security = $options['security'];
        $builder
            ->add('descripcion')
            ->add('fecha', 'datetime', array(
                                            'widget' => 'single_text',
                                            // this is actually the default format for single_text
                                            'format' => 'dd/MM/yyyy H:mm',
                                            'html5' => false,
                                        )
                )
            ->add('escenario','entity', array(
                                    'class' => 'ResultadoBundle:Escenario',
                                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er ){
                                                            return $er->createQueryBuilder('e')->orderBy('e.nombre');
                                                        },
                                    'multiple' => false,
                                    'required' => true                                   
                        )
                )
            ->add('eventos','entity', array(
                                    'class' => 'ResultadoBundle:Evento',
                                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er ) use($security){
                                                            $q = $er->createQueryBuilder('e')
                                                                ->where('e.soloInscribe = 0 OR e.soloInscribe IS NULL');
                                                            if (!$security->isGranted('ROLE_DIRECTOR')){
                                                                $q->andwhere('?1 MEMBER OF e.coordinadores')->setParameter(1, $security->getToken()->getUser()->getId());
                                                            }
                                                            return $q->orderBy('e.nombre');                                         
                                                            //return $er->createQueryBuilder('ev')
                                                            //            ->where('(ev.soloInscribe = 0 OR ev.soloInscribe IS NULL) AND ev.id IN (1,2,3,4,5,6,7,8,9)')
                                                            //;
                                                        },
                                    'multiple' => true,
                                    'required' => true                                   
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
            array(
                  'data_class' => 'ResultadoBundle\Entity\Cronograma',
                  'security' => null
                  )
            )
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_cronograma';
    }
}
