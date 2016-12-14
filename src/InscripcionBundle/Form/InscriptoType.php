<?php

namespace InscripcionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use InscripcionBundle\Form\DataTransformer\OrigenToTextTransformer;

class InscriptoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $security = $options['security'];
        $entityManager = $options['em'];
        $transformer = new OrigenToTextTransformer($entityManager);
        $mascActive=true;
        $femActive=true;
        if ($entity->getEvento()){
            if ($entity->getEvento()->getGenero()->getNombre()=='Femenino'){
                $mascActive=false;
            }elseif ($entity->getEvento()->getGenero()->getNombre()=='Masculino'){
                $femActive=false;
            }
        }
        $builder
            ->add('evento', 'entity', array(
                                                'class' => 'ResultadoBundle:Evento',
                                                'property' => 'nombreCompleto',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er ) use ($security,$entity)
                                                                    {
                                                                        if ($entity->getEvento()){
                                                                            return $er->createQueryBuilder('e')
                                                                                        ->where('e.inscribe = 1 AND e = ?1')
                                                                                        ->setParameter(1, $entity->getEvento())
                                                                                        ->orderBy('e.nombre');
                                                                            
                                                                        }
                                                                        return $er->getAllByUserAndInscribeBuilder($security);    
                                                                    },
                                                'multiple' => false,
                                                'required' => true,
                                                'placeholder' => ($entity->getEvento())?'':'Seleccione'
                                            )
                  )
            ->add('municipio', 'entity', array(
                                                'class' => 'CommonBundle:Partido',
                                                'property' => 'nombre',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                            return $er->createQueryBuilder('p')
                                                                                        ->where('p.provincia = 1')
                                                                                        ->orderBy('p.nombre');
                                                                    },
                                                'multiple' => false,
                                                'required' => true,
                                                'placeholder' => 'Seleccione'
                                            )
                  )
            ->add('cantidadMasculinos','integer',array(
                                                        'required' => true,
                                                        'attr' => array(
                                                                        'min' => 0
                                                        ),
                                                        'read_only' => !$mascActive
                            )
                  )
            ->add('cantidadFemeninos','integer',array(
                                                        'required' => true,
                                                        'attr' => array(
                                                                        'min' => 0
                                                        ),
                                                        'read_only' => !$femActive
                            )
                  )
            ->add('tipoOrigen', 'choice', array(
                                            'choices'  => array('' => 'Seleccione',
                                                                'Municipio' => 'Municipio',
                                                                'Escuela' => 'Escuela',
                                                                'Otro' => 'Otra Institución'),
                                            'required' => true,
                                            'mapped' => false,
                                            'data' => $entity->getOrigen()?$entity->getOrigen()->getClass():'',
                                            'disabled' => false,
                                    )
                  )
            ->add(  $builder->create( 'origen', 'text',array('read_only' => false) )->addModelTransformer( $transformer ) ) 
            ->add('descripcion');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'InscripcionBundle\Entity\Inscripto'))
                 ->setRequired(array('em','security'))
                 ->setAllowedTypes(array())
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_inscripcionbundle_inscripto';
    }
}
