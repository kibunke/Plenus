<?php

namespace TesoreriaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TesoreriaBundle\Entity\Recibo
 * @ORM\Entity()
 */
class Recibo
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string $notas
     * 
     * @ORM\Column(name="notas", type="text", nullable = true)
     */
    private $notas;

     /**
     * @var boolean
     *
     * @ORM\Column(name="impreso", type="boolean")
     */
    private $impreso;
    
    /**
     * @var datetime $fechaImpresion
     *
     * @ORM\Column(name="fechaImpresion", type="datetime", nullable = true)
     */
    private $fechaImpresion;

     /**
     * @var boolean
     *
     * @ORM\Column(name="anulado", type="boolean")
     */
    private $anulado;
    
    /**
     * @var datetime $fechaAnulacion
     *
     * @ORM\Column(name="fechaAnulacion", type="datetime", nullable = true)
     */
    private $fechaAnulacion;
    
    /**
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="anuladoPor", referencedColumnName="id")
     */
    private $anuladoPor;
    
    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;    
    
    /**
     * @ORM\ManyToOne(targetEntity="SeguridadBundle\Entity\Usuario")
     * @ORM\JoinColumn(name="createdBy", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @ORM\OneToOne(targetEntity="Movimiento", mappedBy="recibo")
     */
    private $movimiento;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->impreso = false;
        $this->anulado = false;
        $this->createdAt = new \DateTime();
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
     * Set notas
     *
     * @param string $notas
     * @return Recibo
     */
    public function setNotas($notas)
    {
        $this->notas = $notas;

        return $this;
    }

    /**
     * Get notas
     *
     * @return string 
     */
    public function getNotas()
    {
        return $this->notas;
    }

    /**
     * Set impreso
     *
     * @param boolean $impreso
     * @return Recibo
     */
    public function setImpreso($impreso)
    {
        $this->impreso = $impreso;

        return $this;
    }

    /**
     * Get impreso
     *
     * @return boolean 
     */
    public function getImpreso()
    {
        return $this->impreso;
    }

    /**
     * Set fechaImpresion
     *
     * @param \DateTime $fechaImpresion
     * @return Recibo
     */
    public function setFechaImpresion($fechaImpresion)
    {
        $this->fechaImpresion = $fechaImpresion;

        return $this;
    }

    /**
     * Get fechaImpresion
     *
     * @return \DateTime 
     */
    public function getFechaImpresion()
    {
        return $this->fechaImpresion;
    }

    /**
     * Set anulado
     *
     * @param boolean $anulado
     * @return Recibo
     */
    public function setAnulado($anulado)
    {
        $this->anulado = $anulado;

        return $this;
    }

    /**
     * Get anulado
     *
     * @return boolean 
     */
    public function getAnulado()
    {
        return $this->anulado;
    }

    /**
     * Set fechaAnulacion
     *
     * @param \DateTime $fechaAnulacion
     * @return Recibo
     */
    public function setFechaAnulacion($fechaAnulacion)
    {
        $this->fechaAnulacion = $fechaAnulacion;

        return $this;
    }

    /**
     * Get fechaAnulacion
     *
     * @return \DateTime 
     */
    public function getFechaAnulacion()
    {
        return $this->fechaAnulacion;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Recibo
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
     * Set createdBy
     *
     * @param \SeguridadBundle\Entity\Usuario $createdBy
     * @return Recibo
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
     * Set anuladoPor
     *
     * @param \SeguridadBundle\Entity\Usuario $anuladoPor
     * @return Recibo
     */
    public function setAnuladoPor(\SeguridadBundle\Entity\Usuario $anuladoPor = null)
    {
        $this->anuladoPor = $anuladoPor;

        return $this;
    }

    /**
     * Get anuladoPor
     *
     * @return \SeguridadBundle\Entity\Usuario 
     */
    public function getAnuladoPor()
    {
        return $this->anuladoPor;
    }

    /**
     * Get monto
     *
     * @return float
     */
    public function getMonto()
    {
        return number_format($this->movimiento->getMonto(), 2, ',', '.');
    }

    /**
     * Get montoEnLetras
     *
     * @return string
     */
    public function getMontoEnLetras()
    {
        return strtolower($this->ValorEnLetras($this->movimiento->getMonto()))." con 00/100";
    }
    
    /**
     * Get fechaFormateada
     *
     * @return string
     */
    public function getFechaFormateada()
    {
        $mes = ['Enero','Febrero','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
        $str = $this->createdAt->format('d')." de ";
        $str .= $mes[$this->createdAt->format('n')-1]." de ";
        $str .= $this->createdAt->format('Y');
        return $str;
    }
    
    private $Void = "";
    private $SP = " ";
    private $Dot = ".";
    private $Zero = "0";
    private $Neg = "Menos";

    public function ValorEnLetras($x, $Moneda="" )
    {
        $s="";
        $Ent="";
        $Frc="";
        $Signo="";
    
        if(floatVal($x) < 0)
         $Signo = $this->Neg . " ";
        else
         $Signo = "";
    
        if(intval(number_format($x,2,'.','') )!=$x) //<- averiguar si tiene decimales
          $s = number_format($x,2,'.','');
        else
          $s = number_format($x,0,'.','');
    
        $Pto = strpos($s, $this->Dot);
    
        if ($Pto === false)
        {
          $Ent = $s;
          $Frc = $this->Void;
        }
        else
        {
          $Ent = substr($s, 0, $Pto );
          $Frc =  substr($s, $Pto+1);
        }
    
        if($Ent == $this->Zero || $Ent == $this->Void)
           $s = "Cero ";
        elseif( strlen($Ent) > 7)
        {
           $s = $this->SubValLetra(intval( substr($Ent, 0,  strlen($Ent) - 6))) .
                 "Millones " . $this->SubValLetra(intval(substr($Ent,-6, 6)));
        }
        else
        {
          $s = $this->SubValLetra(intval($Ent));
        }
    
        if (substr($s,-9, 9) == "Millones " || substr($s,-7, 7) == "Mill�n ")
           $s = $s . "de ";
    
        $s = $s . $Moneda;
    
        if($Frc != $this->Void)
        {
           $s = $s . " Con " . $this->SubValLetra(intval($Frc)) . "Centavos";
           //$s = $s . " " . $Frc . "/100";
        }
        return ($Signo . $s);
    
    }
    
    
    private function SubValLetra($numero)
    {
        $Ptr="";
        $n=0;
        $i=0;
        $x ="";
        $Rtn ="";
        $Tem ="";
    
        $x = trim("$numero");
        $n = strlen($x);
    
        $Tem = $this->Void;
        $i = $n;
    
        while( $i > 0)
        {
           $Tem = $this->Parte(intval(substr($x, $n - $i, 1).
                               str_repeat($this->Zero, $i - 1 )));
           If( $Tem != "Cero" )
              $Rtn .= $Tem . $this->SP;
           $i = $i - 1;
        }
    
    
        //--------------------- GoSub FiltroMil ------------------------------
        $Rtn=str_replace(" Mil Mil", " Un Mil", $Rtn );
        while(1)
        {
           $Ptr = strpos($Rtn, "Mil ");
           If(!($Ptr===false))
           {
              If(! (strpos($Rtn, "Mil ",$Ptr + 1) === false ))
                $this->ReplaceStringFrom($Rtn, "Mil ", "", $Ptr);
              Else
               break;
           }
           else break;
        }
    
        //--------------------- GoSub FiltroCiento ------------------------------
        $Ptr = -1;
        do{
           $Ptr = strpos($Rtn, "Cien ", $Ptr+1);
           if(!($Ptr===false))
           {
              $Tem = substr($Rtn, $Ptr + 5 ,1);
              if( $Tem == "M" || $Tem == $this->Void)
                 ;
              else
                 $this->ReplaceStringFrom($Rtn, "Cien", "Ciento", $Ptr);
           }
        }while(!($Ptr === false));
    
        //--------------------- FiltroEspeciales ------------------------------
        $Rtn=str_replace("Diez Un", "Once", $Rtn );
        $Rtn=str_replace("Diez Dos", "Doce", $Rtn );
        $Rtn=str_replace("Diez Tres", "Trece", $Rtn );
        $Rtn=str_replace("Diez Cuatro", "Catorce", $Rtn );
        $Rtn=str_replace("Diez Cinco", "Quince", $Rtn );
        $Rtn=str_replace("Diez Seis", "Dieciseis", $Rtn );
        $Rtn=str_replace("Diez Siete", "Diecisiete", $Rtn );
        $Rtn=str_replace("Diez Ocho", "Dieciocho", $Rtn );
        $Rtn=str_replace("Diez Nueve", "Diecinueve", $Rtn );
        $Rtn=str_replace("Veinte Un", "Veintiun", $Rtn );
        $Rtn=str_replace("Veinte Dos", "Veintidos", $Rtn );
        $Rtn=str_replace("Veinte Tres", "Veintitres", $Rtn );
        $Rtn=str_replace("Veinte Cuatro", "Veinticuatro", $Rtn );
        $Rtn=str_replace("Veinte Cinco", "Veinticinco", $Rtn );
        $Rtn=str_replace("Veinte Seis", "Veintiseis", $Rtn );
        $Rtn=str_replace("Veinte Siete", "Veintisiete", $Rtn );
        $Rtn=str_replace("Veinte Ocho", "Veintiocho", $Rtn );
        $Rtn=str_replace("Veinte Nueve", "Veintinueve", $Rtn );
    
        //--------------------- FiltroUn ------------------------------
        If(substr($Rtn,0,1) == "M") $Rtn = "Un " . $Rtn;
        //--------------------- Adicionar Y ------------------------------
        for($i=65; $i<=88; $i++)
        {
          If($i != 77)
             $Rtn=str_replace("a " . Chr($i), "* y " . Chr($i), $Rtn);
        }
        $Rtn=str_replace("*", "a" , $Rtn);
        return($Rtn);
    }
    
    
    private function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr)
    {
      $x = substr($x, 0, $Ptr)  . $NewWrd . substr($x, strlen($OldWrd) + $Ptr);
    }
    
    
    private function Parte($x)
    {
        $Rtn='';
        $t='';
        $i='';
        Do
        {
          switch($x)
          {
             Case 0:  $t = "Cero";break;
             Case 1:  $t = "Un";break;
             Case 2:  $t = "Dos";break;
             Case 3:  $t = "Tres";break;
             Case 4:  $t = "Cuatro";break;
             Case 5:  $t = "Cinco";break;
             Case 6:  $t = "Seis";break;
             Case 7:  $t = "Siete";break;
             Case 8:  $t = "Ocho";break;
             Case 9:  $t = "Nueve";break;
             Case 10: $t = "Diez";break;
             Case 20: $t = "Veinte";break;
             Case 30: $t = "Treinta";break;
             Case 40: $t = "Cuarenta";break;
             Case 50: $t = "Cincuenta";break;
             Case 60: $t = "Sesenta";break;
             Case 70: $t = "Setenta";break;
             Case 80: $t = "Ochenta";break;
             Case 90: $t = "Noventa";break;
             Case 100: $t = "Cien";break;
             Case 200: $t = "Doscientos";break;
             Case 300: $t = "Trescientos";break;
             Case 400: $t = "Cuatrocientos";break;
             Case 500: $t = "Quinientos";break;
             Case 600: $t = "Seiscientos";break;
             Case 700: $t = "Setecientos";break;
             Case 800: $t = "Ochocientos";break;
             Case 900: $t = "Novecientos";break;
             Case 1000: $t = "Mil";break;
             Case 1000000: $t = "Millón";break;
          }
    
          If($t == $this->Void)
          {
            $i = $i + 1;
            $x = $x / 1000;
            If($x== 0) $i = 0;
          }
          else
             break;
    
        }while($i != 0);
    
        $Rtn = $t;
        Switch($i)
        {
           Case 0: $t = $this->Void;break;
           Case 1: $t = " Mil";break;
           Case 2: $t = " Millones";break;
           Case 3: $t = " Billones";break;
        }
        return($Rtn . $t);
    }

    /**
     * Set movimiento
     *
     * @param \TesoreriaBundle\Entity\Movimiento $movimiento
     * @return Recibo
     */
    public function setMovimiento(\TesoreriaBundle\Entity\Movimiento $movimiento = null)
    {
        $this->movimiento = $movimiento;

        return $this;
    }

    /**
     * Get movimiento
     *
     * @return \TesoreriaBundle\Entity\Movimiento 
     */
    public function getMovimiento()
    {
        return $this->movimiento;
    }
}
