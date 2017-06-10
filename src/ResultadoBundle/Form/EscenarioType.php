<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
<<<<<<< Updated upstream
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
=======
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
>>>>>>> Stashed changes

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
<<<<<<< Updated upstream
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
=======
                                                          'class'         => 'CommonBundle:Localidad',
                                                          'property'      => 'nombre',
                                                          'query_builder' => function(EntityRepository $er )
                                                                             {
                                                                                return $er->createQueryBuilder('p')
                                                                                          ->where('p.partido = 162')
                                                                                          ->orderBy('p.nombre');
                                                                             },
                                                           'multiple'      => false,
                                                           'required'      => true,
                                                           'placeholder'   => 'Seleccione'
                                                        )
>>>>>>> Stashed changes
                  )
            ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
<<<<<<< Updated upstream
    public function setDefaultOptions(OptionsResolverInterface $resolver)
=======
    public function configureOptions(OptionsResolverInterface $resolver)
>>>>>>> Stashed changes
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
