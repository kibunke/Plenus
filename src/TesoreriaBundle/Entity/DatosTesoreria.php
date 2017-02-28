<?php

namespace TesoreriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AcreditacionBundle\Entity\PersonalJuegos;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TesoreriaBundle\Entity\DatosTesoreria
 * @ORM\Table(name="DatosTesoreria")
 * @ORM\Entity(repositoryClass="TesoreriaBundle\Entity\Repository\DatosTesoreriaRepository")
 */
class DatosTesoreria {
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string $empleadoPublico
     * 
     * @ORM\Column(name="empleadoPublico", type="string")
     */
    private $empleadoPublico;
    
    /**
     * @var string $legajo
     * 
     * @ORM\Column(name="legajo", type="string", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     groups={"datosTesoreria"}
     * )
     */
    private $legajo;
    
    /**
     * @var string $cbu
     * 
     * @ORM\Column(name="cbu", type="string", nullable=true)
     * @Assert\Type(
     *     type="string",
     *     groups={"datosTesoreria"}
     * )
     */
    private $cbu;
    
    /**
     * @var Partido $pagoProvincia
     * 
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Provincia")
     * @ORM\JoinColumn(name="pagoProvincia", referencedColumnName="id")
     * @Assert\NotNull(groups={"datosTesoreria"})
     */
    private $pagoProvincia;
    
    /**
     * @var Partido $pagoPartido
     * 
     * @ORM\ManyToOne(targetEntity="CommonBundle\Entity\Municipio")
     * @ORM\JoinColumn(name="pagoPartido", referencedColumnName="id")
     * @Assert\NotNull(groups={"datosTesoreria"})
     */
    private $pagoPartido;
    /**
     * @var CategoriaPago $categoriaPago
     * 
     * @ORM\ManyToOne(targetEntity="CategoriaPago")
     * @ORM\JoinColumn(name="categoriaPago", referencedColumnName="id")
     * @Assert\NotNull(groups={"datosTesoreria"})
     */
    private $categoriaPago;
    
    /**
     * @var float $pagoEspecifico
     * 
     * @ORM\Column(name="pagoEspecifico", type="float", nullable=true)
     * @Assert\Type(
     *     type="float",
     *     groups={"datosTesoreria"}
     * )
     */
    private $pagoEspecifico;
    
    /**
     * @var PersonalJuegos $personalJuegos
     * 
     * @ORM\OneToOne(targetEntity="AcreditacionBundle\Entity\PersonalJuegos", inversedBy="datosTesoreria")
     * @ORM\JoinColumn(name="personalJuegos", referencedColumnName="id")
     */
    private $personalJuegos;
    
    /**
     * @ORM\OneToMany(targetEntity="Egreso", mappedBy="destinatario")
     */
    private $movimientos;
    
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
     * Constructor
     */
    public function __construct()
    {
        $this->pagoEspecifico = 0;
        $this->movimientos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return DatosTesoreria
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
     * @return DatosTesoreria
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
     * Set categoriaPago
     *
     * @param \TesoreriaBundle\Entity\CategoriaPago $categoriaPago
     * @return DatosTesoreria
     */
    public function setCategoriaPago(\TesoreriaBundle\Entity\CategoriaPago $categoriaPago = null)
    {
        $this->categoriaPago = $categoriaPago;

        return $this;
    }

    /**
     * Get categoriaPago
     *
     * @return \TesoreriaBundle\Entity\CategoriaPago 
     */
    public function getCategoriaPago()
    {
        return $this->categoriaPago;
    }

    /**
     * Set personalJuegos
     *
     * @param \AcreditacionBundle\Entity\PersonalJuegos $personalJuegos
     * @return DatosTesoreria
     */
    public function setPersonalJuegos(\AcreditacionBundle\Entity\PersonalJuegos $personalJuegos = null)
    {
        $this->personalJuegos = $personalJuegos;

        return $this;
    }

    /**
     * Get personalJuegos
     *
     * @return \AcreditacionBundle\Entity\PersonalJuegos 
     */
    public function getPersonalJuegos()
    {
        return $this->personalJuegos;
    }

    /**
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return DatosTesoreria
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
     * @return DatosTesoreria
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
     * Set pagoEspecifico
     *
     * @param float $pagoEspecifico
     * @return DatosTesoreria
     */
    public function setPagoEspecifico($pagoEspecifico)
    {
        $this->pagoEspecifico = $pagoEspecifico;

        return $this;
    }

    /**
     * Get pagoEspecifico
     *
     * @return float 
     */
    public function getPagoEspecifico()
    {
        return $this->pagoEspecifico;
    }

    /**
     * Set legajo
     *
     * @param string $legajo
     * @return DatosTesoreria
     */
    public function setLegajo($legajo)
    {
        $this->legajo = $legajo;

        return $this;
    }

    /**
     * Get legajo
     *
     * @return string 
     */
    public function getLegajo()
    {
        return $this->legajo;
    }

    /**
     * Set cbu
     *
     * @param string $cbu
     * @return DatosTesoreria
     */
    public function setCbu($cbu)
    {
        $this->cbu = $cbu;

        return $this;
    }

    /**
     * Get cbu
     *
     * @return string 
     */
    public function getCbu()
    {
        return $this->cbu;
    }   

    /**
     * Get pagoProvincia
     *
     * @return /CommonBundle/Entity/Provincia 
     */
    public function getPagoProvincia()
    {
        return $this->pagoProvincia;
    }

   

    /**
     * Get pagoPartido
     *
     * @return /CommonBundle/Entity/Partido 
     */
    public function getPagoPartido()
    {
        return $this->pagoPartido;
    }

    /**
     * Set pagoProvincia
     *
     * @param \CommonBundle\Entity\Provincia $pagoProvincia
     * @return DatosTesoreria
     */
    public function setPagoProvincia(\CommonBundle\Entity\Provincia $pagoProvincia = null)
    {
        $this->pagoProvincia = $pagoProvincia;

        return $this;
    }

    /**
     * Set pagoPartido
     *
     * @param \CommonBundle\Entity\Municipio $pagoPartido
     * @return DatosTesoreria
     */
    public function setPagoPartido(\CommonBundle\Entity\Municipio $pagoPartido = null)
    {
        $this->pagoPartido = $pagoPartido;

        return $this;
    }

    /**
     * Set empleadoPublico
     *
     * @param string $empleadoPublico
     * @return DatosTesoreria
     */
    public function setEmpleadoPublico($empleadoPublico)
    {
        $this->empleadoPublico = $empleadoPublico;

        return $this;
    }

    /**
     * Get empleadoPublico
     *
     * @return string 
     */
    public function getEmpleadoPublico()
    {
        return $this->empleadoPublico;
    }

    /**
     * Add movimientos
     *
     * @param \TesoreriaBundle\Entity\Egreso $movimiento
     * @return DatosTesoreria
     */
    public function addMovimiento(\TesoreriaBundle\Entity\Egreso $movimiento)
    {
        $this->movimientos[] = $movimiento;
        $movimiento->setDestinatario($this);

        return $this;
    }

    /**
     * Remove movimientos
     *
     * @param \TesoreriaBundle\Entity\Egreso $movimiento
     */
    public function removeMovimiento(\TesoreriaBundle\Entity\Egreso $movimiento)
    {
        $this->movimientos->removeElement($movimiento);
        $movimiento->setDestinatario(null);
    }

    /**
     * Get movimientos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMovimientos()
    {
        return $this->movimientos;
    }
    
    /**
     * hasPagoOrReserva
     *
     * @return boolean
     */
    public function hasPagoOrReserva()
    {
        return count($this->movimientos);
    }
    
    /**
     * Get remuneracion
     *
     * @return float
     */
    public function getRemuneracion()
    {
        if ($this->pagoEspecifico == 0)
            return $this->categoriaPago->getMonto();
        return $this->pagoEspecifico;
    }    

    /**
     * Get ultMovimiento
     *
     * @return string 
     */
    public function getUltMovimiento()
    {
        if ($this->movimientos->count()){
            return $this->movimientos->last();
        }
        return null;
    }
    
    /**
     * Get estadoIcon
     *
     * @return string fa-icon
     */
    public function getEstadoIcon()
    {
        if ($aux = $this->getUltMovimiento()){
            return $aux->getEstado()->getIcon();
        }
        return "fa-frown-o text-danger";
    }
    /**
     * tieneMovimiento
     *
     * @return boolean
     */
    public function tieneMovimiento()
    {
        if ($aux = $this->getUltMovimiento()){
            return $aux;
        }
        return false;
    }    
    /**
     * Get estadoText
     *
     * @return string 
     */
    public function getEstadoText()
    {
        if ($aux = $this->getUltMovimiento())
            return $aux->getEstado()->getTxt();
        return "";
    }

    /**
     * get MovimientosJson
     *
     * @return array
     */
    public function getMovimientosJson()
    {
        $arr = array();
        foreach($this->movimientos as $mov){
            $arr[] = array(
                    "id" => $mov->getId(),
                    "monto" => $mov->getMonto(),
                    "fondo" => array(
                                        "id" => $mov->getFondo()->getId(),
                                        "nombre" => $mov->getFondo()->getNombre(),
                                        "abrev" => $mov->getFondo()->getAbreviatura()
                                    ),
                    "estado" => array (
                                    "nombre" =>  $this->getEstadoText(),
                                    "icon" => $this->getEstadoIcon()
                                ),
                    "recibo" => $mov->getRecibo()?$mov->getRecibo()->getId():''
            );
        }
        return $arr;
    }
    
    /**
     * Get json
     *
     * @return string 
     */
    public function getJson($encode = true)
    {
        $arr = array(
                "id" => $this->getId(),
                "nombre" => ucwords(mb_strtolower($this->personalJuegos->getDatosPersonales()->getNombreCompleto(), 'UTF-8')),
                "dni" => $this->personalJuegos->getDatosPersonales()->getDocumentoNro(),
                "remuneracion" => $this->getRemuneracion(),
                "movimientos" => $this->getMovimientosJson(),
                "estado" => array (
                                    "nombre" =>  $this->getEstadoText(),
                                    "icon" => $this->getEstadoIcon()
                ),
                "categoria" => array(
                                    "id" => $this->getCategoriaPago()->getId(),
                                    "nombre" => $this->getCategoriaPago()->getNombre(),
                                    "monto" => $this->getCategoriaPago()->getMonto()
                                ),
                "area" => array(
                                    "id" => $this->getPersonalJuegos()->getArea()->getId(),
                                    "nombre" => $this->getPersonalJuegos()->getArea()->getNombre()
                                )
            );
        if ($encode)
            return json_encode($arr);
        return $arr;
    }
}
