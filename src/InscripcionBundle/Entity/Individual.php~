<?php

namespace InscripcionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * InscripcionBundle\Entity\Individual
 * @ORM\Entity()
 */

class Individual extends Planilla
{    
    public function getNewEquipo(){
        return new \ResultadoBundle\Entity\Individual();
    }
    
    public function getTemplate(){
        return "InscripcionBundle:Planilla:planillaIndividual.html.twig";
    }
    
    /**
     * Set responsableMunicipioNombre
     *
     * @param string $responsableMunicipioNombre
     *
     * @return Individual
     */
    public function setResponsableMunicipioNombre($responsableMunicipioNombre)
    {
        $this->responsableMunicipioNombre = $responsableMunicipioNombre;

        return $this;
    }

    /**
     * Get responsableMunicipioNombre
     *
     * @return string
     */
    public function getResponsableMunicipioNombre()
    {
        return $this->responsableMunicipioNombre;
    }

    /**
     * Set responsableMunicipioApellido
     *
     * @param string $responsableMunicipioApellido
     *
     * @return Individual
     */
    public function setResponsableMunicipioApellido($responsableMunicipioApellido)
    {
        $this->responsableMunicipioApellido = $responsableMunicipioApellido;

        return $this;
    }

    /**
     * Get responsableMunicipioApellido
     *
     * @return string
     */
    public function getResponsableMunicipioApellido()
    {
        return $this->responsableMunicipioApellido;
    }

    /**
     * Set directorTecnicoNombre
     *
     * @param string $directorTecnicoNombre
     *
     * @return Individual
     */
    public function setDirectorTecnicoNombre($directorTecnicoNombre)
    {
        $this->directorTecnicoNombre = $directorTecnicoNombre;

        return $this;
    }

    /**
     * Get directorTecnicoNombre
     *
     * @return string
     */
    public function getDirectorTecnicoNombre()
    {
        return $this->directorTecnicoNombre;
    }

    /**
     * Set directorTecnicoApellido
     *
     * @param string $directorTecnicoApellido
     *
     * @return Individual
     */
    public function setDirectorTecnicoApellido($directorTecnicoApellido)
    {
        $this->directorTecnicoApellido = $directorTecnicoApellido;

        return $this;
    }

    /**
     * Get directorTecnicoApellido
     *
     * @return string
     */
    public function getDirectorTecnicoApellido()
    {
        return $this->directorTecnicoApellido;
    }
}
