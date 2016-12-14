<?php

namespace AcreditacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CommonBundle\Entity\Partido;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AcreditacionBundle\Entity\DatosOperativo
 * @ORM\Table(name="services_juegosba_final.DatosOperativo")
 * @ORM\Entity(repositoryClass="AcreditacionBundle\Entity\Repository\DatosOperativoRepository")
 */
class DatosOperativo {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean $necesitaTransporte
     * 
     * @ORM\Column(name="necesitaTransporte", type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"operativo"})
     */
    private $necesitaTransporte;

    /**
     * @var datetime $fechaIdaTransporte
     *
     * @ORM\Column(name="fechaIdaTransporte", type="date", nullable=true)
     */
    private $fechaIdaTransporte;

    /**
     * @var  Partido $idaOrigen
     * 
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Partido")
     * @ORM\JoinColumn(name="idaOrigen", referencedColumnName="id", nullable=true)
     */
    private $idaOrigen;

    /**
     * @var datetime $fechaRegresoTransporte
     *
     * @ORM\Column(name="fechaRegresoTransporte", type="date", nullable=true)
     */
    private $fechaRegresoTransporte;

    /**
     * @var  Partido $regresoDestino
     * 
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Partido")
     * @ORM\JoinColumn(name="regresoDestino", referencedColumnName="id", nullable=true)
     */
    private $regresoDestino;

    /**
     * @var boolean $necesitaHospedaje
     * 
     * @ORM\Column(name="necesitaHospedaje", type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"operativo"})
     */
    private $necesitaHospedaje;

    /**
     * @var datetime $fechaIngresoHospedaje
     *
     * @ORM\Column(name="fechaIngresoHospedaje", type="date", nullable=true)
     */
    private $fechaIngresoHospedaje;

    /**
     * @var datetime $fechaEgresoHospedaje
     *
     * @ORM\Column(name="fechaEgresoHospedaje", type="date", nullable=true)
     */
    private $fechaEgresoHospedaje;

    /**
     * @var boolean $certificado140908
     * 
     * @ORM\Column(name="certificado140908", type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"operativo"})
     */
    private $certificado140908;

    /**
     * @var boolean $certificadoEstablecimientoPrivado
     * 
     * @ORM\Column(name="certificadoEstablecimientoPrivado", type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"operativo"})
     */
    private $certificadoEstablecimientoPrivado;

    /**
     * @var boolean $certificadoLaboral
     * 
     * @ORM\Column(name="certificadoLaboral", type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"operativo"})
     */
    private $certificadoLaboral;

    /**
     * @var boolean $esPersonalGestion
     * 
     * @ORM\Column(name="esPersonalGestion", type="boolean", options={"default":0})
     */
    private $esPersonalGestion;

    /**
     * @var string $talleIndumentaria
     * 
     * @ORM\Column(name="talleIndumentaria", type="string", length=10, nullable=true)
     */
    private $talleIndumentaria;

    /**
     * @var boolean $vianda
     * 
     * @ORM\Column(name="vianda", type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"operativo"})
     */
    private $vianda;

    /**
     * @var PersonalJuegos $personalJuegos
     * 
     * @ORM\OneToOne(targetEntity="PersonalJuegos", inversedBy="datosOperativo")
     * @ORM\JoinColumn(name="personalJuegos", referencedColumnName="id" )
     * */
    private $personalJuegos;

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

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set necesitaTransporte
     *
     * @param boolean $necesitaTransporte
     * @return DatosOperativo
     */
    public function setNecesitaTransporte($necesitaTransporte) {
        $this->necesitaTransporte = $necesitaTransporte;

        return $this;
    }

    /**
     * Get necesitaTransporte
     *
     * @return boolean 
     */
    public function getNecesitaTransporte() {
        return $this->necesitaTransporte;
    }

    /**
     * Set fechaIdaTransporte
     *
     * @param \DateTime $fechaIdaTransporte
     * @return DatosOperativo
     */
    public function setFechaIdaTransporte($fechaIdaTransporte) {
        $this->fechaIdaTransporte = $fechaIdaTransporte;

        return $this;
    }

    /**
     * Get fechaIdaTransporte
     *
     * @return \DateTime 
     */
    public function getFechaIdaTransporte() {
        return $this->fechaIdaTransporte;
    }

    /**
     * Set fechaRegresoTransporte
     *
     * @param \DateTime $fechaRegresoTransporte
     * @return DatosOperativo
     */
    public function setFechaRegresoTransporte($fechaRegresoTransporte) {
        $this->fechaRegresoTransporte = $fechaRegresoTransporte;

        return $this;
    }

    /**
     * Get fechaRegresoTransporte
     *
     * @return \DateTime 
     */
    public function getFechaRegresoTransporte() {
        return $this->fechaRegresoTransporte;
    }

    /**
     * Set necesitaHospedaje
     *
     * @param boolean $necesitaHospedaje
     * @return DatosOperativo
     */
    public function setNecesitaHospedaje($necesitaHospedaje) {
        $this->necesitaHospedaje = $necesitaHospedaje;

        return $this;
    }

    /**
     * Get necesitaHospedaje
     *
     * @return boolean 
     */
    public function getNecesitaHospedaje() {
        return $this->necesitaHospedaje;
    }

    /**
     * Set fechaIngresoHospedaje
     *
     * @param \DateTime $fechaIngresoHospedaje
     * @return DatosOperativo
     */
    public function setFechaIngresoHospedaje($fechaIngresoHospedaje) {
        $this->fechaIngresoHospedaje = $fechaIngresoHospedaje;

        return $this;
    }

    /**
     * Get fechaIngresoHospedaje
     *
     * @return \DateTime 
     */
    public function getFechaIngresoHospedaje() {
        return $this->fechaIngresoHospedaje;
    }

    /**
     * Set fechaEgresoHospedaje
     *
     * @param \DateTime $fechaEgresoHospedaje
     * @return DatosOperativo
     */
    public function setFechaEgresoHospedaje($fechaEgresoHospedaje) {
        $this->fechaEgresoHospedaje = $fechaEgresoHospedaje;

        return $this;
    }

    /**
     * Get fechaEgresoHospedaje
     *
     * @return \DateTime 
     */
    public function getFechaEgresoHospedaje() {
        return $this->fechaEgresoHospedaje;
    }

    /**
     * Set certificado140908
     *
     * @param boolean $certificado140908
     * @return DatosOperativo
     */
    public function setCertificado140908($certificado140908) {
        $this->certificado140908 = $certificado140908;

        return $this;
    }

    /**
     * Get certificado140908
     *
     * @return boolean 
     */
    public function getCertificado140908() {
        return $this->certificado140908;
    }

    /**
     * Set Certificado de Establecimiento Privado
     *
     * @param boolean $certificadoEstablecimientoPrivado
     * @return DatosOperativo
     */
    public function setCertificadoEstablecimientoPrivado($certificadoEstablecimientoPrivado) {
        $this->certificadoEstablecimientoPrivado = $certificadoEstablecimientoPrivado;

        return $this;
    }

    /**
     * Get Certificado de Establecimiento Privado
     *
     * @return boolean 
     */
    public function getCertificadoEstablecimientoPrivado() {
        return $this->certificadoEstablecimientoPrivado;
    }

    /**
     * Set certificadoLaboral
     *
     * @param boolean $certificadoLaboral
     * @return DatosOperativo
     */
    public function setCertificadoLaboral($certificadoLaboral) {
        $this->certificadoLaboral = $certificadoLaboral;

        return $this;
    }

    /**
     * Get certificadoLaboral
     *
     * @return boolean 
     */
    public function getCertificadoLaboral() {
        return $this->certificadoLaboral;
    }

    /**
     * Set esPersonalGestion
     *
     * @param boolean $esPersonalGestion
     * @return DatosOperativo
     */
    public function setEsPersonalGestion($esPersonalGestion) {
        $this->esPersonalGestion = $esPersonalGestion;

        return $this;
    }

    /**
     * Get esPersonalGestion
     *
     * @return boolean 
     */
    public function getEsPersonalGestion() {
        return $this->esPersonalGestion;
    }

    /**
     * Set talleIndumentaria
     *
     * @param string $talleIndumentaria
     * @return DatosOperativo
     */
    public function setTalleIndumentaria($talleIndumentaria) {
        $this->talleIndumentaria = $talleIndumentaria;

        return $this;
    }

    /**
     * Get talleIndumentaria
     *
     * @return string 
     */
    public function getTalleIndumentaria() {
        return $this->talleIndumentaria;
    }

    /**
     * Set vianda
     *
     * @param boolean $vianda
     * @return DatosOperativo
     */
    public function setVianda($vianda) {
        $this->vianda = $vianda;

        return $this;
    }

    /**
     * Get vianda
     *
     * @return boolean 
     */
    public function getVianda() {
        return $this->vianda;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return DatosOperativo
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return DatosOperativo
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * Set idaOrigen
     *
     * @param \CommonBundle\Entity\Partido $idaOrigen
     * @return DatosOperativo
     */
    public function setIdaOrigen(\CommonBundle\Entity\Partido $idaOrigen = null) {
        $this->idaOrigen = $idaOrigen;

        return $this;
    }

    /**
     * Get idaOrigen
     *
     * @return \CommonBundle\Entity\Partido 
     */
    public function getIdaOrigen() {
        return $this->idaOrigen;
    }

    /**
     * Set regresoDestino
     *
     * @param \CommonBundle\Entity\Partido $regresoDestino
     * @return DatosOperativo
     */
    public function setRegresoDestino(\CommonBundle\Entity\Partido $regresoDestino = null) {
        $this->regresoDestino = $regresoDestino;

        return $this;
    }

    /**
     * Get regresoDestino
     *
     * @return \CommonBundle\Entity\Partido 
     */
    public function getRegresoDestino() {
        return $this->regresoDestino;
    }

    /**
     * Set personalJuegos
     *
     * @param \AcreditacionBundle\Entity\PersonalJuegos $personalJuegos
     * @return DatosOperativo
     */
    public function setPersonalJuegos(\AcreditacionBundle\Entity\PersonalJuegos $personalJuegos = null) {
        $this->personalJuegos = $personalJuegos;

        return $this;
    }

    /**
     * Get personalJuegos
     *
     * @return \AcreditacionBundle\Entity\PersonalJuegos 
     */
    public function getPersonalJuegos() {
        return $this->personalJuegos;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return DatosOperativo
     */
    public function setCreatedBy(\SeguridadBundle\Entity\Usuario $createdBy = null) {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \SeguridadBundle\Entity\Usuario 
     */
    public function getCreatedBy() {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \SeguridadBundle\Entity\Usuario $updatedBy
     * @return DatosOperativo
     */
    public function setUpdatedBy(\SeguridadBundle\Entity\Usuario $updatedBy = null) {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \SeguridadBundle\Entity\Usuario 
     */
    public function getUpdatedBy() {
        return $this->updatedBy;
    }
    

}
