<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\Evento
 * @ORM\Table(name="Evento")
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
     * @ORM\Column(name="eventoAdaptado", type="boolean", nullable=true)
     */
    private $eventoAdaptado;

    /**
     * @var integer $orden
     * @Assert\NotNull()
     * @ORM\Column(name="orden", type="integer")
     */
    private $orden;

    /**
     * @ORM\OneToMany(targetEntity="Etapa", mappedBy="evento")
     */
    private $etapas;

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
     * @ORM\ManyToOne(targetEntity="InscripcionBundle\Entity\Segmento", inversedBy="eventos")
     * @ORM\JoinColumn(name="segmento", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $segmento;

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
        $nomAux = '';
        if ($this->nombre)
            $nomAux = " - <small>".$this->getNombre()."</small>";
        $nom =  "<div title='".$this->getNombreCompleto()."'>".
                    "<strong>".$this->getDisciplina()->getNombreCompleto()."</strong><br>".
                    "<small>".$this->getCategoria()->getNombre()." - ".$this->getGenero()->getNombre()."</small><br>".
                    "<strong><small>".$this->getModalidad()->getNombre()."</small></strong>".$nomAux.
                "</div>";

        return $nom;

        //$nom =  "<div title='".$this->getNombreCompleto()."'>".
        //            "<strong>".$this->getDisciplina()->getNombreCompleto()."</strong><br>".
        //            "<small>".$this->getCategoria()->getNombre()." - ".$this->getGenero()->getNombre()."</small><br>".
        //            "<strong><small>".$this->getModalidad()->getNombre()."</small></strong>";
        //if ($this->nombre)
        //    $nom .= "-<small>".$this->getNombre()."</small>";
        //$nom .= "</div>";
        //return $nom;
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
     * Get coordinadores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoordinadores()
    {
        return $this->getSegmento()->getCoordinadores();
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
     * Get statsEquipos
     *
     * @return string
     */
    public function getStatsEquipos()
    {
        echo "REFACtoRIZAR";die(); // Esto se paso a etapa 
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
        $this->etapas = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set segmento
     *
     * @param \InscripcionBundle\Entity\Segmento $segmento
     *
     * @return Evento
     */
    public function setSegmento(\InscripcionBundle\Entity\Segmento $segmento = null)
    {
        $this->segmento = $segmento;

        return $this;
    }

    /**
     * Get segmento
     *
     * @return \InscripcionBundle\Entity\Segmento
     */
    public function getSegmento()
    {
        return $this->segmento;
    }

    /**
     * Set DimensionesFromSegmento
     *
     * @return Evento
     */
    public function setDimensionesFromSegmento($segmento)
    {
        $this->setTorneo($segmento->getTorneo());
        $this->setDisciplina($segmento->getDisciplina());
        $this->setCategoria($segmento->getCategoria());
        $this->setGenero($segmento->getGenero());
        $this->setModalidad($segmento->getModalidad());
        $this->setNombre($segmento->getNombre());
        $this->setSegmento($segmento);

        return $this;
    }

    /**
     * agregarEquipoClasificado
     *
     * @return \InscripcionBundle\Entity\Evento
     */
    public function agregarEquipoClasificado($equipo)
    {
        $etapaMunicipal = null;
        /*
         * Si no tiene etapas crea una, si tiene recupera la primera y verifica que se de clasificación municipal.
         * sino, lanza una Exception porque deben ser reacomodadas.
         */
        if (count($this->etapas)){
            $etapaMunicipal = $this->etapas->first();
            if (!$etapaMunicipal->isEtapaMunicipal()){
                throw new \Exception('Plenus: El evento tiene etapas creadas y la primera no es la de clasificación. Debe reacomodar las etapas antes de continuar.');
            }
        }else{
            $etapaMunicipal = new EtapaMunicipal();
        }

        $etapaMunicipal->addEquipo($equipo);
    }
}
