<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AcreditacionBundle\Entity;

use AcreditacionBundle\Entity\Area;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of AreaCategoriaPago
 *
 * @author kibunke
 */

/**
 * AcreditacionBundle\Entity\AreaCategoriaPago
 * @ORM\Table(name="AreaCategoriaPago")
 * @ORM\Entity(repositoryClass="AcreditacionBundle\Entity\Repository\AreaCategoriaPagoRepository")
 */
class AreaCategoriaPago {

    /**
     * @var integer $id
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $cupo
     * 
     * @ORM\Column(name="cupoMax", type="integer")
     */
    private $cupoMax = 0;

    /**
     * @var \AcreditacionBundle\Entity\Area
     * 
     * @ORM\ManyToOne(targetEntity="Area", inversedBy="cuposCategoriasPago")
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     */
    private $area;

    /**
     * @var \TesoreriaBundle\Entity\CategoriaPago
     * 
     * @ORM\ManyToOne(targetEntity="\TesoreriaBundle\Entity\CategoriaPago")
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     */
    private $categoria;

    /**
     * Set cupoMax
     *
     * @param integer $cupoMax
     * @return AreaCategoriaPago
     */
    public function setCupoMax($cupoMax) {
        $this->cupoMax = $cupoMax;

        return $this;
    }

    /**
     * Get cupoMax
     *
     * @return integer 
     */
    public function getCupoMax() {
        return $this->cupoMax;
    }

    /**
     * Set area
     *
     * @param \AcreditacionBundle\Entity\Area $area
     * @return AreaCategoriaPago
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
     * Set categoria
     *
     * @param \TesoreriaBundle\Entity\CategoriaPago $categoria
     * @return AreaCategoriaPago
     */
    public function setCategoria(\TesoreriaBundle\Entity\CategoriaPago $categoria = null) {
        $this->categoria = $categoria;
        return $this;
    }

    /**
     * Get categoria
     *
     * @return \TesoreriaBundle\Entity\CategoriaPago 
     */
    public function getCategoria() {
        return $this->categoria;
    }

    /**
     * Get Id de la tabla
     * @return integer
     */
    function getId() {
        return $this->id;
    }

    /**
     * Set id de la tabla
     * @param integer $id
     */
    function setId($id) {
        $this->id = $id;
    }

}
