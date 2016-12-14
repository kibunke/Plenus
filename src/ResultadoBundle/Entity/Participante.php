<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CommonBundle\Entity\Persona;
/**
 * ResultadoBundle\Entity\Participante
 *
 * @ORM\Entity(repositoryClass="ResultadoBundle\Entity\Repository\ParticipanteRepository")
 *
 */
class Participante extends Persona
{
    /**
    * @ORM\ManyToMany(targetEntity="Equipo", inversedBy="participantes")
    * @ORM\JoinTable(name="services_juegosba_final.equipo_participante")
    */
    private $equipos;

    /**
     * @var string $rol
     *
     * @ORM\Column(name="rol", type="string", length=100)
     */
    protected $rol;

    /**
     * Constructor
     */
    public function __construct($user=null)
    {
        $this->equipos = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct($user);
    }
    
    /**
     * Set equipo
     *
     * @param \ResultadoBundle\Entity\Equipo $equipo
     * @return Participante
     */
    public function setEquipo(\ResultadoBundle\Entity\Equipo $equipo = null)
    {
        $this->equipos = $equipo;
        return $this;
    }

    /**
     * Get equipo
     *
     * @return \ResultadoBundle\Entity\Equipo 
     */
    public function getEquipos()
    {
        return $this->equipos;
    }
    
    /**
     * Set rol
     *
     * @param string $rol
     * @return Evento
     */
    public function setRol($rol)
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return string 
     */
    public function getRol()
    {
        return $this->rol;
    }
    

    
    /**
     * validarAsignacion
     * valida que el participante no este en dos equipos del mismo torneo
     * 
     */
    public function validarAsignacion($participante)
    {
        /* ESTE METODO HAY Q REFACTORIZARLO !!!!
         * en resumidas cuentas lo que haces es buscar los equipos donde el participante que ya estaba inscripto
         * integra. Luego para esos equipos se fija las areas de los mismo y chequea que el evento donde se lo quiere inscribir
         * no tenga problemas, Las condiciones son :
         * 1- no estar en dos eventos del area de cultura
         * 2- no estar en dos eventos del area de deportes, salvo que uno de los dos sea una competencia de posta
         */
        
        $torneos = new \Doctrine\Common\Collections\ArrayCollection();
        $diciplinas = [];//METO TODAS LAS DISCIPLINAS DONDE PARTICIPA EL INSCRIPTO, las que ya esta isncripto y la que se quiere inscribir
        foreach ( $this->getEquipos() as $item ){
            $torneos->add($item->getEvento()->getTorneo()->getArea());
            // disciplinas en las que ya esta inscripto
            $diciplinas[] = strtoupper($item->getEvento()->getDisciplina()->getNombreCompleto());
        }
        foreach ( $participante->getEquipos() as $item ){
            if (true === $torneos->contains($item->getEvento()->getTorneo()->getArea())) {
                //PARABUSCAR
                //Esto hay que refacorizarlo TODO y llevarlo a sub clases de torneo para poder
                //separar el comportamiento sin IFs
                if ($item->getEvento()->getTorneo()->getArea() == "Deportes"){
                    //disciplina en la que se queire inscribir
                    $diciplinas[] = strtoupper($item->getEvento()->getDisciplina()->getNombreCompleto());
                }else{
                    // si es una inscripcion doble en cultura lo patea de una
                    return false;
                }
                
            }
        }
        $cont = 0;// cuenta cuantas disciplinas no son de posta
        foreach ( $diciplinas as $item ){
            if (!strpos($item,"POSTA")){
                $cont++;
            }
        }// si se intenta iscribir en más de 1 disciplina q no se a de posta lo patea
        if ($cont>1){
            return false;
        }
        return true;
    }   
    

    /**
     * Add equipo
     *
     * @param \ResultadoBundle\Entity\Equipo $equipo
     * @return Participante
     */
    public function addEquipo(\ResultadoBundle\Entity\Equipo $equipo)
    {
        $this->equipos[] = $equipo;

        return $this;
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
     * Get nombreCompletoRaw
     *
     * @return string 
     */
    public function getNombreCompletoRaw()
    {
        $equipo = '';
        if (count($this->getEquipos())>0)
            $equipo = $this->getEquipos()[0]->getMunicipio();
        return "<strong>".$this->getNombreCompleto()."</strong><br><small>".$this->getDocumentoNro()."</small><br><small>".$equipo."</small>";
    }

    /**
     * Get medallero
     *
     * @return array 
     */
    public function getMedallero()
    {    
        $eventos = [];
        foreach ($this->getEquipos() as $equipo) {
            if ($equipo->hasMedalla()) {
                $eventos[] = $equipo->getEvento();
            }
        }
        return $eventos;
    }

    /**
     * Get municipios
     *
     * @return string 
     */
    public function getMunicipios()
    {    
        $mun = [];
        foreach ($this->getEquipos() as $equipo) {
            if (!in_array($equipo->getMunicipio()->getNombre(),$mun)){
                $mun[] = $equipo->getMunicipio()->getNombre();
            }
        }
        return $mun;
    }
}
