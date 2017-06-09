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

class CronogramaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entity = $builder->getData();
        $builder
            ->add('descripcion')
            ->add('fecha', DateTimeType::class, array(
                                                        'widget' => 'single_text',
                                                        'format' => 'dd/MM/yyyy H:mm',
                                                        'html5'  => false,
                                                     )
                )
            ->add('escenario',EntityType::class, array(
                                                        'class'         => 'ResultadoBundle:Escenario',
                                                        'query_builder' => function(EntityRepository $er )
                                                                           {
                                                                                return $er->createQueryBuilder('e')->orderBy('e.nombre');
                                                                           },
                                                        'multiple' => false,
                                                        'required' => true                                   
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
            array('data_class' => 'ResultadoBundle\Entity\Cronograma'))
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
