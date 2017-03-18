<?php

namespace ResultadoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ResultadoBundle\Entity\CompetenciaSerieDistancia
 *
 * @ORM\Entity()
 *
 */
class CompetenciaSerieDistancia extends CompetenciaSerie
{
    private $mascara = "/^([0-9]{2}[.][0-9]{2})$/";
    
    /*
     * CONSTRUCT
     */
    public function __construct($user = NULL) {
       parent::__construct($user);
    }
    
   /**
     * recalcularOrdenNatural
     *
     * @return array
     */
    public function recalcularOrdenNatural($serie)
    {
        $plazas = $serie->getPlazas();
        foreach ( $plazas as $plaza )
        {
            $marcas[] = str_replace(".", "", $plaza->getMarca());
            $plAux[] = $plaza->getId();
        }
        
        $arr = array_multisort(
                    $marcas, SORT_DESC, SORT_NUMERIC,
                    $plAux
                );
        foreach ( $plazas as $plaza){
            if ($plaza->getMarca()!='00.00'){
                $plaza->setOrdenNatural(array_search($plaza->getId(), $plAux));
            }
        }        
        return $arr;
    }
    
    /**
     * aplicar mascara
     * Plica la mascara del tipo de competencia al parametro
     *
     * @return string
     */
    public function aplicarMascara($plaza,$plazaDeReferencia)
    {
        $marca = $plaza->getMarca();
        if ($plaza->getId()==$plazaDeReferencia->getId() || $marca == "00.00"){
            if ($marca == "00.00")
                return "<strike>".$marca."</strike>";
            return "<strong>".$marca."</strong>";
        }else{
            $dif = (integer)str_replace(".", "", $plazaDeReferencia->getMarca())-(integer)str_replace(".", "", $marca);
            return "<strong>".$marca."</strong> <small style='color:#a94442'>(-".wordwrap(str_pad($dif, 4,0, STR_PAD_LEFT), 2, '.', 1).")</small>";
        }        
    }
    
    /*
     * validarMarca
     *
     * @return string
     */
    public function validarMarca($marca)
    {
        if(!preg_match($this->mascara, $marca))
        {
            return false;   
        }
        return true;
    }
    
    /*
     * get etiueta
     *
     * @return string
     */
    public function getEtiqueta()
    {
        return "Distancia";
    }
    
    /*
     * get DefaultMarca
     *
     * @return string
     */
    public function getDefaultMarca()
    {
        return "00.00";
    }
    
    /*
     * get Mascara
     *
     * @return string
     */
    public function getMascara()
    {
        return "99.99";
    }
    
    /*
     * get AyudaMarca
     *
     * @return string
     */
    public function getAyudaMarca()
    {
        return "<div class='bg-danger' style='padding:10px 50px'>
                    <h3 style='padding:0;margin:0;font-size:16px'><b>Atención</b></h3>
                    <li>Los valores representan METROS.CENTÍMETROS</li>
                    <li>El rango válido es de 00.00 a 99.99</li>
                    <li>Ej: 55.98</li>
                </div>";
    }    
}
