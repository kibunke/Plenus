<?php

namespace TesoreriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TesoreriaBundle\Entity\Entidad
 * @ORM\Table(name="Entidad")
 * @ORM\Entity()
 */
class Entidad {
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
     * @ORM\Column(name="nombre", type="string")
     */
    private $nombre;
    
    /**
     * @var string $descripcion
     * 
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @var string $razonSocial
     * 
     * @ORM\Column(name="razonSocial", type="string")
     */
    private $razonSocial;
    
    /**
     * @var string $datosFiscales
     * 
     * @ORM\Column(name="datosFiscales", type="string")
     */
    private $datosFiscales;
    
    /**
     * @var string $domicilioFiscal
     * 
     * @ORM\Column(name="domicilioFiscal", type="string")
     */
    private $domicilioFiscal;
    
    /**
     * @var $logo
     * 
     * @ORM\Column(name="logo", type="text")
     */
    private $logo;
    
    /**
     * @var ArrayCollection $fondos
     * 
     * @ORM\OneToMany(targetEntity="Fondo", mappedBy="entidad")
     */
    private $fondos;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;
    
    /**
     * @var SeguridadBundle\Entity\Usuario $createdBy
     * 
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
     * @var SeguridadBundle\Entity\Usuario $updatedBy
     * 
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="updatedBy", referencedColumnName="id")
     */   
    private $updatedBy;

    public function __construct() {
        $this->fondos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();
    }
    
    public function __toString() {
        return $this->razonSocial;
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
     * @return Federacion
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Federacion
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
     * @return Federacion
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
     * @return Federacion
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
     * Add fondo
     *
     * @param \TesoreriaBundle\Entity\Fondo $fondo
     * @return Fondo
     */
    public function addFondo(\TesoreriaBundle\Entity\Fondo $fondo)
    {
        $this->fondos[] = $fondo;

        return $this;
    }

    /**
     * Remove fondos
     *
     * @param \TesoreriaBundle\Entity\Fondo $fondo
     */
    public function removeFondo(\TesoreriaBundle\Entity\Fondo $fondo)
    {
        $this->fondos->removeElement($fondo);
    }

    /**
     * Get fondos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFondos()
    {
        return $this->fondos;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Federacion
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
     * @return Federacion
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
     * Set razonSocial
     *
     * @param string $razonSocial
     * @return Entidad
     */
    public function setRazonSocial($razonSocial)
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string 
     */
    public function getRazonSocial()
    {
        return $this->razonSocial;
    }

    /**
     * Set datosFiscales
     *
     * @param string $datosFiscales
     * @return Entidad
     */
    public function setDatosFiscales($datosFiscales)
    {
        $this->datosFiscales = $datosFiscales;

        return $this;
    }

    /**
     * Get datosFiscales
     *
     * @return string 
     */
    public function getDatosFiscales()
    {
        return $this->datosFiscales;
    }

    /**
     * Set domicilioFiscal
     *
     * @param string $domicilioFiscal
     * @return Entidad
     */
    public function setDomicilioFiscal($domicilioFiscal)
    {
        $this->domicilioFiscal = $domicilioFiscal;

        return $this;
    }

    /**
     * Get domicilioFiscal
     *
     * @return string 
     */
    public function getDomicilioFiscal()
    {
        return $this->domicilioFiscal;
    }

    /**
     * Set logo
     *
     * @param base64 $logo
     * @return Entidad
     */
    public function setLogo($logo = null)
    {
        $this->logo = $logo;
        
        return $this;
    }
    
    /**
     * Get logo
     *
     * @return base64
     */
    public function getLogo()
    {
        return $this->logo;
    }
    
    /**
     * Get monto
     *
     * @return float
     */
    public function getMonto()
    {
        $sum = 0;
        foreach ($this->fondos as $fondo){
            $sum += $fondo->getMonto();
        }
        return $sum;
    }
    
    /**
     * Get movimientos
     *
     * @return array
     */
    public function getMovimientos()
    {
        $arr = [];
        foreach ($this->fondos as $fondo){
            $arr = array_merge($arr,$fondo->getMovimientos()->toArray());
        }
        return $arr;
    }    
}
