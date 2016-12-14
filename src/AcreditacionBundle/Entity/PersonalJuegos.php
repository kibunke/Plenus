<?php

namespace AcreditacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CommonBundle\Entity\Persona;
use SeguridadBundle\Entity\Usuario;
use TesoreriaBundle\Entity\DatosTesoreria;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AcreditacionBundle\Entity\PersonalJuegos
 * @ORM\Table(name="services_juegosba_final.PersonalJuegos")
 * @ORM\Entity(repositoryClass="AcreditacionBundle\Entity\Repository\PersonalJuegosRepository")
 */
class PersonalJuegos {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Area $area
     * 
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="personal")
     * @ORM\JoinColumn(name="area", referencedColumnName="id")
     * @Assert\NotNull(groups={"datosAcreditativos"})
     * */
    private $area;

    /**
     * @var FuncionJuegos $funcion
     * 
     * @ORM\ManyToOne(targetEntity="FuncionJuegos")
     * @ORM\JoinColumn(name="funcion", referencedColumnName="id")
     * @Assert\NotNull(groups={"datosAcreditativos"})
     */
    private $funcion;

    /**
     * @var Avatar $avatar
     * 
     * @ORM\OneToOne(targetEntity="Avatar", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="avatar", referencedColumnName="id",  nullable=true)
     */
    private $avatar;

    /**
     * @var boolean $accesoSector1
     *
     * @ORM\Column(name="accesoSector1",type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"datosAcreditativos"})
     * @Assert\Type(type="bool",groups={"datosAcreditativos"})
     */
    private $accesoSector1;

    /**
     * @var boolean $accesoSector2
     *
     * @ORM\Column(name="accesoSector2",type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"datosAcreditativos"})
     * @Assert\Type(type="bool",groups={"datosAcreditativos"})
     */
    private $accesoSector2;

    /**
     * @var boolean $accesoSector3
     *
     * @ORM\Column(name="accesoSector3",type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"datosAcreditativos"})
     * @Assert\Type(type="bool",groups={"datosAcreditativos"})
     */
    private $accesoSector3;

    /**
     * @var boolean $accesoSector4
     *
     * @ORM\Column(name="accesoSector4",type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"datosAcreditativos"})
     * @Assert\Type(type="bool",groups={"datosAcreditativos"})
     */
    private $accesoSector4;

    /**
     * @var boolean $accesoSector5
     *
     * @ORM\Column(name="accesoSector5",type="boolean", options={"default":0})
     * @Assert\NotNull(groups={"datosAcreditativos"})
     * @Assert\Type(type="bool", groups={"datosAcreditativos"})
     */
    private $accesoSector5;

    /**
     * @var string $letraIdentificacion
     * 
     * @ORM\Column(name="letraIdentificacion", type="string", length=1, nullable=true)
     */
    private $letraIdentificacion;

    /**
     * @var DatosOperativo $datosOperativo
     * 
     * @ORM\OneToOne(targetEntity="DatosOperativo", mappedBy="personalJuegos", cascade={"persist", "remove"})
     */
    private $datosOperativo;

    /**
     * @var DatosTesoreria $datosTesoreria
     * 
     * @ORM\OneToOne(targetEntity="TesoreriaBundle\Entity\DatosTesoreria", mappedBy="personalJuegos", cascade={"persist", "remove"})
     */
    private $datosTesoreria;

    /**
     * @var Persona $datosPersonales
     * 
     * @ORM\OneToOne(targetEntity="CommonBundle\Entity\Persona", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="datosPersonales", referencedColumnName="id")
     */
    private $datosPersonales;

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
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo;    
    
    public function __construct() {
       // $this->datosPersonales =  new Persona();
       $this->activo = true;
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PersonalJuegos
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
     * @return PersonalJuegos
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
     * Set area
     *
     * @param \AcreditacionBundle\Entity\Area $area
     * @return PersonalJuegos
     */
    public function setArea(\AcreditacionBundle\Entity\Area $area = null) {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return \AcreditacionBundle\Entity\Area 
     */
    public function getArea() {
        return $this->area;
    }

    /**
     * Set funcion
     *
     * @param \AcreditacionBundle\Entity\FuncionJuegos $funcion
     * @return PersonalJuegos
     */
    public function setFuncion(\AcreditacionBundle\Entity\FuncionJuegos $funcion = null) {
        $this->funcion = $funcion;

        return $this;
    }

    /**
     * Get funcion
     *
     * @return \AcreditacionBundle\Entity\FuncionJuegos 
     */
    public function getFuncion() {
        return $this->funcion;
    }

    /**
     * Set datosOperativo
     *
     * @param \AcreditacionBundle\Entity\DatosOperativo $datosOperativo
     * @return PersonalJuegos
     */
    public function setDatosOperativo(\AcreditacionBundle\Entity\DatosOperativo $datosOperativo = null) {
        $this->datosOperativo = $datosOperativo;
        $datosOperativo->setPersonalJuegos($this);
        return $this;
    }

    /**
     * Get datosOperativo
     *
     * @return \AcreditacionBundle\Entity\DatosOperativo 
     */
    public function getDatosOperativo() {
        return $this->datosOperativo;
    }

    /**
     * Set datosTesoreria
     *
     * @param \TesoreriaBundle\Entity\DatosTesoreria $datosTesoreria
     * @return PersonalJuegos
     */
    public function setDatosTesoreria(\TesoreriaBundle\Entity\DatosTesoreria $datosTesoreria = null) {
        $this->datosTesoreria = $datosTesoreria;
        $datosTesoreria->setPersonalJuegos($this);
        return $this;
    }

    /**
     * Get datosTesoreria
     *
     * @return \TesoreriaBundle\Entity\DatosTesoreria 
     */
    public function getDatosTesoreria() {
        return $this->datosTesoreria;
    }

    /**
     * Set datosPersonales
     *
     * @param \CommonBundle\Entity\Persona $datosPersonales
     * @return PersonalJuegos
     */
    public function setDatosPersonales(\CommonBundle\Entity\Persona $datosPersonales = null) {
        $this->datosPersonales = $datosPersonales;

        return $this;
    }

    /**
     * Get datosPersonales
     *
     * @return \CommonBundle\Entity\Persona 
     */
    public function getDatosPersonales() {
        return $this->datosPersonales;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return PersonalJuegos
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
     * @return PersonalJuegos
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

    public function __toString() {
        return 'PersonalJuegos';
    }

    /**
     * 
     */
    public function getStrAvatar() {
        return $this->getAvatar()->getArchivo();
    }

    public function hasAvatar() {
        return (($this->getAvatar()) && ($this->getAvatar()->getArchivo())) ? true : false;
    }

    /**
     * Set accesoSector1
     *
     * @param boolean $accesoSector1
     * @return PersonalJuegos
     */
    public function setAccesoSector1($accesoSector1) {
        $this->accesoSector1 = $accesoSector1;

        return $this;
    }

    /**
     * Get accesoSector1
     *
     * @return boolean 
     */
    public function getAccesoSector1() {
        return $this->accesoSector1;
    }

    /**
     * Set accesoSector2
     *
     * @param boolean $accesoSector2
     * @return PersonalJuegos
     */
    public function setAccesoSector2($accesoSector2) {
        $this->accesoSector2 = $accesoSector2;

        return $this;
    }

    /**
     * Get accesoSector2
     *
     * @return boolean 
     */
    public function getAccesoSector2() {
        return $this->accesoSector2;
    }

    /**
     * Set accesoSector3
     *
     * @param boolean $accesoSector3
     * @return PersonalJuegos
     */
    public function setAccesoSector3($accesoSector3) {
        $this->accesoSector3 = $accesoSector3;

        return $this;
    }

    /**
     * Get accesoSector3
     *
     * @return boolean 
     */
    public function getAccesoSector3() {
        return $this->accesoSector3;
    }

    /**
     * Set accesoSector4
     *
     * @param boolean $accesoSector4
     * @return PersonalJuegos
     */
    public function setAccesoSector4($accesoSector4) {
        $this->accesoSector4 = $accesoSector4;

        return $this;
    }

    /**
     * Get accesoSector4
     *
     * @return boolean 
     */
    public function getAccesoSector4() {
        return $this->accesoSector4;
    }

    /**
     * Set accesoSector5
     *
     * @param boolean $accesoSector5
     * @return PersonalJuegos
     */
    public function setAccesoSector5($accesoSector5) {
        $this->accesoSector5 = $accesoSector5;

        return $this;
    }

    /**
     * Get accesoSector5
     *
     * @return boolean 
     */
    public function getAccesoSector5() {
        return $this->accesoSector5;
    }

    /**
     * Set letraIdentificacion
     *
     * @param string $letraIdentificacion
     * @return PersonalJuegos
     */
    public function setLetraIdentificacion($letraIdentificacion) {
        $this->letraIdentificacion = $letraIdentificacion;

        return $this;
    }

    /**
     * Get letraIdentificacion
     *
     * @return string 
     */
    public function getLetraIdentificacion() {
        return $this->letraIdentificacion;
    }

    /**
     * Set avatar
     *
     * @param \AcreditacionBundle\Entity\Avatar $avatar
     * @return PersonalJuegos
     */
    public function setAvatar(\AcreditacionBundle\Entity\Avatar $avatar = null) {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return \AcreditacionBundle\Entity\Avatar 
     */
    public function getAvatar() {
        return $this->avatar;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return PersonalJuegos
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo()
    {
        return $this->activo;
    }    
}
