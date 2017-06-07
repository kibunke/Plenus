<?php

namespace SeguridadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * SeguridadBundle\Entity\LogLogin
 *
 * @ORM\Table(name="LogLogin")
 * @ORM\Entity(repositoryClass="SeguridadBundle\Entity\Repository\LogLoginRepository")
 * --
 * -- Estructura de tabla para la tabla `LogLogin`
 * --
 * CREATE TABLE IF NOT EXISTS `LogLogin` (
 *   `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
 *   `intentos` int(11) NOT NULL DEFAULT '0',
 *   PRIMARY KEY (`ip`)
 * ) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 */

class LogLogin
{
    /**
     * @var string $ip
     * @ORM\Id
     * @ORM\Column(name="ip", type="string", length=20)
     */
    private $ip;

    /**
     * @var integer $intentos
     * @ORM\Column(name="intentos", type="integer")
     */
    private $intentos;

    public function __construct($ip){
        $this->ip = $ip;
        $this->intentos = 0;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return LogLogin
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set intentos
     *
     * @param integer $intentos
     *
     * @return LogLogin
     */
    public function setIntentos($intentos)
    {
        $this->intentos = $intentos;

        return $this;
    }

    /**
     * Get intentos
     *
     * @return integer
     */
    public function getIntentos()
    {
        return $this->intentos;
    }

    /**
     * incrementar
     *
     * @return integer
     */
    public function incrementar()
    {
        return $this->intentos = $this->intentos + 1;
    }
}
