<?php
namespace InscripcionBundle\Form\DataTransformer;
 
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use InscripcionBundle\Entity\Origen;
use InscripcionBundle\Entity\Escuela;
use InscripcionBundle\Entity\Municipio;
use InscripcionBundle\Entity\Otro;

class OrigenToTextTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (Origen) to a string (text).
     *
     * @param  Origen|null $object
     * @return string
     */
    public function transform($object)
    {
        if (null === $object) 
        {
            return "";
        }
        
        return $object->getNombre();
    }

    /**
     * Transforms a string (text) to an object (Origen).
     *
     * @param  string $text
     *
     * @return Origen | null
     *
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($text)
    {
        if (!$text) 
        {
            return null;
        }
        $text=explode('-',$text);
        $nombre=$text[0];
        $tipo=$text[1];
        $municipio=$this->om
                       ->getRepository('CommonBundle:Municipio')
                       ->find($text[2]);
        $object = $this->om
                       ->getRepository('InscripcionBundle:Origen')
                       ->findByNameAndTipoAndMunicipio($nombre,$tipo,$municipio)
        ;


        if (null === $object) 
        {
            switch ($tipo){
                case 'Municipio':
                        $object= new Municipio();
                    break;
                case 'Escuela':
                        $object= new Escuela();
                    break;
                case 'Otro':
                        $object= new Otro();
                    break;
                default : throw $this->createAccessDeniedException('No se puede completar la acción por datos erroneos.');
            }
            
            $object->setNombre($nombre);
            $object->setMunicipio($municipio);
        }

        return $object;
    }
}