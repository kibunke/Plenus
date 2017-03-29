<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InscripcionBundle\Entity\Institucion
 * @ORM\Table(name="Institucion")
 * @ORM\Entity(repositoryClass="InscripcionBundle\Entity\Repository\InstitucionRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "Escuela"   = "Escuela",
 *                          "Club"      = "Club"
 *                      })
 */

abstract class Institucion
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $nombre
     *
     * @ORM\Column(name="nombre", type="string", length=150)
     */
    private $nombre;
 
     /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;
    
    /**
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Municipio")
     * @ORM\JoinColumn(name="municipio", referencedColumnName="id")
     */       
    private $municipio;
 
    /**
     * @var string $domicilio
     *
     * @ORM\Column(name="domicilio", type="string", length=150)
     */
    private $domicilio;
    
    /**
     * @var string $telefono
     *
     * @ORM\Column(name="telefono", type="string", length=150, nullable=true)
     */
    private $telefono;
    
    /**
     * @var string $responsableNombre
     *
     * @ORM\Column(name="responsableNombre", type="string", length=100)
     */
    protected $responsableNombre;
    
    /**
     * @var string $responsableApellido
     *
     * @ORM\Column(name="responsableApellido", type="string", length=100)
     */
    protected $responsableApellido;
    
    /**
     * @var string $responsableDni
     *
     * @ORM\Column(name="responsableDni", type="string", length=100)
     */
    protected $responsableDni;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="id")
     */       
    private $createdBy;

    /**
     * @var datetime $updatedAt
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="updatedBy", referencedColumnName="id")
     */   
    private $updatedBy;    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    
    /**
     * __toString
     */    
    public function __toString()
    {
        return $this->getNombre();
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Origen
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Get icono
     *
     * @return string 
     */
    public function getIcono()
    {
        return "fa fa-user";
    }

    /**
     * Set color
     *
     * @param string $color
     * @return Origen
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Origen
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Origen
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Origen
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set municipio
     *
     * @param \CommonBundle\Entity\Municipio $municipio
     * @return Origen
     */
    public function setMunicipio(\CommonBundle\Entity\Municipio $municipio = null)
    {
        $this->municipio = $municipio;

        return $this;
    }

    /**
     * Get municipio
     *
     * @return \CommonBundle\Entity\Municipio 
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Origen
     */
    public function setCreatedBy(\SeguridadBundle\Entity\Usuario $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \SeguridadBundle\Entity\Usuario 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \SeguridadBundle\Entity\Usuario $updatedBy
     * @return Origen
     */
    public function setUpdatedBy(\SeguridadBundle\Entity\Usuario $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \SeguridadBundle\Entity\Usuario 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
    
    /**
     * Get json
     *
     * @return json
     */
    public function getJson()
    {
        return array(
                    "id" => $this->getId(),
                    "tipo" => $this->getDiscr(),
                    "nombre" => $this->getNombre(),
                    "domicilio" => $this->getDomicilio(),
                    "telefono" => $this->getTelefono(),
                    "responsable" => array(
                        "nombre" => $this->getResponsableNombre(),
                        "apellido" => $this->getResponsableApellido(),
                        "dni" => $this->getResponsableDni()
                    )
                );
    }

    /**
     * Get json
     *
     * @return json
     */
    static function getInstance($user,$json)
    {
        //var_dump($json);
        if (strlen($json->nombre) > 2 && strlen($json->domicilio) > 2){
            if (strlen($json->responsable->nombre) > 2 && strlen($json->responsable->apellido) > 2 && strlen($json->responsable->dni) > 6){
                if ($json->tipo == 'inscripcionInstitucionalEscuela')
                    $institucion = new Escuela($user);
                else
                    $institucion = new Club($user);
                    
                
                return $institucion->load($json);
            }else{
                throw new \Exception('Plenus: La institucion no se pudo crear. Los datos del responsable estan incompletos o son erroneos.');
            }
        }else{
            throw new \Exception('Plenus: La institucion no se pudo crear. Los datos estan incompletos o son erroneos.');
        }
        //return $institucion;
    }    
    
    public function load($json)
    {
        $this->setNombre($json->nombre);
        $this->setDomicilio($json->domicilio);
        $this->setTelefono($json->telefono);
        $this->setResponsableNombre($json->responsable->nombre);
        $this->setResponsableApellido($json->responsable->apellido);
        $this->setResponsableDni($json->responsable->dni);
        return $this;
    }
    /**
     * Set domicilio
     *
     * @param string $domicilio
     *
     * @return Institucion
     */
    public function setDomicilio($domicilio)
    {
        $this->domicilio = $domicilio;

        return $this;
    }

    /**
     * Get domicilio
     *
     * @return string
     */
    public function getDomicilio()
    {
        return $this->domicilio;
    }

    /**
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Institucion
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set responsableNombre
     *
     * @param string $responsableNombre
     *
     * @return Institucion
     */
    public function setResponsableNombre($responsableNombre)
    {
        $this->responsableNombre = $responsableNombre;

        return $this;
    }

    /**
     * Get responsableNombre
     *
     * @return string
     */
    public function getResponsableNombre()
    {
        return $this->responsableNombre;
    }

    /**
     * Set responsableApellido
     *
     * @param string $responsableApellido
     *
     * @return Institucion
     */
    public function setResponsableApellido($responsableApellido)
    {
        $this->responsableApellido = $responsableApellido;

        return $this;
    }

    /**
     * Get responsableApellido
     *
     * @return string
     */
    public function getResponsableApellido()
    {
        return $this->responsableApellido;
    }  

    /**
     * Set responsableDni
     *
     * @param string $responsableDni
     *
     * @return Institucion
     */
    public function setResponsableDni($responsableDni)
    {
        $this->responsableDni = $responsableDni;

        return $this;
    }

    /**
     * Get responsableDni
     *
     * @return string
     */
    public function getResponsableDni()
    {
        return $this->responsableDni;
    }
}
