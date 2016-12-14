<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EscenarioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('descripcion')
            ->add('calle')
            ->add('numero')
            ->add('entreCalle1')
            ->add('entreCalle2')
            ->add('esquina')
            ->add('latLng')
            ->add('localidad', 'entity', array(
                                                'class' => 'CommonBundle:Localidad',
                                                'property' => 'nombre',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                            return $er->createQueryBuilder('p')
                                                                                        ->where('p.partido = 162')
                                                                                        ->orderBy('p.nombre');
                                                                    },
                                                'multiple' => false,
                                                'required' => true,
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
            array('data_class' => 'ResultadoBundle\Entity\Escenario'))
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_escenario';
    }
}
