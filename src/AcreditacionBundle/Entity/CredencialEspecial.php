<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AcreditacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use CommonBundle\Entity\TipoDocumento;
use CommonBundle\Entity\Persona;
use SeguridadBundle\Entity\Usuario;

/**
 * Description of Credencial Especial
 *
 * @author kibunke
 */

/**
 * @ORM\Entity
 * @ORM\Table(name="CredencialEspecial")
 * @ORM\Entity(repositoryClass="AcreditacionBundle\Entity\Repository\CredencialEspecialRepository")
 */
class CredencialEspecial {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $apellido
     * @ORM\Column(name="apellido",type="string")
     * @Assert\NotNull()
     */
    private $apellido;

    /**
     * @var string $nombre
     * @ORM\Column(name="nombre",type="string")
     * @Assert\NotNull()
     */
    private $nombre;

    /**
     * @var CommonBundle\Entity\TipoDocumento $documentoTipo
     * 
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\TipoDocumento")
     * @ORM\JoinColumn(name="documentoTipo", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $documentoTipo;

    /**
     * @var integer $documentoNro
     * 
     * @ORM\Column(name="documentoNro", type="integer")
     * @Assert\NotNull()
     */
    private $documentoNro;

    /**
     * @var Avatar $avatar
     * 
     * @ORM\OneToOne(targetEntity="Avatar", cascade={"persist","remove"})
     * @ORM\JoinColumn(name="avatar", referencedColumnName="id",  nullable=true)
     */
    private $avatar = null;

    /**
     * @var Area $area
     * 
     * @ORM\ManyToOne(targetEntity="Area")
     * @ORM\JoinColumn(name="area", referencedColumnName="id")
     * @Assert\NotNull()
     * */
    private $area;

    /**
     * @var FuncionJuegos $funcion
     * 
     * @ORM\ManyToOne(targetEntity="FuncionJuegos")
     * @ORM\JoinColumn(name="funcion", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $funcion;

    /**
     * @ORM\Column(name="letraIdentificacion", type="string", length=1)
     * @Assert\NotNull()
     */
    private $letraIdentificacion;

    /**
     * @var boolean $accesoSector1
     *
     * @ORM\Column(name="accesoSector1",type="boolean")
     * @Assert\NotNull()
     */
    private $accesoSector1 = false;

    /**
     * @var boolean $accesoSector2
     *
     * @ORM\Column(name="accesoSector2",type="boolean")
     * @Assert\NotNull()
     */
    private $accesoSector2 = false;

    /**
     * @var boolean $accesoSector3
     *
     * @ORM\Column(name="accesoSector3",type="boolean")
     * @Assert\NotNull()
     */
    private $accesoSector3 = false;

    /**
     * @var boolean $accesoSector4
     *
     * @ORM\Column(name="accesoSector4",type="boolean")
     * @Assert\NotNull()
     */
    private $accesoSector4 = false;

    /**
     * @var boolean $accesoSector5
     *
     * @ORM\Column(name="accesoSector5",type="boolean")
     * @Assert\NotNull()
     */
    private $accesoSector5 = false;

    /**
     * @var string $template
     *
     * @ORM\Column(name="template",type="string")
     * @Assert\NotNull()
     */
    private $template = "DEFAULT";

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

    /*     * *************************************************************************
     * SETTERs AND GETTERs
     * **********************
     */

    function getId() {
        return $this->id;
    }

    function getApellido() {
        return $this->apellido;
    }

    function getNombre() {
        return $this->nombre;
    }

    function getDocumentoTipo() {
        return $this->documentoTipo;
    }

    function getDocumentoNro() {
        return $this->documentoNro;
    }

    function getAvatar() {
        return $this->avatar;
    }

    function getArea() {
        return $this->area;
    }

    function getFuncion() {
        return $this->funcion;
    }

    function getLetraIdentificacion() {
        return $this->letraIdentificacion;
    }

    function getAccesoSector1() {
        return $this->accesoSector1;
    }

    function getAccesoSector2() {
        return $this->accesoSector2;
    }

    function getAccesoSector3() {
        return $this->accesoSector3;
    }

    function getAccesoSector4() {
        return $this->accesoSector4;
    }

    function getAccesoSector5() {
        return $this->accesoSector5;
    }

    function getTemplate() {
        return $this->template;
    }

    function getCreatedAt() {
        return $this->createdAt;
    }

    function getCreatedBy() {
        return $this->createdBy;
    }

    function getUpdatedAt() {
        return $this->updatedAt;
    }

    function getUpdatedBy() {
        return $this->updatedBy;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function setDocumentoTipo(TipoDocumento $documentoTipo) {
        $this->documentoTipo = $documentoTipo;
    }

    function setDocumentoNro($documentoNro) {
        $this->documentoNro = $documentoNro;
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

    function setAvatar(Avatar $avatar) {
        $this->avatar = $avatar;
    }

    function setArea(Area $area) {
        $this->area = $area;
    }

    function setFuncion(FuncionJuegos $funcion) {
        $this->funcion = $funcion;
    }

    function setLetraIdentificacion($letraIdentificacion) {
        $this->letraIdentificacion = $letraIdentificacion;
    }

    function setAccesoSector1($accesoSector1) {
        $this->accesoSector1 = $accesoSector1;
    }

    function setAccesoSector2($accesoSector2) {
        $this->accesoSector2 = $accesoSector2;
    }

    function setAccesoSector3($accesoSector3) {
        $this->accesoSector3 = $accesoSector3;
    }

    function setAccesoSector4($accesoSector4) {
        $this->accesoSector4 = $accesoSector4;
    }

    function setAccesoSector5($accesoSector5) {
        $this->accesoSector5 = $accesoSector5;
    }

    function setTemplate($template) {
        $this->template = $template;
    }

      /**
     * @param \DateTime $createdAt
     */
    function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

     
    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return CredencialEspecial
     */
    public function setCreatedBy(\SeguridadBundle\Entity\Usuario $createdBy = null) {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @param \DateTime $updatedAt
     */
    function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

     /**
     * Set setUpdatedBy
     *
     * @param \SeguridadBundle\Entity\Usuario $updatedBy
     */
    function setUpdatedBy(Usuario $updatedBy) {
        $this->updatedBy = $updatedBy;
    }

}
