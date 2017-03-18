<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * InscripcionBundle\Entity\Planilla
 * @ORM\Table(name="Planilla")
 * @ORM\Entity(repositoryClass="InscripcionBundle\Entity\Repository\PlanillaRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *                          "Individual" = "Individual",
 *                          "Equipo"   = "Equipo"
 *                      })
 */
abstract class Planilla
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
     * @ORM\OneToMany(targetEntity="ResultadoBundle\Entity\Equipo", mappedBy="planilla", cascade={"persist","remove"})
     */
    private $equipos;
    
    /**
     * @ORM\ManyToOne(targetEntity="Institucion", cascade={"persist"})
     * @ORM\JoinColumn(name="institucion", referencedColumnName="id")
     */       
    private $institucion;    
    
    /**
     * @ORM\ManyToOne(targetEntity="Segmento", inversedBy="planillas")
     * @ORM\JoinColumn(name="segmento", referencedColumnName="id")
     */       
    private $segmento;
    
    /**
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Municipio")
     * @ORM\JoinColumn(name="municipio", referencedColumnName="id")
     */       
    private $municipio;
    
    /**
     * @var string $responsableMunicipioNombre
     *
     * @ORM\Column(name="responsableMunicipioNombre", type="string", length=100)
     */
    protected $responsableMunicipioNombre;
    
    /**
     * @var string $responsableMunicipioApellido
     *
     * @ORM\Column(name="responsableMunicipioApellido", type="string", length=100)
     */
    protected $responsableMunicipioApellido;
    
    /**
     * @var string $responsableMunicipioDni
     *
     * @ORM\Column(name="responsableMunicipioDni", type="string", length=100)
     */
    protected $responsableMunicipioDni;
    
    /**
     * @var string $directorTecnicoDni
     *
     * @ORM\Column(name="directorTecnicoDni", type="string", length=100, nullable=true)
     */
    protected $directorTecnicoDni;

    /**
     * @var string $directorTecnicoNombre
     *
     * @ORM\Column(name="directorTecnicoNombre", type="string", length=100, nullable=true)
     */
    protected $directorTecnicoNombre;
    
    /**
     * @var string $directorTecnicoApellido
     *
     * @ORM\Column(name="directorTecnicoApellido", type="string", length=100, nullable=true)
     */
    protected $directorTecnicoApellido;
    
    /**
     * this is OneToMany because the historial is important
     * @ORM\OneToMany(targetEntity="PlanillaEstado", mappedBy="planilla", cascade={"persist","remove"})
     */
    private $estados;
    
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
        $this->equipos   = new ArrayCollection();
        $this->estados   = new ArrayCollection();
        //$this->addEstado(new Cargada());
    }

    /**
     * __toString
     */    
    public function __toString()
    {
        return "Planilla de ".$this->getMunicipio()->getNombre()." con ".count($this->getParticipantes())." participantes";
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
     * Get id
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->getId() ? str_pad($this->getId(), 6, "0", STR_PAD_LEFT):'000000';
    }    

    /**
     * Set descripcion
     *
     * @param string $descripcion
     *
     * @return Planilla
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
     *
     * @return Planilla
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
     *
     * @return Planilla
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
     * Add equipo
     *
     * @param \ResultadoBundle\Entity\Equipo $equipo
     *
     * @return Planilla
     */
    public function addEquipo(\ResultadoBundle\Entity\Equipo $equipo)
    {
        $this->equipos[] = $equipo;
        $equipo->setPlanilla($this);

        return $this;
    }

    /**
     * Remove equipo
     *
     * @param \ResultadoBundle\Entity\Equipo $equipo
     */
    public function removeCompetidore(\ResultadoBundle\Entity\Equipo $equipo)
    {
        $this->equipos->removeElement($equipo);
    }

    /**
     * Get equipo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEquipos()
    {
        return $this->equipos;
    }

    /**
     * Set institucion
     *
     * @param \InscripcionBundle\Entity\Institucion $institucion
     *
     * @return Planilla
     */
    public function setInstitucion(\InscripcionBundle\Entity\Institucion $institucion = null)
    {
        $this->institucion = $institucion;

        return $this;
    }

    /**
     * Get institucion
     *
     * @return \InscripcionBundle\Entity\Institucion
     */
    public function getInstitucion()
    {
        return $this->institucion;
    }

    /**
     * Set segmento
     *
     * @param \InscripcionBundle\Entity\Segmento $segmento
     *
     * @return Planilla
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
     * Set municipio
     *
     * @param \CommonBundle\Entity\Municipio $municipio
     *
     * @return Planilla
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
     * Add estado
     *
     * @param \InscripcionBundle\Entity\PlanillaEstado $estado
     *
     * @return Planilla
     */
    public function addEstado(\InscripcionBundle\Entity\PlanillaEstado $estado)
    {
        $this->estados[] = $estado;
        $estado->setPlanilla($this);
        return $this;
    }

    /**
     * Remove estado
     *
     * @param \InscripcionBundle\Entity\PlanillaEstado $estado
     */
    public function removeEstado(\InscripcionBundle\Entity\PlanillaEstado $estado)
    {
        $this->estados->removeElement($estado);
    }

    /**
     * Get estados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstados()
    {
        return $this->estados;
    }

    /**
     * Get estados
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstado()
    {
        return $this->estados->last();
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     *
     * @return Planilla
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
     *
     * @return Planilla
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
     * Set responsableMunicipioNombre
     *
     * @param string $responsableMunicipioNombre
     *
     * @return Planilla
     */
    public function setResponsableMunicipioNombre($responsableMunicipioNombre)
    {
        $this->responsableMunicipioNombre = $responsableMunicipioNombre;

        return $this;
    }

    /**
     * Get responsableMunicipioNombre
     *
     * @return string
     */
    public function getResponsableMunicipioNombre()
    {
        return $this->responsableMunicipioNombre;
    }

    /**
     * Set responsableMunicipioApellido
     *
     * @param string $responsableMunicipioApellido
     *
     * @return Planilla
     */
    public function setResponsableMunicipioApellido($responsableMunicipioApellido)
    {
        $this->responsableMunicipioApellido = $responsableMunicipioApellido;

        return $this;
    }

    /**
     * Get responsableMunicipioApellido
     *
     * @return string
     */
    public function getResponsableMunicipioApellido()
    {
        return $this->responsableMunicipioApellido;
    }

    /**
     * Remove equipo
     *
     * @param \ResultadoBundle\Entity\Equipo $equipo
     */
    public function removeEquipo(\ResultadoBundle\Entity\Equipo $equipo)
    {
        $this->equipos->removeElement($equipo);
    }

    /**
     * isTecnicoRequired
     *
     * @return boolean
     */
    public function IsTecnicoRequired()
    {
        //Por negocion los Juveniles tiene DT obligatorio
        return !$this->getSegmento()->getTorneo()->getIsAdultosMayores();
    }

    /**
     * inDateRange
     *
     * @return boolean
     */
    public function inDateRange($integrante)
    {
        return  $this->getSegmento()->getMinFechaNacimiento() <= $integrante->getFNacimiento() &&
                $this->getSegmento()->getMaxFechaNacimiento() >= $integrante->getFNacimiento();
    }    
    
    /**
     * Get equipos
     *
     * @return json
     */
    public function getJson()
    {
        $segmento = $this->getSegmento();
        return array(
                      "id"         => $this->getId(),
                      "parametros" => array(
                                                "maxEqPlanilla"  => $segmento->getMaxEquiposPorPlanilla(),
                                                "maxIntegrantes" => $segmento->getMaxIntegrantes(),
                                                "minIntegrantes" => $segmento->getMinIntegrantes(),
                                                "maxReemplazos"  => $segmento->getMaxReemplazos(),
                                                "fechaMin"       => $segmento->getMinFechaNacimiento(),
                                                "fechaMax"       => $segmento->getMaxFechaNacimiento(),
                                                "genero"         => $segmento->getGenero()->getNombre(),
                                            ),
                      "equipos"                  => $this->getEquiposJson(),
                      "inscripcionInstitucional" => $this->getInstitucion() ? true : false,
                      "institucion"              => $this->getInstitucion() ? $this->getInstitucion()->getJson() : [],
                      "responsableMunicipio" => array(
                                                        "nombre"   => $this->getResponsableMunicipioNombre(),
                                                        "apellido" => $this->getResponsableMunicipioApellido(),
                                                        "dni"      => $this->getResponsableMunicipioDni(),
                                                      )
                );
    }
    
    /**
     * Get equipos
     *
     * @return json
     */
    public function toArray()
    {
        return array(
                        "id"        => $this->getId(),
                        "numero"    => $this->getNumero(),
                        "municipio" => $this->getMunicipio()->getNombre(),
                        "segmento"  => $this->getSegmento()->toArray()
                );
    }
    
    private function getEquiposJson()
    {
        $equipos = array();
        foreach ($this->getEquipos() as $equipo)
        {
            $equipos[] = $equipo->getJson();
        }
        
        return $equipos;        
    }
    
    /**
     * validarInscripcion
     */
    public function inscripcionValida($planilla)
    {    
        //Chequea que el participante no este inscripto en el segmento 
        if ($this->getSegmento() == $planilla->getSegmento())
        {
            throw new \Exception('Plenus: El participante con DNI %DNI% ya está inscripto en este segmento pero en otro equipo!');
        }
        //Chequea que el participante siempre represente al mismo municipio
        if ($this->getMunicipio() != $planilla->getMunicipio())
        {
            throw new \Exception('Plenus: El participante con DNI %DNI% ya está inscripto en otro equipo por el municipio de '.$this->getMunicipio()->getNombre());
        }
        
        return true;
    }
    
    /**
     * validarInscripcion
     */
    public function validarInscripcion($inscripto)
    {
        try{
            if (!$this->getInstitucion()){
                if ($this->getMunicipio() != $inscripto->getMunicipio()){
                    throw new \Exception('Plenus: El participante con DNI %DNI% no puede inscribirse en esta planilla porque pertenece al municipio de '.$inscripto->getMunicipio()->getNombre().'. En una inscripción MUNICIPAL todos los participantes deben pertenecer al municipio indicado en la planilla.');
                }
            }
            if ($this->getSegmento()->getGenero()->getNombre() != 'Mixto'){
                if ($this->getSegmento()->getGenero()->getNombre() != $inscripto->getGenero()->getNombre()){
                    throw new \Exception('Plenus: El participante con DNI %DNI% no puede inscribirse en esta planilla por su sexo.');
                }
            }

            if(in_array($inscripto,$this->getInscriptos())){
                throw new \Exception('Plenus: El participante con DNI %DNI% no puede inscribirse más de una vez por planilla.');
            }            
            foreach ($inscripto->getEquipos() as $equipo){
                if ($this->id != $equipo->getPlanilla()->getId())
                    $this->inscripcionValida($equipo->getPlanilla());
            }
        }catch(\Exception $e){
            $message = $e->getMessage();
            throw new \Exception(str_replace('%DNI%',$inscripto->getDni(),$message));
        }

        return true;
    }
    
    /**
     * getTotalInscriptos
     */
    public function getTotalInscriptos()
    {
        $sum = 0;
        foreach ($this->getEquipos() as $equipo)
        {
            $sum += count($equipo->getCompetidores());
        }
        return $sum;
    }

    /**
     * getInscriptos
     */
    public function getInscriptos()
    {
        $inscriptos = [];
        foreach ($this->getEquipos() as $equipo)
        {
            foreach ($this->getEquipos() as $equipo)
            $inscriptos = array_merge($inscriptos,$equipo->getCompetidores()->toArray());
        }
        return $inscriptos;
    }
    
    /**
     * Set responsableMunicipioDni
     *
     * @param string $responsableMunicipioDni
     *
     * @return Planilla
     */
    public function setResponsableMunicipioDni($responsableMunicipioDni)
    {
        $this->responsableMunicipioDni = $responsableMunicipioDni;

        return $this;
    }

    /**
     * Get responsableMunicipioDni
     *
     * @return string
     */
    public function getResponsableMunicipioDni()
    {
        return $this->responsableMunicipioDni;
    }

    /**
     * Get isRemovable
     *
     * @return boolean
     */
    public function isRemovable($user)
    {
        return $this->getEstado()->isRemovable() && $user->getId() == $this->getCreatedBy()->getId();
    }

    /**
     * Get isCompleted
     *
     * @return boolean
     */
    public function isCompleted()
    {
        foreach ($this->getEquipos() as $equipo)
        {
            $cantCompetidores = 0;
            foreach ($equipo->getEquipoCompetidores() as $aux)
            {
                if ($aux->getRol() == "inscripto"){
                    $cantCompetidores ++;
                }
            }
            if ( $cantCompetidores > $this->getSegmento()->getMaxIntegrantes() || $cantCompetidores < $this->getSegmento()->getMinIntegrantes())
                return false;
            
        }
        return true;
    }
    
    /**
     * Get isEditable
     *
     * @return boolean
     */
    public function isEditable($user)
    {
        return $this->getEstado()->isEditable() && $user->getId() == $this->getCreatedBy()->getId();
    }
    
    /**
     * Get prepareToDelete
     *
     * @return boolean
     */
    public function prepareToDelete()
    {
        $toRemove = [];
        foreach ($this->getEquipos() as $equipo)
        {
            $toRemove = array_merge($toRemove,$equipo->cleanCompetidores());
            $equipo->setDirectorTecnico = NULL;
        }
        $this->setInstitucion = NULL;
        
        return $toRemove;
    }    
    
    
    public function getProximosEstados(\SeguridadBundle\Entity\Usuario $usuario)
    {
       return $this->getEstado()->getProximosEstados($usuario); 
    }
    

    /**
     * Set directorTecnicoNombre
     *
     * @param string $directorTecnicoNombre
     *
     * @return Planilla
     */
    public function setDirectorTecnicoNombre($directorTecnicoNombre)
    {
        $this->directorTecnicoNombre = $directorTecnicoNombre;

        return $this;
    }

    /**
     * Get directorTecnicoNombre
     *
     * @return string
     */
    public function getDirectorTecnicoNombre()
    {
        return $this->directorTecnicoNombre;
    }

    /**
     * Set directorTecnicoApellido
     *
     * @param string $directorTecnicoApellido
     *
     * @return Planilla
     */
    public function setDirectorTecnicoApellido($directorTecnicoApellido)
    {
        $this->directorTecnicoApellido = $directorTecnicoApellido;

        return $this;
    }

    /**
     * Get directorTecnicoApellido
     *
     * @return string
     */
    public function getDirectorTecnicoApellido()
    {
        return $this->directorTecnicoApellido;
    }
    
    /**
     * Get directorTecnicoDni
     *
     * @return string
     */
    public function getDirectorTecnicoDni()
    {
        return $this->directorTecnicoDni;
    }
    
       /**
     * Set directorTecnicoDni
     *
     * @param string $directorTecnicoDni
     *
     * @return Planilla
     */
    public function setDirectorTecnicoDni($directorTecnicoDni)
    {
        $this->directorTecnicoDni = $directorTecnicoDni;

        return $this;
    }
    
    public function getTemplateShow(){
        return "InscripcionBundle:Planilla:planillaShow.html.twig";
    }    
}
