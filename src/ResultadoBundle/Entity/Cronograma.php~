<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Cronograma
 *
 * @ORM\Entity(repositoryClass="ResultadoBundle\Entity\Repository\CronogramaRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "cronograma"                = "Cronograma",
 *                          "cronogramaPartido"         = "CronogramaPartido"
 *                      })
 */
class Cronograma
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
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;
    
    /**
     * @ORM\ManyToOne(targetEntity="Escenario", inversedBy="cronogramas")
     * @ORM\JoinColumn(name="escenario", referencedColumnName="id")
     */       
    private $escenario;
    
    /**
     * @ORM\ManyToMany(targetEntity="Evento", inversedBy="cronogramas")
     * @ORM\JoinTable(name="evento_cronograma")
     **/
    private $eventos;

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
    public function __construct($user = null)
    {
        $this->eventos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdBy = $user;
        $this->createdAt = new \DateTime();
    }
    
    /**
     * Get raw
     *
     * @return string 
     */
    public function getRaw()
    {
        if ($this->getFecha()){
            $f1 = $this->fecha->format('d/m');
            $f2 = $this->fecha->format('H:i');
        }else{
            $f1 = $f2 = "";
        }
        return  "<div>
                    <strong><small>".
                        $f1.
                    " </small>".
                        $f2.
                    "</strong><br>
                    <small>".
                        $this->getEscenario().                        
                    "</small>
                </div>"
        ;
    }
    
        /**
     * Get raw
     *
     * @return string 
     */
    public function descripcionFrontRaw($evento = null)
    {
        $str="";
        if (count($this->getEventos())==0){
            $pl1 = "No definido";
            $pl2 = "No definido";
            if ($this->getPartido()->getPlaza1()->getEquipo())
                $pl1 = $this->getPartido()->getPlaza1()->getEquipo()->getMunicipio()->getNombre();
            if ($this->getPartido()->getPlaza2()->getEquipo())
                $pl2 = $this->getPartido()->getPlaza2()->getEquipo()->getMunicipio()->getNombre();
            $str.="<div>
                    <strong><small>".
                        $this->getPartido()->getEvento()->getNombreCompleto().
                    " </small></strong><br><small>".$this->getPartido()->getNivelTexto()."</small><br>".
                        $pl1.
                    "<strong> .VS. </strong>".
                        $pl2.
                    "
                </div>";
        }else{
            $str.="<div>
                    <strong><small>".
                        $evento.
                    " </small>".
                        
                    "</strong><br>
                    <small>".
                        $this->getDescripcion().
                    "</small>
                </div>";            
        }
        return  $str;
        ;
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return Cronograma
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
     * Set fecha
     *
     * @param \DateTime $fecha
     * @return Cronograma
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime 
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set escenario
     *
     * @param \ResultadoBundle\Entity\Escenario $escenario
     * @return Cronograma
     */
    public function setEscenario(\ResultadoBundle\Entity\Escenario $escenario)
    {
        $this->escenario = $escenario;

        return $this;
    }

    /**
     * Get escenario
     *
     * @return Cronograma 
     */
    public function getEscenario()
    {
        return $this->escenario;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Cronograma
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
     * @return Cronograma
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
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Cronograma
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
     * @return Cronograma
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
     * Get idReload
     *
     * @return integer
     */
    public function getIdReload()
    {
        //return $this->getEvento()->getId();
    }
    
    /**
     * Can Delete
     *
     * @return boolean
     */
    public function canDelete()
    {
        return true;
    }    

    /**
     * Add eventos
     *
     * @param \ResultadoBundle\Entity\Evento $eventos
     * @return Cronograma
     */
    public function addEvento(\ResultadoBundle\Entity\Evento $eventos)
    {
        $this->eventos[] = $eventos;
        //$eventos->addCronograma($this);
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
    
}
