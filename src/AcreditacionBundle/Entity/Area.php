<?php

namespace AcreditacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * AcreditacionBundle\Entity\Area
 * @ORM\Table(name="services_juegosba_final.Area")
 * @ORM\Entity(repositoryClass="AcreditacionBundle\Entity\Repository\AreaRepository")
 */
class Area {

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
     * @ORM\Column(name="descripcion", type="text", nullable=true)
     */
    private $descripcion;

    /**
     * @var integer $cupoMaxPersonal
     * 
     * @ORM\Column(name="cupoMaxPersonal", type="integer")
     */
    private $cupoMaxPersonal = 0;

    /**
     * @var integer $cupoMaxHoteleria
     * 
     * @ORM\Column(name="cupoMaxHoteleria", type="integer")
     */
    private $cupoMaxHoteleria = 0;

    /**
     * @var integer $cupoMaxTransporte
     * 
     * @ORM\Column(name="cupoMaxTransporte", type="integer")
     */
    private $cupoMaxTransporte = 0;

    /**
     * @var float $cupoMaxPresupuesto
     * 
     * @ORM\Column(name="cupoMaxPresupuesto", type="float")
     */
    private $cupoMaxPresupuesto = 0;

    /**
     * @var ArrayCollection $cuposCategoriasPago
     * 
     * @ORM\OneToMany(targetEntity="AreaCategoriaPago", mappedBy="area", cascade={"persist", "remove"})
     */
    private $cuposCategoriasPago;

    /**
     * @var ArrayCollection $funcionesPermitidas
     * 
     * @ORM\ManyToMany(targetEntity="FuncionJuegos", inversedBy="areasAbarcadas")
     * @ORM\JoinTable(name="services_juegosba_final.Area_FuncionJuegos")
     */
    private $funcionesPermitidas;

    /**
     * @var ArrayCollection $usuariosResponsables
     * 
     * @ORM\ManyToMany(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinTable(name="services_juegosba_final.Area_Usuario", 
     *                      joinColumns={@ORM\JoinColumn(name="area_id", referencedColumnName="id")},
     *                      inverseJoinColumns={@ORM\JoinColumn(name="usuario_id", referencedColumnName="id")}
     *               )
     */
    private $usuariosResponsables;

    /**
     * @ORM\OneToMany(targetEntity="PersonalJuegos", mappedBy="area")
     */
    private $personal;
    
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
        $this->usuariosResponsables = new ArrayCollection();
        $this->funcionesPermitidas = new ArrayCollection();
        $this->categoriasPago = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Area
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Area
     */
    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Area
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
     * @return Area
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
     * Add usuariosResponsables
     *
     * @param \SeguridadBundle\Entity\Usuario $usuariosResponsables
     * @return Area
     */
    public function addUsuariosResponsable(\SeguridadBundle\Entity\Usuario $usuariosResponsables) {
        $this->usuariosResponsables[] = $usuariosResponsables;

        return $this;
    }

    /**
     * Remove usuariosResponsables
     *
     * @param \SeguridadBundle\Entity\Usuario $usuariosResponsables
     */
    public function removeUsuariosResponsable(\SeguridadBundle\Entity\Usuario $usuariosResponsables) {
        $this->usuariosResponsables->removeElement($usuariosResponsables);
    }

    /**
     * Get usuariosResponsables
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsuariosResponsables() {
        return $this->usuariosResponsables;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Area
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
     * @return Area
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

    /**
     * Add funcionesPermitidas
     *
     * @param \AcreditacionBundle\Entity\FuncionJuegos $funcionesPermitidas
     * @return Area
     */
    public function addFuncionesPermitida(\AcreditacionBundle\Entity\FuncionJuegos $funcionesPermitidas) {
        $this->funcionesPermitidas[] = $funcionesPermitidas;

        return $this;
    }

    /**
     * Remove funcionesPermitidas
     *
     * @param \AcreditacionBundle\Entity\FuncionJuegos $funcionesPermitidas
     */
    public function removeFuncionesPermitida(\AcreditacionBundle\Entity\FuncionJuegos $funcionesPermitidas) {
        $this->funcionesPermitidas->removeElement($funcionesPermitidas);
    }

    /**
     * Get funcionesPermitidas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFuncionesPermitidas() {
        return $this->funcionesPermitidas;
    }

    /**
     * 
     */
    public function __toString() {
        return $this->getNombre();
    }

    /**
     * Get Cupo maximos de personal del area
     * 
     * @return integer
     */
    function getCupoMaxPersonal() {
        return $this->cupoMaxPersonal;
    }

    /**
     * Get Cupo máximo de Hotelería disponible para el Área
     * 
     * @return integer
     */
    function getCupoMaxHoteleria() {
        return $this->cupoMaxHoteleria;
    }

    /**
     * Get Cupo máximo de plazas de transporte para en área
     * 
     * @return integer 
     */
    function getCupoMaxTransporte() {
        return $this->cupoMaxTransporte;
    }

    /**
     * Get Cupo máximo de presupuesto asignado al Área
     * 
     * @return float
     */
    function getCupoMaxPresupuesto() {
        return $this->cupoMaxPresupuesto;
    }

    /**
     * Get Cupos máximos por categorias de pago
     * 
     * @return \Doctrine\Common\Collections\Collection 
     */
    function getCuposCategoriasPago() {
        return $this->cuposCategoriasPago;
    }

    /**
     * Set Cupos máximos de personal de área
     * 
     * @param integer $cupoMaxPersonal
     * @return Area
     */
    function setCupoMaxPersonal($cupoMaxPersonal) {
        $this->cupoMaxPersonal = $cupoMaxPersonal;
    }

    /**
     * Set cupos máximos de plazas hoteleras del área
     * 
     * @param integer $cupoMaxHoteleria
     * @return Area
     */
    function setCupoMaxHoteleria($cupoMaxHoteleria) {
        $this->cupoMaxHoteleria = $cupoMaxHoteleria;
    }

    /**
     * Set Cupo máximo de plazas de transporte para el área
     * 
     * @param integer $cupoMaxTransporte
     * @return Area
     */
    function setCupoMaxTransporte($cupoMaxTransporte) {
        $this->cupoMaxTransporte = $cupoMaxTransporte;
    }

    /**
     * Set Cupo máximo de presupuesto para el área
     * 
     * @param float $cupoMaxPresupuesto
     * @return Area
     */
    function setCupoMaxPresupuesto($cupoMaxPresupuesto) {
        $this->cupoMaxPresupuesto = $cupoMaxPresupuesto;
    }

    /**
     * Set Colección de cupos de categorias de pago del área
     * 
     * @param ArrayCollection $cuposCategoriasPago
     * @return Area
     */
    function setCuposCategoriasPago(ArrayCollection $cuposCategoriasPago) {
        $this->cuposCategoriasPago = $cuposCategoriasPago;
    }

    /**
     * Add Cupos de categoria de pago para el área
     *
     * @param \AcreditacionBundle\Entity\AreaCategoriaPago $areaCategoriaPago
     * @return Area
     */
    public function addCuposCategoriasPago(\AcreditacionBundle\Entity\AreaCategoriaPago $areaCategoriaPago) {
        $this->cuposCategoriasPago[] = $areaCategoriaPago;
        $areaCategoriaPago->setArea($this);
        return $this;
    }

    /**
     * Remove cupo de categoria de pago del área
     *
     * @param \AcreditacionBundle\Entity\AreaCategoriaPago $areaCategoriaPago
     * @return boolean
     */
    public function removeCuposCategoriasPago(\AcreditacionBundle\Entity\AreaCategoriaPago $areaCategoriaPago) {
        $areaCategoriaPago->setArea(null);
        return $this->cuposCategoriasPago->removeElement($areaCategoriaPago);
    }

    /**
     * Add personal
     *
     * @param \AcreditacionBundle\Entity\PersonalJuegos $personal
     * @return Area
     */
    public function addPersonal(\AcreditacionBundle\Entity\PersonalJuegos $personal)
    {
        $this->personal[] = $personal;

        return $this;
    }

    /**
     * Remove personal
     *
     * @param \AcreditacionBundle\Entity\PersonalJuegos $personal
     */
    public function removePersonal(\AcreditacionBundle\Entity\PersonalJuegos $personal)
    {
        $this->personal->removeElement($personal);
    }

    /**
     * Get personal
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersonal()
    {
        return $this->personal;
    }
    
    /**
     * Get personalSinMovimientos
     *
     * @return array
     */
    public function getPersonalSinMovimientos()
    {
        $personalSinPagar = [];
        foreach ($this->personal as $persona)
        {
            $mov = $persona->getDatosTesoreria()->getUltMovimiento();
            if (!$mov){
                $personalSinPagar[] = $persona;
            }
        }
        return $personalSinPagar;
    }
    
    /**
     * Get personalReserva
     *
     * @return array
     */
    public function getPersonalConReserva()
    {
        $personalConReserva = [];
        /* Clasifica el personal segun su estado */
        foreach ($this->personal as $persona)
        {
            $mov = $persona->getDatosTesoreria()->getUltMovimiento();
            if ($mov && !$mov->estaPagado()){
                $personalConReserva[] = $persona;
            }
        }
        return $personalConReserva;
    }
    
    /**
     * Get personalPagado
     *
     * @return array
     */
    public function getPersonalPagado()
    {
        $personalPagado = [];
        /* Clasifica el personal segun su estado */
        foreach ($this->personal as $persona)
        {
            $mov = $persona->getDatosTesoreria()->getUltMovimiento();
            if ($mov && $mov->estaPagado()){
                $personalPagado[] = $persona;
            } 
        }
        return $personalPagado;
    }
    

    /**
     * Get FondosUtilizados
     *
     * @return array
     */
    public function getFondosUtilizados()
    {
        $arrFondos = [];
        foreach ($this->personal as $persona)
        {
            $mov = $persona->getDatosTesoreria()->getUltMovimiento();
            if ($mov && !in_array($mov->getFondo()->getAbreviatura(), $arrFondos)){
                $arrFondos[] = $mov->getFondo()->getAbreviatura();
            }
        }
        return $arrFondos;
    }
    
    /**
     * Get sumPersonal
     *
     * @return float
     */
    public function getSumPersonal($pers)
    {
        $monto = 0;
        foreach ($pers as $persona)
        {
            $monto += $persona->getDatosTesoreria()->getRemuneracion();
        }
        return $monto;
    }    
}
