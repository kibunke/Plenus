<?php

namespace InscripcionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use Doctrine\Common\Persistence\ObjectManager;

use InscripcionBundle\Form\DataTransformer\OrigenToTextTransformer;

class PlanillaType extends AbstractType
{
    private $manager;
    private $tokenStorage;

    public function __construct(ObjectManager $manager,TokenStorage $tokenStorage)
    {
        $this->manager = $manager;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $entity = $builder->getData();
        $builder
            //->add('nombre', TextType::class, array("attr" => array('placeholder' => 'Ingrese el nombre del segmento')))
            //->add('descripcion', TextareaType::class, array("attr" => array('placeholder' => 'Ingrese la descripción del segmento')))
            ->add('municipio', EntityType::class, array(
                                                'class' => 'CommonBundle:Municipio',
                                                'choice_label' => 'nombre',
                                                'query_builder' => function(\Doctrine\ORM\EntityRepository $er )
                                                                    {
                                                                            return $er->createQueryBuilder('p')
                                                                                        ->where('p.provincia = 1')
                                                                                        ->orderBy('p.nombre');
                                                                    },
                                                'multiple' => false,
                                                'required' => true,
                                                //'placeholder' => 'Seleccione',
                                                'data' => $user->getPersona()->getMunicipio(),
                                                'disabled' => !$user->hasRole('ROLE_COORDINADOR') || !$user->hasRole('ROLE_ADMIN')
                                            )
                  )
            ->add('data', TextareaType::class, array("mapped" => false,"data" => json_encode($entity->getJson())))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'InscripcionBundle\Entity\Planilla',
        ));
    }
}