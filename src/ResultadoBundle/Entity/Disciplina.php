<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Disciplina
 *
 * @ORM\Entity(repositoryClass="ResultadoBundle\Entity\Repository\DisciplinaRepository")
 * @ORM\Table(name="Disciplina")
 */
class Disciplina
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
     * @Assert\NotNull()
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    protected $nombre;
    
    /**
     * @var string $abreviatura
     *
     * @ORM\Column(name="abreviatura", type="string", length=10, nullable=true)
     */
    protected $abreviatura;    
    
    /**
    * @var string $descripcion
    *
    * @ORM\Column(name="descripcion", type="text", nullable=true)
    */
   private $descripcion;

    /**
    * @var string $parametros
    *
    * @ORM\Column(name="parametros", type="text", nullable=true)
    */
   private $parametros;
   
    /**
     * @ORM\OneToMany(targetEntity="Evento", mappedBy="disciplina")
     */
    private $eventos;
    
    /**
     * @ORM\OneToMany(targetEntity="InscripcionBundle\Entity\Segmento", mappedBy="disciplina")
     */
    private $segmentos;    

     /**
     * @var boolean
     *
     * @ORM\Column(name="armarNombreRecursivo", type="boolean", nullable=true)
     */
    private $armarNombreRecursivo;

     /**
     * @var string
     *
     * @ORM\Column(name="nombreRecursivo", type="string", length=100, nullable=true)
     */
    private $nombreRecursivo;
    
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
     * @ORM\ManyToOne(targetEntity="Disciplina", inversedBy="hijos")
     * @ORM\JoinColumn(name="padre", referencedColumnName="id")
     */   
    private $padre;
    
    /**
     * @ORM\OneToMany(targetEntity="Disciplina", mappedBy="padre")
     */
    private $hijos;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->eventos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->coordinadores = new \Doctrine\Common\Collections\ArrayCollection();
        $this->hijos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->armarNombreRecursivo = TRUE;
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
     * Get idRecursivo
     *
     * @return array 
     */
    public function getIdRecursivo()
    {
        if ($this->getPadre()){
            $aux = $this->getPadre()->getIdRecursivo();
            $aux[]=$this->getId();
            return $aux;
        }
        return [$this->id];
    }    

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Disciplina
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
     * Set parametros
     *
     * @param string $parametros
     * @return Disciplina
     */
    public function setParametros($parametros)
    {
        $this->parametros = $parametros;

        return $this;
    }

    /**
     * Get parametros
     *
     * @return string 
     */
    public function getParametros()
    {
        if (!$this->parametros){
            if ($this->getPadre()){
                return $this->getPadre()->getParametros();
            }else{
                return "";
            }
        }
        return $this->parametros;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Disciplina
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
     * @return Disciplina
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
     * Add eventos
     *
     * @param \ResultadoBundle\Entity\Evento $eventos
     * @return Disciplina
     */
    public function addEvento(\ResultadoBundle\Entity\Evento $eventos)
    {
        $this->eventos[] = $eventos;

        return $this;
    }

    /**
     * Remove eventos
     *
     * @param \ResultadoBundle\Entity\Evento $eventos
     */
    public function removeEvento(\ResultadoBundle\Entity\Evento $eventos)
    {
        $this->eventos->removeElement($eventos);
    }

    /**
     * Get eventos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEventos()
    {
        return $this->eventos;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Disciplina
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
     * @return Disciplina
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
     * Set padre
     *
     * @param \ResultadoBundle\Entity\Disciplina $padre
     * @return Disciplina
     */
    public function setPadre(\ResultadoBundle\Entity\Disciplina $padre = null)
    {
        $this->padre = $padre;

        return $this;
    }

    /**
     * Get padre
     *
     * @return \ResultadoBundle\Entity\Disciplina 
     */
    public function getPadre()
    {
        return $this->padre;
    }

    /**
     * Add hijos
     *
     * @param \ResultadoBundle\Entity\Disciplina $hijos
     * @return Disciplina
     */
    public function addHijo(\ResultadoBundle\Entity\Disciplina $hijos)
    {
        $this->hijos[] = $hijos;

        return $this;
    }

    /**
     * Remove hijos
     *
     * @param \ResultadoBundle\Entity\Disciplina $hijos
     */
    public function removeHijo(\ResultadoBundle\Entity\Disciplina $hijos)
    {
        $this->hijos->removeElement($hijos);
    }

    /**
     * Get hijos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHijos()
    {
        return $this->hijos;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Disciplina
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
        return ucfirst($this->nombre);
    }

    /**
     * Get nombreCompleto
     *
     * @return string 
     */
    public function getNombreCompleto()
    {
        if ($this->getPadre())
        {
            if ($this->getNombreRecursivo())
                return $this->getPadre()->getNombreCompleto()." ".$this->nombre;
            return $this->getPadre()->getNombreCompleto();
        }
        //if ($this->getNombreRecursivo())
            return $this->nombre;
        //return;
    }
    
    /**
     * Set abreviatura
     *
     * @param string $abreviatura
     * @return Disciplina
     */
    public function setAbreviatura($abreviatura)
    {
        $this->abreviatura = $abreviatura;

        return $this;
    }

    /**
     * Get abreviatura
     *
     * @return string 
     */
    public function getAbreviatura()
    {
        return $this->abreviatura;
    }
    
    /**
     * Set armarNombreRecursivo
     *
     * @param boolean $armarNombreRecursivo
     * @return Disciplina
     */
    public function setArmarNombreRecursivo($armarNombreRecursivo)
    {
        $this->armarNombreRecursivo = $armarNombreRecursivo;

        return $this;
    }

    /**
     * Get armarNombreRecursivo
     *
     * @return boolean
     */
    public function getArmarNombreRecursivo()
    {
        return $this->armarNombreRecursivo;
    }
    
    /**
     * Set nombreRecursivo
     *
     * @param string $nombreRecursivo
     * @return Disciplina
     */
    public function setNombreRecursivo($nombreRecursivo)
    {
        $this->nombreRecursivo = $nombreRecursivo;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombreRecursivo()
    {
        return $this->nombreRecursivo;
    }    
}
