<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use InscripcionBundle\Form\DataTransformer\OrigenToTextTransformer;

class EquipoType extends AbstractType
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
            ->add('municipio', 'entity', array(
                                                'class' => 'CommonBundle:Partido',
                                                'property' => 'nombre',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                            return $er->createQueryBuilder('p')
                                                                                        ->where('p.provincia = 1')
                                                                                        ->orderBy('p.regionDeportiva');
                                                                    },
                                                'multiple' => false,
                                                'required' => true,
                                                'group_by'=> 'cruceRegional',
                                                'placeholder' => 'Seleccione'
                                            )
                  )
            ->add('participantes',
                    'bootstrap_collection',
                    array(
                        'type' => new ParticipanteType(),
                        'allow_add'          => true,
                        'allow_delete'       => true,
                        'by_reference' => false,
                        'add_button_text'    => 'Agregar Participante',
                        'delete_button_text' => 'Borrar Participante',
                        'sub_widget_col'     => 9,
                        'button_col'         => 3
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
            array('data_class' => 'ResultadoBundle\Entity\Equipo'))
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_equipo';
    }
}
