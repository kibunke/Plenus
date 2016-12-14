<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Evento
 * @ORM\Table(name="services_juegosba_final.Evento")
 * @ORM\Entity(repositoryClass="ResultadoBundle\Entity\Repository\EventoRepository")
 */
class Evento
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
     * @ORM\Column(name="nombre", type="string", length=100, nullable=true)
     */
    protected $nombre;
    
     /**
     * @var string $descripcion
     *
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;
    
     /**
     * @var boolean
     *
     * @ORM\Column(name="inscribe", type="boolean", nullable=true)
     */
    private $inscribe;
    
     /**
     * @var boolean
     *
     * @ORM\Column(name="soloInscribe", type="boolean", nullable=true)
     */
    private $soloInscribe;

     /**
     * @var boolean
     *
     * @ORM\Column(name="eventoAdaptado", type="boolean", nullable=true)
     */
    private $eventoAdaptado;
    
    /**
     * @var integer $orden
     *
     * @ORM\Column(name="orden", type="integer")
     */
    private $orden;
    
    /**
     * @ORM\OneToMany(targetEntity="InscripcionBundle\Entity\Inscripto", mappedBy="evento")
     */
    private $inscriptos;

    /**
     * @ORM\OneToMany(targetEntity="Etapa", mappedBy="evento")
     */
    private $etapas;
    
    /**
     * @ORM\OneToMany(targetEntity="Equipo", mappedBy="evento")
     */
    private $equipos;     
    
    /**
     * @ORM\ManyToOne(targetEntity="Torneo", inversedBy="eventos")
     * @ORM\JoinColumn(name="torneo", referencedColumnName="id")
     */       
    private $torneo;

    /**
     * @ORM\ManyToOne(targetEntity="Categoria", inversedBy="eventos")
     * @ORM\JoinColumn(name="categoria", referencedColumnName="id")
     */       
    private $categoria;

    /**
     * @ORM\ManyToOne(targetEntity="Modalidad", inversedBy="eventos")
     * @ORM\JoinColumn(name="modalidad", referencedColumnName="id")
     */       
    private $modalidad;

    /**
     * @ORM\ManyToOne(targetEntity="Genero", inversedBy="eventos")
     * @ORM\JoinColumn(name="genero", referencedColumnName="id")
     */       
    private $genero;
    
    /**
     * @ORM\ManyToOne(targetEntity="Disciplina", inversedBy="eventos")
     * @ORM\JoinColumn(name="disciplina", referencedColumnName="id")
     */       
    private $disciplina;
    
    /**
     * @ORM\ManyToMany(targetEntity="SeguridadBundle\Entity\Usuario", inversedBy="coordina", cascade={"persist"})
     * @ORM\JoinTable(name="services_juegosba_admin.usuarios_coordina_eventos")
     **/    
    private $coordinadores;
    
    /**
     * @ORM\ManyToMany(targetEntity="Cronograma", mappedBy="eventos")
     **/    
    private $cronogramas;
    
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
     * __toString
     */    
    public function __toString()
    {
        return $this->getNombreCompleto();
    }
    
    /**
     * hasAccess
     */    
    public function hasAccess($user)
    {
        $idUser=$user->getId();
        foreach ($this->getCoordinadores() as $coordinador){
            if ($idUser==$coordinador->getId()){
                return true;
            }
        }
        return false;
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
     * @return Evento
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
        //$this->getTorneo()->getNombre()."-".
        $nom =  $this->getDisciplina()->getNombreCompleto()."-".
                $this->getCategoria()->getNombre()."-".
                $this->getGenero()->getNombre()."-".
                $this->getModalidad()->getNombre();
        if ($this->nombre)
            $nom = $nom."-".$this->getNombre();
        return $nom;
    }

    /**
     * Get nombreCompletoRaw
     *
     * @return string 
     */
    public function getNombreCompletoRaw()
    {
        $nom =  "<div title='".$this->getNombreCompleto()."'>".
                    "<strong>".$this->getDisciplina()->getNombreCompleto()."</strong><br>".
                    "<small>".$this->getCategoria()->getNombre()." - ".$this->getGenero()->getNombre()."</small><br>".
                    "<strong><small>".$this->getModalidad()->getNombre()."</small></strong>".
                "</div>";
        if ($this->nombre)
            $nom = $nom."-".$this->getNombre();
        return $nom;
    }

    /**
     * Get nombreCompletoRaw
     *
     * @return string 
     */
    public function getAuditoriaRaw()
    {
        return "<small><b>Creación :</b>".($this->createdAt?$this->createdAt->format('d/m/y H:i'):"No Definido").
                "<br/><b>Por:</b>".$this->createdBy.
                "<br/><b>Modificación:</b>".($this->updatedAt?$this->updatedAt->format('d/m/y H:i'):"Sin modificar").
                "<br/><b>Por:</b>".$this->updatedBy."</small>";
    }


    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Evento
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
     * @return Evento
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
     * @return Evento
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
     * Add inscriptos
     *
     * @param \InscripcionBundle\Entity\Inscripto $inscriptos
     * @return Evento
     */
    public function addInscripto(\InscripcionBundle\Entity\Inscripto $inscriptos)
    {
        $this->inscriptos[] = $inscriptos;

        return $this;
    }

    /**
     * Remove inscriptos
     *
     * @param \InscripcionBundle\Entity\Inscripto $inscriptos
     */
    public function removeInscripto(\InscripcionBundle\Entity\Inscripto $inscriptos)
    {
        $this->inscriptos->removeElement($inscriptos);
    }

    /**
     * Get inscriptos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInscriptos()
    {
        return $this->inscriptos;
    }

    /**
     * Set torneo
     *
     * @param \ResultadoBundle\Entity\Torneo $torneo
     * @return Evento
     */
    public function setTorneo(\ResultadoBundle\Entity\Torneo $torneo = null)
    {
        $this->torneo = $torneo;

        return $this;
    }

    /**
     * Get torneo
     *
     * @return \ResultadoBundle\Entity\Torneo 
     */
    public function getTorneo()
    {
        return $this->torneo;
    }

    /**
     * Set categoria
     *
     * @param \ResultadoBundle\Entity\Categoria $categoria
     * @return Evento
     */
    public function setCategoria(\ResultadoBundle\Entity\Categoria $categoria = null)
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * Get categoria
     *
     * @return \ResultadoBundle\Entity\Categoria 
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    /**
     * Set modalidad
     *
     * @param \ResultadoBundle\Entity\Modalidad $modalidad
     * @return Evento
     */
    public function setModalidad(\ResultadoBundle\Entity\Modalidad $modalidad = null)
    {
        $this->modalidad = $modalidad;

        return $this;
    }

    /**
     * Get modalidad
     *
     * @return \ResultadoBundle\Entity\Modalidad 
     */
    public function getModalidad()
    {
        return $this->modalidad;
    }

    /**
     * Set genero
     *
     * @param \ResultadoBundle\Entity\Genero $genero
     * @return Evento
     */
    public function setGenero(\ResultadoBundle\Entity\Genero $genero = null)
    {
        $this->genero = $genero;

        return $this;
    }

    /**
     * Get genero
     *
     * @return \ResultadoBundle\Entity\Genero 
     */
    public function getGenero()
    {
        return $this->genero;
    }

    /**
     * Set disciplina
     *
     * @param \ResultadoBundle\Entity\Disciplina $disciplina
     * @return Evento
     */
    public function setDisciplina(\ResultadoBundle\Entity\Disciplina $disciplina = null)
    {
        $this->disciplina = $disciplina;

        return $this;
    }

    /**
     * Get disciplina
     *
     * @return \ResultadoBundle\Entity\Disciplina 
     */
    public function getDisciplina()
    {
        return $this->disciplina;
    }

    /**
     * Add coordinadores
     *
     * @param \SeguridadBundle\Entity\Usuario $coordinadores
     * @return Evento
     */
    public function addCoordinadore(\SeguridadBundle\Entity\Usuario $coordinadores)
    {
        $this->coordinadores[] = $coordinadores;

        return $this;
    }

    /**
     * Remove coordinadores
     *
     * @param \SeguridadBundle\Entity\Usuario $coordinadores
     */
    public function removeCoordinadore(\SeguridadBundle\Entity\Usuario $coordinadores)
    {
        $this->coordinadores->removeElement($coordinadores);
    }

    /**
     * Get coordinadores
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCoordinadores()
    {
        return $this->coordinadores;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Evento
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
     * @return Evento
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
     * Set inscribe
     *
     * @param boolean $inscribe
     * @return Evento
     */
    public function setInscribe($inscribe)
    {
        $this->inscribe = $inscribe;

        return $this;
    }

    /**
     * Get inscribe
     *
     * @return boolean
     */
    public function getInscribe()
    {
        return $this->inscribe;
    }

    /**
     * Set soloInscribe
     *
     * @param boolean $soloInscribe
     * @return Evento
     */
    public function setSoloInscribe($soloInscribe)
    {
        $this->soloInscribe = $soloInscribe;

        return $this;
    }

    /**
     * Get soloInscribe
     *
     * @return boolean
     */
    public function getSoloInscribe()
    {
        return $this->soloInscribe;
    }

    /**
     * Set eventoAdaptado
     *
     * @param boolean $eventoAdaptado
     * @return Evento
     */
    public function setEventoAdaptado($eventoAdaptado)
    {
        $this->eventoAdaptado = $eventoAdaptado;

        return $this;
    }

    /**
     * Get eventoAdaptado
     *
     * @return boolean
     */
    public function getEventoAdaptado()
    {
        return $this->eventoAdaptado;
    }
    
    /**
     * Set orden
     *
     * @param integer $orden
     * @return Evento
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;

        return $this;
    }

    /**
     * Get inscribe
     *
     * @return integer
     */
    public function getOrden()
    {
        return $this->orden;
    }
    
    /**
     * Get totalInscriptos
     *
     * @return array
     */
    public function getTotalInscriptos()
    {
        $result = ['masculinos'=>0,'femeninos'=>0,'total'=>0];
        foreach ($this->getInscriptos() as $inscripto)
        {
            $result['masculinos']=$result['masculinos']+$inscripto->getCantidadMasculinos();
            $result['femeninos']=$result['femeninos']+$inscripto->getCantidadFemeninos();
            $result['total']=$result['total']+$inscripto->getCantidadMasculinos()+$inscripto->getCantidadFemeninos();
        }
        return $result;
    }    

    /**
     * Add etapas
     *
     * @param \ResultadoBundle\Entity\Etapa $etapas
     * @return Evento
     */
    public function addEtapa(\ResultadoBundle\Entity\Etapa $etapas)
    {
        $this->etapas[] = $etapas;

        return $this;
    }

    /**
     * Remove etapas
     *
     * @param \ResultadoBundle\Entity\Etapa $etapas
     */
    public function removeEtapa(\ResultadoBundle\Entity\Etapa $etapas)
    {
        $this->etapas->removeElement($etapas);
    }

    /**
     * Get etapas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEtapas()
    {
        return $this->etapas;
    }

    /**
     * Add equipos
     *
     * @param \ResultadoBundle\Entity\Equipo $equipos
     * @return Evento
     */
    public function addEquipo(\ResultadoBundle\Entity\Equipo $equipos)
    {
        $this->equipos[] = $equipos;

        return $this;
    }

    /**
     * Remove equipos
     *
     * @param \ResultadoBundle\Entity\Equipo $equipos
     */
    public function removeEquipo(\ResultadoBundle\Entity\Equipo $equipos)
    {
        $this->equipos->removeElement($equipos);
    }

    /**
     * Get equipos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEquipos()
    {
        //return $this->equipos;
        $return = $this->equipos->toArray();
        usort($return, function ($a, $b){
                return $a->getMunicipio()->getRegionDeportiva() > $b->getMunicipio()->getRegionDeportiva();
            }
        );        
        return $return;        
    }

    /**
     * Get statsEquipos
     *
     * @return string
     */
    public function getStatsEquipos()
    {
        $cantEqAsigando = 0;
        foreach ($this->equipos as $equipo){
            if ($equipo->hasPlazas()){
                $cantEqAsigando++;
            }
        }
        return $cantEqAsigando."/".count($this->equipos);
    }

    /**
     * Get statsEquipos
     *
     * @return string
     */
    public function getStatsPlazas()
    {
        $cantPlazas = 0;
        $cantPlazasAsignadas = 0;
        foreach ($this->etapas as $etapa){
            if ($etapa->getCompetencia()){
                foreach ($etapa->getCompetencia()->getPlazas() as $plaza){
                    $cantPlazas++;
                    if ($plaza->getEquipo())
                        $cantPlazasAsignadas++;
                }
            }
        }
        return $cantPlazasAsignadas."/".$cantPlazas;
    }
    
    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        $percent = 0;
        if (count($this->etapas) > 0){
            foreach ($this->etapas as $etapa){
                $percent = $percent + $etapa->getState();
            }
            return round($percent/count($this->etapas),1);
        }
        //return $percent;
        return round($percent,1);
    }

    /**
     * Add cronogramas
     *
     * @param \ResultadoBundle\Entity\Cronograma $cronogramas
     * @return Evento
     */
    public function addCronograma(\ResultadoBundle\Entity\Cronograma $cronogramas)
    {
        $this->cronogramas[] = $cronogramas;

        return $this;
    }

    /**
     * Remove cronogramas
     *
     * @param \ResultadoBundle\Entity\Cronograma $cronogramas
     */
    public function removeCronograma(\ResultadoBundle\Entity\Cronograma $cronogramas)
    {
        $this->cronogramas->removeElement($cronogramas);
    }

    /**
     * Get cronogramas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCronogramas()
    {        
        $cronogramas = $this->cronogramas->toArray();
        usort($cronogramas, function ($a, $b){
                if ($a->getFecha() > $b->getFecha()){
                    return true;
                }elseif ($a->getFecha() == $b->getFecha()){
                    return $a->getDescripcion()>$b->getDescripcion();
                }
                return false;
            }
        );                
        return $cronogramas;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->inscriptos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->etapas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->equipos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->coordinadores = new \Doctrine\Common\Collections\ArrayCollection();
        $this->cronogramas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();
    }
    
    /**
     * Get partidos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartidos()
    {
        $partidos=[];
        foreach($this->getEtapas() as $etapa){
            $partidos=array_merge($etapa->getPartidos(),$partidos);            
        }
        usort($partidos, function ($a, $b){
                //return $a->getCronograma()->getFecha() > $b->getCronograma()->getFecha();
                if ($a->getCronograma()->getFecha() > $b->getCronograma()->getFecha()){
                    return true;
                }elseif ($a->getCronograma()->getFecha() == $b->getCronograma()->getFecha()){
                    return $a->getNombre() > $b->getNombre();
                }
                return false;            
            }
        );        
        return $partidos;
    }

    /**
     * Get PlazasMedallero
     *
     * @return \Doctrine\Common\Collections\Collection 
     */    
    function getPlazasMedallero(){
        return $this->getEtapas()->last()->getCompetencia()->getPlazas();
    }
}
