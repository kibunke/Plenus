<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CommonBundle\Entity\Persona;
/**
 * ResultadoBundle\Entity\Competidor
 *
 * @ORM\Entity(repositoryClass="ResultadoBundle\Entity\Repository\CompetidorRepository")
 *
 */
class Competidor extends Persona
{
    /**
     * @ORM\OneToMany(targetEntity="EquiposCompetidores", mappedBy="competidor", cascade={"all"})
     * */
    protected $competidorEquipos;

    /**
     * Constructor
     */
    public function __construct($user = null)
    {
        $this->competidorEquipos = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct($user);
    }

    /**
     * validarAsignacion
     * valida que el competidor no este en dos equipos del mismo torneo
     * Esto no va as er necesario porque lo resuelve la PLANILLA DE INSCRIPCION
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

        //$torneos = new \Doctrine\Common\Collections\ArrayCollection();
        //$diciplinas = [];//METO TODAS LAS DISCIPLINAS DONDE PARTICIPA EL INSCRIPTO, las que ya esta isncripto y la que se quiere inscribir
        //foreach ( $this->getEquipos() as $item ){
        //    $torneos->add($item->getEvento()->getTorneo()->getArea());
        //    // disciplinas en las que ya esta inscripto
        //    $diciplinas[] = strtoupper($item->getEvento()->getDisciplina()->getNombreCompleto());
        //}
        //foreach ( $participante->getEquipos() as $item ){
        //    if (true === $torneos->contains($item->getEvento()->getTorneo()->getArea())) {
        //        //PARABUSCAR
        //        //Esto hay que refacorizarlo TODO y llevarlo a sub clases de torneo para poder
        //        //separar el comportamiento sin IFs
        //        if ($item->getEvento()->getTorneo()->getArea() == "Deportes"){
        //            //disciplina en la que se queire inscribir
        //            $diciplinas[] = strtoupper($item->getEvento()->getDisciplina()->getNombreCompleto());
        //        }else{
        //            // si es una inscripcion doble en cultura lo patea de una
        //            return false;
        //        }
        //
        //    }
        //}
        //$cont = 0;// cuenta cuantas disciplinas no son de posta
        //foreach ( $diciplinas as $item ){
        //    if (!strpos($item,"POSTA")){
        //        $cont++;
        //    }
        //}// si se intenta iscribir en más de 1 disciplina q no se a de posta lo patea
        //if ($cont>1){
        //    return false;
        //}
        //return true;
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
            if (!in_array($equipo->getPlanilla()->getMunicipio()->getNombre(),$mun)){
                $mun[] = $equipo->getPlanilla()->getMunicipio()->getNombre();
            }
        }
        return $mun;
    }

    public function toArray($aux = null)
    {
        return array(
                'rol' => is_object($aux)?$aux->getRol():'',
                'persona' => parent::toArray(),
            );
    }

    public function toFullArray($aux = null)
    {
        $equipos = [];
        foreach ($this->getEquipos() as $equipo) {
            if ($equipo->getPlanilla()->isAprobada()){
                $eventos = [];

                foreach ($equipo->getPlanilla()->getSegmento()->getEventos() as $evento) {
                    $eventos[] = [
                        "id" => $evento->getId(),
                        "nombre" => $evento->getNombreCompleto(),
                        "nombreRaw" => $evento->getNombreCompletoRaw(),
                        "existe" => $evento->containsEquipo($equipo)
                    ];
                }

                $cant = count($equipo->getEquipoCompetidores());
                $title = ($cant > 1) ? "EQUIPO" : $this->getNombreCompleto();
                $equipos[] = [
                    "id" => $equipo->getId(),
                    "cant" => $cant,
                    "nombre" => $equipo->getNombre(),
                    "title" => $title,
                    "planilla" => [
                        "id" => $equipo->getPlanilla()->getId(),
                    ],
                    "segmento" => [
                        "data" => $equipo->getPlanilla()->getSegmento()->toArray(),
                        "eventos" => $eventos
                    ]
                ];
            }
        }
        return array(
                'rol' => is_object($aux)?$aux->getRol():'',
                'persona' => parent::toArray(),
                'equipos'=> $equipos
            );
    }

    /**
     * Load
     */
    public function loadFromJson($json)
    {
        return parent::loadFromJson($json->persona);
    }

    public function getTipoPersona()
    {
        return 'Competidor';
    }

    public function getClass()
    {
        return 'ResultadoBundle:Competidor';
    }

    public function getPlanillas()
    {
        $planillas = [];
        foreach($this->getEquipos() as $equipo){
            $planillas[] = $equipo->getPlanilla();
        }
        return $planillas;
    }

    public function getSegmentos()
    {
        $segmentosIds = [];
        $segmentos = [];
        foreach($this->getEquipos() as $equipo){
            $segmento = $equipo->getPlanilla()->getSegmento();
            if (!in_array($segmento->getId(), $segmentosIds)){
                $segmentosIds[] = $segmento->getId();
                $segmentos[] = $segmento;
            }
        }
        return $segmentos;
    }

    /**
     * inscriptoEnSegmento
     * return si el competidor esta inscripto en el segmento
     * @return boolean
     */
    public function inscriptoEnSegmento($segmento)
    {
        foreach($this->getSegmentos() as $seg){
            if ($segmento == $seg){
                return true;
            }
        }
        return false;
    }

    public function getEquipos()
    {
        $equipos = [];

        foreach($this->competidorEquipos as $aux)
        {
            if (is_object($aux->getEquipo()))
                $equipos[] = $aux->getEquipo();
        }

        return $equipos;
    }

    public function getTorneosParticipa()
    {
        $torneos = [];
        foreach($this->getEquipos() as $equipo){
            foreach($equipo->getEtapas() as $etapa){
                if ($etapa->containsEquipo($equipo)){
                    $torneos[] = $etapa->getEvento()->getTorneo();
                }
            }
        }
        return $torneos;
    }

    public function getTorneosParticipaGandorMunicipal()
    {
        $torneos = [];
        foreach($this->getCompetidorEquipos() as $eqCom){
            if ($eqCom->getRol() == 'inscripto'){
                $equipo = $eqCom->getEquipo();
                foreach($equipo->getEtapas() as $etapa){
                    if ($etapa->containsEquipo($equipo)){
                        if (!$etapa->getEvento()->getSaltaControlEtapaMunicipal()){
                            $torneos[] = $etapa->getEvento()->getTorneo();
                        }
                    }
                }
            }
        }
        return $torneos;
    }

    /**
     * Add competidorEquipo
     *
     * @param \ResultadoBundle\Entity\EquiposCompetidores $competidorEquipo
     *
     * @return Competidor
     */
    public function addCompetidorEquipo(\ResultadoBundle\Entity\EquiposCompetidores $competidorEquipo)
    {
        $this->competidorEquipos[] = $competidorEquipo;

        return $this;
    }

    /**
     * Remove competidorEquipo
     *
     * @param \ResultadoBundle\Entity\EquiposCompetidores $competidorEquipo
     */
    public function removeCompetidorEquipo(\ResultadoBundle\Entity\EquiposCompetidores $competidorEquipo)
    {
        $this->competidorEquipos->removeElement($competidorEquipo);
    }

    /**
     * Get competidorEquipos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompetidorEquipos()
    {
        return $this->competidorEquipos;
    }

    /**
     * prepareToDelete
     *
     * @return ''
     */
    public function prepareToDelete()
    {
        $this->competidorEquipos = [];
        return $this;
    }

    /**
     * getEquipoCompetidorEnSegmento
     * devuelve una relacion EquiposCompetidores en un segmento especifico
     *
     * @return EquiposCompetidores
     */
    public function getEquipoCompetidorEnSegmento($segmento)
    {
        foreach ($this->getCompetidorEquipos() as $eqCom) {
            if ($eqCom->getEquipo()->getPlanilla()->getSegmento() == $segmento){
                $eqCom->setSale(NULL);
                $eqCom->setEntra(NULL);
                return $eqCom;
            }
        }
        return NULL;
    }
}
