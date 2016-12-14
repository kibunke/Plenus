<?php

namespace TesoreriaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class DatosTesoreriaType extends AbstractType {

    protected $personalJuegos = null; 

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('empleadoPublico', 'choice', array(
                    'choices' => array(
                        'NO' => 'NO',
                        'SUBSECRETARIA DE DEPORTES' => 'SUBSECRETARIA DE DEPORTES',
                        "AUTORIDAD DEL AGUA" => 'AUTORIDAD DEL AGUA',
                        "CENTRO UNICO COORDINADOR DE ABLACION E IMPLANTE" => "CENTRO UNICO COORDINADOR DE ABLACION E IMPLANTE",
                        "CONTADURIA GENERAL" => "CONTADURIA GENERAL",
                        "DIRECION GENERAL DE CULTURA Y EDUCACION" => "DIRECION GENERAL DE CULTURA Y EDUCACION",
                        "ESCRIBANIA GENERAL" => "ESCRIBANIA GENERAL",
                        "INTITUTO DE LA VIVIENDA" => "INTITUTO DE LA VIVIENDA",
                        "INSTITUTO DE OBRA MEDICO ASISTENCIAL (I.O.M.A)" => "INSTITUTO DE OBRA MEDICO ASISTENCIAL (I.O.M.A)",
                        "INSTITUTO DE PREVISION SOCIAL (I.P.S)" => "INSTITUTO DE PREVISION SOCIAL (I.P.S)",
                        "INSTITUTO PROVINCIAL DE LOTERIA Y CASINOS" => "INSTITUTO PROVINCIAL DE LOTERIA Y CASINOS",
                        "MINISTERIO DE LA PRODUCCION CIENCIA Y TECNOLOGIA" => "MINISTERIO DE LA PRODUCCION CIENCIA Y TECNOLOGIA",
                        "MINISTERIO DE SALUD" => "MINISTERIO DE SALUD",
                        "MINISTERIO DE TRABAJO" => "MINISTERIO DE TRABAJO",
                        "MINISTERIO DE ASUNTOS AGRARIOS" => "MINISTERIO DE ASUNTOS AGRARIOS",
                        "MINISTERIO DE DESARROLLO SOCIAL" => "MINISTERIO DE DESARROLLO SOCIAL",
                        "MINISTERIO DE ECONOMIA" => "MINISTERIO DE ECONOMIA",
                        "MINISTERIO DE INFRAESTRUCTURA" => "MINISTERIO DE INFRAESTRUCTURA",
                        "MINISTERIO DE JEFATURA DE GABINETE DE MINISTROS" => "MINISTERIO DE JEFATURA DE GABINETE DE MINISTROS",
                        "MINISTERIO DE JUSTICIA Y SEGURIDAD (JUSTICIA)" => "MINISTERIO DE JUSTICIA Y SEGURIDAD (JUSTICIA)",
                        "MINISTERIO DE JUSTICIA Y SEGURIDAD (SEGURIDAD)" => "MINISTERIO DE JUSTICIA Y SEGURIDAD (SEGURIDAD)",
                        "O.C.E.B.A" => 'O.C.E.B.A',
                        "ORGANISMO PROVINCIAL PARA EL DESARROLLO SOSTENIBLE" => "ORGANISMO PROVINCIAL PARA EL DESARROLLO SOSTENIBLE",
                        "PODER JUDICIAL" => "PODER JUDICIAL",
                        "SECRETARIA DE TURISMO" => "SECRETARIA DE TURISMO",
                        "SECRETARIA GENERAL DE LA GOBERNACION" => "SECRETARIA GENERAL DE LA GOBERNACION",
                        "SECRETARIA LEGAL Y TECNICA" => "SECRETARIA LEGAL Y TECNICA",
                        "SECRETARIA GENERAL" => "SECRETARIA GENERAL",
                        'OTRO' => 'OTRO',
                    ),
                    'choices_as_values' => true,
                    'data' => ($this->getPersonalJuegos()->getDatosTesoreria() == null)?'NO':strtoupper($this->getPersonalJuegos()->getDatosTesoreria()->getEmpleadoPublico()),
                    'expanded' => false,
                    'multiple' => false,
                    'label' => 'Empleado Provincial',
                ))
                ->add('legajo', 'text', array(
                    'required' => false,
                ))
                ->add('cbu', 'text', array(
                    'required' => false,
                    'label' => 'CBU Banco Provincia de Buenos Aires',
                ))
                ->add('categoriaPago', 'entity', array(
                    'class' => 'TesoreriaBundle:CategoriaPago',
                    'label' => 'Categoría',
                    'placeholder' => 'Seleccione',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('cat')
                                ->orderBy('cat.nombre', 'ASC');
                    }
                ))
                ->add('pagoProvincia', 'entity', array(
                    'class' => 'CommonBundle:Provincia',
                    'label' => 'Provincia - Pago',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('provincia')
                                ->where('provincia.id = :param')
                                ->setParameter(':param', 1);
                    },
                ))
                ->add('pagoPartido', 'entity', array(
                    'class' => 'CommonBundle:Partido',
                    'label' => 'Partido - Pago',
                    'query_builder' => function(EntityRepository $er) {
                        // SOLO LA PLATA y GENERAL PUEYREDON
                        return $er->createQueryBuilder('partido')
                                ->where('partido.id IN (128,162)');
                    },
                ))
                ->add('pagoEspecifico', 'number', array(
                    'required' => false,
                    'label' => 'Monto',
        ));
    }

    public function __construct($personalJuegos) {
        $this->personalJuegos = $personalJuegos;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'TesoreriaBundle\Entity\DatosTesoreria'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'juegosba_tesoreriabundle_datostesoreria';
    }

    function getPersonalJuegos() {
        return $this->personalJuegos;
    }

    function setPersonalJuegos($personalJuegos) {
        $this->personalJuegos = $personalJuegos;
    }
}
