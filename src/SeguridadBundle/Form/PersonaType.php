<?php

namespace SeguridadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PersonaType extends PersonaCheckDataType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->add('avatar', TextType::class, array(
                                                'attr' => array('placeholder' => 'Cuenta de Linkedin'),
                                                'required' => false)
                         )
                    ->add('email', EmailType::class, array('attr' => array('placeholder' => 'casilla@email.com'))
            
            
            );
            
            return parent::buildForm($builder,$options);
        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CommonBundle\Entity\Persona',
        ));
    }
}