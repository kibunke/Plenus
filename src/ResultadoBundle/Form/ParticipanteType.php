<?php

namespace ResultadoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipanteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('apellido')
            ->add('documentoTipo','entity', array(
                                                    'class' => 'CommonBundle:TipoDocumento'
                                            )
            )
            ->add('documentoNro')
            ->add('rol', 'choice', array(
                                    'choices' => array(
                                                       'participante' => 'Participante',
                                                       //'acompaniante' => 'Acompañante',
                                                       //'tecnico' => 'Director Técnico',
                                                       //'delegado' => 'Delegado'
                                                ),
                                    'preferred_choices' => array('participante'),
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
            array('data_class' => 'ResultadoBundle\Entity\Participante'))
        ;        
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'juegosba_resultadobundle_participante';
    }
}
