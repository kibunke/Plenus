<?php

namespace AcreditacionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use AcreditacionBundle\Form\EventListener\RangoFechas;

class DatosOperativoType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('necesitaTransporte', 'checkbox', array(
                    'label' => '¿Necesita?',
                    'required' => false,
                ))
                ->add('fechaIdaTransporte', 'date', array(
                    'input' => 'datetime',
                    'label' => 'Ida',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                ))
                ->add('idaOrigen', 'entity', array(
                    'class' => 'CommonBundle:Partido',
                    'label' => 'Ida: Origen',
                    'required' => false,
                    'placeholder' => 'NINGUNO',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('p')->where('p.id = :op1')->orWhere('p.id = :op2')->setParameter(':op1', 128)->setParameter(':op2', 198);
                    }
                ))
                ->add('fechaRegresoTransporte', 'date', array(
                    'input' => 'datetime',
                    'label' => 'Regreso',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                ))
                ->add('regresoDestino', 'entity', array(
                    'class' => 'CommonBundle:Partido',
                    'label' => 'Regreso: Destino',
                    'required' => false,
                    'placeholder' => 'NINGUNO',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('p')->where('p.id = :op1')->orWhere('p.id = :op2')->setParameter(':op1', 128)->setParameter(':op2', 198);
                    }
                ))
                ->add('esPersonalGestion', 'checkbox', array(
                    'required' => false,
                ))
                ->add('vianda', 'checkbox', array(
                    'required' => false,
                ))->add('certificado140908', 'checkbox', array(
                    'required' => false,
                ))
                ->add('certificadoEstablecimientoPrivado', 'checkbox', array(
                    'required' => false,
                ))
                ->add('certificadoLaboral', 'checkbox', array(
                    'required' => false,
                ))
                ->add('talleIndumentaria', 'choice', array(
                    'placeholder' => 'Elegir...',
                    'required' => false,
                    'choices' => array(
                        'XS' => 'XS',
                        'S' => 'S',
                        'M' => 'M',
                        'L' => 'L',
                        'XL' => 'XL',
                        'XXL' => 'XXL',
                    )
                ))
                ->add('necesitaHospedaje', 'checkbox', array(
                    'required' => false,
                ))
                ->add('fechaIngresoHospedaje', 'date', array(
                    'input' => 'datetime',
                    'label' => 'Fecha de checkin del hotel',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                ))
                ->add('fechaEgresoHospedaje', 'date', array(
                    'input' => 'datetime',
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'label' => 'Fecha de checkout del hotel',
                    'required' => false,
        ));
        $builder->addEventSubscriber(new RangoFechas());
    }

 

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AcreditacionBundle\Entity\DatosOperativo'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'juegosba_acreditacionbundle_datosoperativo';
    }

   
}
