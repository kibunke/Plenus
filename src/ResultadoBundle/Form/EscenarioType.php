<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
            ->add('localidad', EntityType::class, array(
                                                'class' => 'CommonBundle:Localidad',
                                                'choice_label' => 'nombre',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                            return $er->createQueryBuilder('l')
                                                                                        ->where('l.municipio = 162')
                                                                                        ->orderBy('l.nombre');
                                                                    },
                                                'multiple' => false,
                                                'required' => true,
                                                'placeholder' => 'Seleccione'
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
            'data_class' => 'ResultadoBundle\Entity\Escenario'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_escenario';
    }
}
