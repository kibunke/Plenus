<?php
namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResultadoBundle\Entity\Partido
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "partido"               = "Partido",
 *                          "partidoPuntos"         = "PartidoPuntos",
 *                          "partidoTantos"         = "PartidoTantos",
 *                          "partidoTantosCartas"   = "PartidoTantosCartas"
 *                      })
 */
abstract class Partido
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
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;
    
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
     * @ORM\ManyToOne(targetEntity="PlazaPartido", inversedBy="partidosLocal", cascade={"persist","refresh"})
     * @ORM\JoinColumn(name="plaza1", referencedColumnName="id", nullable=false)
     */    
    private $plaza1;
    
    /**
     * @ORM\ManyToOne(targetEntity="PlazaPartido", inversedBy="partidosVisitante", cascade={"persist","refresh"})
     * @ORM\JoinColumn(name="plaza2", referencedColumnName="id", nullable=false)
     */    
    private $plaza2;
    
    /**
     * @ORM\ManyToOne(targetEntity="Zona", inversedBy="partidos")
     * @ORM\JoinColumn(name="zona", referencedColumnName="id")
     */
    private $zona;
    
    /**
     * @ORM\ManyToOne(targetEntity="CompetenciaEliminacionDirecta", inversedBy="partidos")
     * @ORM\JoinColumn(name="copa", referencedColumnName="id")
     */
    private $copa;    
    
    /**
     * @ORM\OneToOne(targetEntity="CronogramaPartido", mappedBy="partido", cascade={"all"})
     **/
    private $cronograma;

    /**
     * @ORM\OneToMany(targetEntity="Partido", mappedBy="siguiente")
     **/
    private $anteriores;
    
    /**
     * @ORM\ManyToOne(targetEntity="Partido", inversedBy="anteriores")
     * @ORM\JoinColumn(name="jerarquiaDePatidos", referencedColumnName="id")
     **/
    private $siguiente;
    
    /**
     * @var integer $orden
     *
     * @ORM\Column(name="orden", type="integer", nullable=true)
     */
    protected $orden;
   
    /**
     * @var integer $nivel
     *
     * @ORM\Column(name="nivel", type="integer", nullable=true)
     */
    protected $nivel;
   
    /**
     * @var integer $resultadoLocal
     *
     * @ORM\Column(name="resultadoLocal", type="integer", nullable=true)
     */
    private $resultadoLocal;
    
    /**
     * @var integer $resultadoVisitante
     *
     * @ORM\Column(name="resultadoVisitante", type="integer", nullable=true)
     */
    private $resultadoVisitante;
    
    /**
     * @var integer $resultadoSecundarioLocal
     *
     * @ORM\Column(name="resultadoSecundarioLocal", type="integer", nullable=true)
     */
    private $resultadoSecundarioLocal;
    
    /**
     * @var integer $resultadoSecundarioVisitante
     *
     * @ORM\Column(name="resultadoSecundarioVisitante", type="integer", nullable=true)
     */
    private $resultadoSecundarioVisitante;
    
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
    
    public function __construct($user = NULL) {
        $this->createdAt = new \DateTime();
        $this->createdBy = $user;
        $this->setCronograma(new CronogramaPartido($user));
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->nombre;
    }
    
    /**
     * resetResultado
     *
     * @return boolean
     */
    public function resetResultado()
    {
        $this->setResultadoLocal(NULL);
        $this->setResultadoVisitante(NULL);
        $this->setResultadoSecundarioLocal(NULL);
        $this->setResultadoSecundarioVisitante(NULL);
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
     * @return Competencia
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
     * @return Competencia
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
     * @return Competencia
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
     * @return Competencia
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
     * @return Competencia
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
     * @return Competencia
     */
    public function setUpdatedBy(\SeguridadBundle\Entity\Usuario $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;
        $this->updatedAt = new \DateTime();
        
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
        return $this->parametros;
    }
    
    /**
     * Set zona
     *
     * @param \ResultadoBundle\Entity\Zona $zona
     * @return Partido
     */
    public function setZona(\ResultadoBundle\Entity\Zona $zona = null)
    {
        $this->zona = $zona;

        return $this;
    }

    /**
     * Get zona
     *
     * @return \ResultadoBundle\Entity\Zona 
     */
    public function getZona()
    {
        return $this->zona;
    }
    
    /**
     * Set plaza1
     *
     * @param \ResultadoBundle\Entity\PlazaPartido $plaza1
     * @return Partido
     */
    public function setPlaza1(\ResultadoBundle\Entity\PlazaPartido $plaza = null)
    {
        $this->plaza1 = $plaza;

        return $this;
    }

    /**
     * Get plaza1
     *
     * @return \ResultadoBundle\Entity\PlazaPartido 
     */
    public function getPlaza1()
    {
        return $this->plaza1;
    }
    
    /**
     * Set plaza1
     *
     * @param \ResultadoBundle\Entity\PlazaPartido $plaza
     * @return Partido
     */
    public function setPlaza2(\ResultadoBundle\Entity\PlazaPartido $plaza = null)
    {
        $this->plaza2 = $plaza;

        return $this;
    }

    /**
     * Get plaza2
     *
     * @return \ResultadoBundle\Entity\PlazaPartido 
     */
    public function getPlaza2()
    {
        return $this->plaza2;
    }   

    /**
     * Set cronograma
     *
     * @param \ResultadoBundle\Entity\Cronograma $cronograma
     * @return Partido
     */
    public function setCronograma(\ResultadoBundle\Entity\Cronograma $cronograma = null)
    {
        $this->cronograma = $cronograma;
        $cronograma->setPartido($this);

        return $this;
    }

    /**
     * Get cronograma
     *
     * @return \ResultadoBundle\Entity\Cronograma 
     */
    public function getCronograma()
    {
        return $this->cronograma;
    }
    
    /** 
     * Get puntos
     *
     * @return array
     */
    public function soyLocal($plaza)
    {
        if ($plaza->getId() == $this->getPlaza1()->getId())
            return true;
        return false;
    }
    
    /** 
     * Get empato
     *
     * @return boolean
     */
    public function empato($plaza)
    {    
        if ( ($this->getResultadoLocal() == $this->getResultadoVisitante()) && ! $this->getResultadoSecundarioLocal())
            return true;
        return false;
    }
    
    /** 
     * Get gano
     *
     * @return boolean
     */
    public function gano($plaza)
    {
        if ($this->soyLocal($plaza)){
            if ( $this->getResultadoLocal() > $this->getResultadoVisitante() || (
                    $this->getResultadoLocal() == $this->getResultadoVisitante() &&
                    $this->getResultadoSecundarioLocal() > $this->getResultadoSecundarioVisitante()
                    )
                )
                return true;
        }else{
            if ( $this->getResultadoLocal() < $this->getResultadoVisitante() || (
                    $this->getResultadoLocal() == $this->getResultadoVisitante() &&
                    $this->getResultadoSecundarioLocal() < $this->getResultadoSecundarioVisitante()
                    )
                )
                return true;
        }
        return false;
    }
    
    /** 
     * Get jugado
     *
     * @return boolean
     */
    public function jugado()
    {
        //return ($this->getResultadoLocal()>=0 && !is_null($this->getResultadoLocal()));
        return !is_null($this->getResultadoLocal());
    }
    
    /** 
     * Has cronograma
     * @return boolean
     */
    public function hasCronograma()
    {
        if ($this->getCronograma()->getEscenario())
            return true;
        return false;
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        if ($this->jugado() && $this->hasCronograma())
            return 100;
        elseif ($this->jugado() || $this->hasCronograma())
            return 50;
        return 0;
    }     

    /**
     * Add anteriores
     *
     * @param \ResultadoBundle\Entity\Partido $anteriores
     * @return Partido
     */
    public function addAnteriore(\ResultadoBundle\Entity\Partido $anteriores)
    {
        $this->anteriores[] = $anteriores;

        return $this;
    }

    /**
     * Remove anteriores
     *
     * @param \ResultadoBundle\Entity\Partido $anteriores
     */
    public function removeAnteriore(\ResultadoBundle\Entity\Partido $anteriores)
    {
        $this->anteriores->removeElement($anteriores);
    }

    /**
     * Get anteriores
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAnteriores()
    {
        return $this->anteriores;
    }

    /**
     * Set siguiente
     *
     * @param \ResultadoBundle\Entity\Partido $siguiente
     * @return Partido
     */
    public function setSiguiente(\ResultadoBundle\Entity\Partido $siguiente = null)
    {
        $this->siguiente = $siguiente;

        return $this;
    }

    /**
     * Get siguiente
     *
     * @return \ResultadoBundle\Entity\Partido 
     */
    public function getSiguiente()
    {
        return $this->siguiente;
    }

    /**
     * Set orden
     *
     * @param integer $orden
     * @return Partido
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get orden
     *
     * @return integer 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set nivel
     *
     * @param integer $nivel
     * @return Partido
     */
    public function setNivel($nivel)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel
     *
     * @return integer 
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * Get nivel
     *
     * @return integer 
     */
    public function getNivelTexto()
    {
        if ($this->getZona())
            return $this->getZona()->getNombre();
        switch ($this->nivel){
            case 4 : return "8vos de Final";
            case 3 : return "4tos de Final";
            case 2 : return "Semifinal";
            case 1 : return "Final";
            case 0 : return "3er Puesto";
        }
    }

    /**
     * Set resultadoLocal
     *
     * @param integer $resultadoLocal
     * @return PartidoPuntos
     */
    public function setResultadoLocal($resultadoLocal)
    {
        $this->resultadoLocal = $resultadoLocal;

        return $this;
    }

    /**
     * Get resultadoLocal
     *
     * @return integer 
     */
    public function getResultadoLocal()
    {
        return $this->resultadoLocal;
    }

    /**
     * Set resultadoVisitante
     *
     * @param integer $resultadoVisitante
     * @return PartidoPuntos
     */
    public function setResultadoVisitante($resultadoVisitante)
    {
        $this->resultadoVisitante = $resultadoVisitante;

        return $this;
    }

    /**
     * Get resultadoVisitante
     *
     * @return integer 
     */
    public function getResultadoVisitante()
    {
        return $this->resultadoVisitante;
    }

    /**
     * Set resultadoSecundarioLocal
     *
     * @param integer $resultadoSecundarioLocal
     * @return PartidoPuntos
     */
    public function setResultadoSecundarioLocal($resultadoSecundarioLocal)
    {
        $this->resultadoSecundarioLocal = $resultadoSecundarioLocal;

        return $this;
    }

    /**
     * Get resultadoSecundarioLocal
     *
     * @return integer 
     */
    public function getResultadoSecundarioLocal()
    {
        return $this->resultadoSecundarioLocal;
    }

    /**
     * Set resultadoSecundarioVisitante
     *
     * @param integer $resultadoSecundarioVisitante
     * @return PartidoPuntos
     */
    public function setResultadoSecundarioVisitante($resultadoSecundarioVisitante)
    {
        $this->resultadoSecundarioVisitante = $resultadoSecundarioVisitante;

        return $this;
    }

    /**
     * Get resultadoSecundarioVisitante
     *
     * @return integer 
     */
    public function getResultadoSecundarioVisitante()
    {
        return $this->resultadoSecundarioVisitante;
    }
    
    /**
     * Set copa
     *
     * @param \ResultadoBundle\Entity\CompetenciaEliminacionDirecta $copa
     * @return Partido
     */
    public function setCopa(\ResultadoBundle\Entity\CompetenciaEliminacionDirecta $copa = null)
    {
        $this->copa = $copa;

        return $this;
    }

    /**
     * Get copa
     *
     * @return \ResultadoBundle\Entity\CompetenciaEliminacionDirecta 
     */
    public function getCopa()
    {
        return $this->copa;
    }

    /**
     * Get evento
     *
     * @return \ResultadoBundle\Entity\Evento
     */
    public function getEvento()
    {
        if ($this->getCopa())
            return $this->getCopa()->getEtapa()->getEvento();
        return $this->getZona()->getLiga()->getEtapa()->getEvento();
    }
    
    /**
     * Get idReload
     *
     * @return integer
     */
    public function getIdReload()
    {
        if ($this->getCopa())
            return $this->getCopa()->getId();
        else
            return $this->getZona()->getId();
    }
    
    /**
     * Get contrincante
     *
     * @return Plaza
     */
    public function getContrincante($plaza)
    {
        if($this->getPlaza1()->getId() == $plaza->getId())
            return $this->getPlaza2();
        return $this->getPlaza1();
    }
    
    /**
     * Get update
     *
     * @return boolean
     */
    public function update()
    {
        if($this->getZona())
            $this->getZona()->recalcularOrdenNatural();
        return true;
    }
    
}
