<?php

namespace TesoreriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TesoreriaBundle\Entity\Egreso
 * @ORM\Entity()
 */
class Egreso extends Movimiento
{
    /**
     * @var DatosTesoreria $destinatario
     * 
     * @ORM\ManyToOne(targetEntity="DatosTesoreria", inversedBy="movimientos")
     * @ORM\JoinColumn(name="destinatario_id", referencedColumnName="id")
     */
    private $destinatario;

    /**
     * Get tipo
     * 
     * @return string Tipo de movimiento
     */
    public function getTipo(){
        return "Egreso";
    }

    /**
     * Set destinatario
     *
     * @param \TesoreriaBundle\Entity\DatosTesoreria $destinatario
     * @return MovimientoEgreso
     */
    public function setDestinatario(\TesoreriaBundle\Entity\DatosTesoreria $destinatario = null)
    {
        $this->destinatario = $destinatario;

        return $this;
    }

    /**
     * Get destinatario
     *
     * @return \TesoreriaBundle\Entity\DatosTesoreria 
     */
    public function getDestinatario()
    {
        return $this->destinatario;
    }
    
    /**
     * Get icon
     *
     * @return string fa-icon
     */
    public function getIcon()
    {
        return "";
    }     
}
