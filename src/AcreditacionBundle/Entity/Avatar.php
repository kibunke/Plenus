<?php

namespace AcreditacionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AcreditacionBundle\Entity\Avatar
 *
 * @ORM\Table(name="services_juegosba_final.Avatar")
 * @ORM\Entity
 */
class Avatar {

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
     * @ORM\Column(name="nombre", type="string", length=50)
     */
    private $nombre;

    /**
     * @var string $tipo
     *
     * @ORM\Column(name="tipo", type="string", length=50)
     */
    private $tipo;

    /**
     * @var string $archivo
     *
     * @ORM\Column(name="archivo", type="blob")
     */
    private $archivo;

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
     * Set nombre
     *
     * @param string $nombre
     * @return Avatar
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
     * Set tipo
     *
     * @param string $tipo
     * @return Avatar
     */
    public function setTipo($tipo) {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * Set imagen
     *
     * @param string $imagen
     * @return Avatar
     */
    public function setImagen($imagen) {

        $this->imagen = $imagen;

        return $this;
    }

    /**
     * Set archivo
     *
     * @param string $archivo
     * @return Avatar
     */
    public function setArchivo($archivo) {
        $this->archivo = base64_decode($archivo);
        //$this->archivo = htmlspecialchars_decode($archivo);
        //return base64_encode($this->archivo);
        return $this;
    }

    /**
     * Get archivo
     *
     * @return string 
     */
    public function getArchivo() {
        //return $this->archivo;


/*
        if ($this->archivo) {
            $resource = $this->archivo;
            rewind($resource);
            $data = null;
            while (!feof($resource)) {
                $data .= fread($resource, 1024);
            }
            //return $data;
            return base64_encode($data);

            //return base64_encode(stream_get_contents($this->archivo));
        }
        return null;*/
        if(gettype($this->archivo) == 'resource'){
            $resource = $this->archivo;
            rewind($resource);
            $data = null;
            while (!feof($resource)) {
                $data .= fread($resource, 1024);
            }
            //return $data;
            return base64_encode($data);
            //return htmlspecialchars($data);
        }
        //return var_dump($this->archivo);
        
        //if($this->archivo){
        return ($this->archivo)?base64_encode($this->archivo):null;
        //}else{
         //   return null;
        //}
        
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Avatar
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
     * @return Avatar
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
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Avatar
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
     * @return Avatar
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
     * Get imagen
     *
     * @return string 
     */
    public function getImagen() {
        $resource = $this->imagen;
        rewind($resource);
        $data = null;
        while (!feof($resource)) {
            $data .= fread($resource, 8192);
        }
        //return $data;
        return base64_encode($this->imagen);
        //return $this->imagen;
    }

    /**
     * Setea y carga una imagen
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $archivo
     * @return Avatar
     */
    public function uploadImagen(\Symfony\Component\HttpFoundation\File\UploadedFile $archivo = NULL) {

        if ($archivo) {
            $stream = fopen($archivo->getPathname(), 'rb');
            $this->archivo = stream_get_contents($stream);
            $this->tipo = $archivo->getClientMimeType();
            $this->nombre = $archivo->getClientOriginalName();
        }

        return $this;
    }

}
