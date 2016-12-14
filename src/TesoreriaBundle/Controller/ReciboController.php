<?php

namespace TesoreriaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use TesoreriaBundle\Entity\DatosTesoreria;
use TesoreriaBundle\Entity\Recibo;
use AcreditacionBundle\Entity\Area;
use TesoreriaBundle\Form\DatosTesoreriaType;
use CommonBundle\PDFs\DocumentoPDF;
use Symfony\Component\HttpFoundation\Response;

/**
 * Recibo controller.
 *
 * @Route("/tesoreria/recibo")
 * @Security("has_role('ROLE_TESORERIA_RECIBO')")
 */
class ReciboController extends Controller
{
    /**
     * print Recibo.
     *
     * @Route("/{id}/print", name="tesoreria_recibo_print")
     * @Method("GET")
     * @Security("has_role('ROLE_TESORERIA_RECIBO_PRINT')")
     * 
     */
    public function printAction(Request $request, Recibo $recibo)
    {
        $nombre = "";
        $mov = $recibo->getMovimiento();
        
        
        $pdf = new DocumentoPDF();
        $pdf->setTextoHeader('Recibo');
        $pdf->setMargenRight(20);
        $pdf->setMargenTop(30);
        $pdf->init();
        $pdf->deletePage(1);
        if ($mov){
            $persona = $mov->getDestinatario()->getPersonalJuegos();
            $this->addPage($pdf,$persona);
            $nombre = $persona->getDatosPersonales()->getNombreCompleto();
        }
        
        
        return new Response($pdf->Output('Recibo-'.$nombre.'.pdf','D'));
    }
    
    private function addPage($pdf,$per)
    {
        $mov = $per->getDatosTesoreria()->getUltMovimiento();
        $recibo = $mov->getRecibo();
        if ($mov && $recibo){
            $fondo = $mov->getFondo();
            $persona = $per->getDatosPersonales();
            
            $pdf->AddPage('P', 'A4');
            if ($recibo->getImpreso()){
                $pdf->writeHTML('<div style="width:100%;text-align:center">COPIA DEL ORIGINAL</div>');                
            }
            $head = 
            '<table style="font-size:13px;width:100%;" cellpadding="2">
                <tr>
                    <td style="width:30%;"></td>
                    <td style="width:4%"></td>
                    <td style="width:66%"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td style="font-size:15px;text-align:right;"><b>Rendición Nº</b> '.str_pad($recibo->getId(), 10, "0", STR_PAD_LEFT).'</td>
                </tr>
                <tr>
                    <td rowspan="5">
                        <img style="border:1px solid silver" src="data:image/jpg;base64,'.$fondo->getEntidad()->getLogo().'" alt="Logo"/>  
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>'.$fondo->getEntidad()->getNombre().'</b></td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>'.$fondo->getEntidad()->getRazonSocial().'</b></td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>'.$fondo->getEntidad()->getDatosFiscales().'</b></td>
                </tr>
                <tr>
                    <td></td>
                    <td><b>'.$fondo->getEntidad()->getDomicilioFiscal().'</b></td>
                </tr>
            </table>';
    
            $pdf->writeHTML("<div>".$head."</div>");        
            $html = $fondo->getModeloRecibo();
            $html = str_replace("[APELLIDOS]",$persona->getApellido(),$html);
            $html = str_replace("[NOMBRES]",$persona->getNombre(),$html);
            $html = str_replace("[FECHA]",$recibo->getFechaFormateada(),$html);
            $html = str_replace("[MONTO]",$recibo->getMonto(),$html);
            $html = str_replace("[MONTOENLETRAS]",$recibo->getMontoEnLetras(),$html);
            $html = str_replace("[TIPODNI]",$persona->getDocumentoTipo()->getNombre(),$html);
            $html = str_replace("[DNI]",$persona->getDocumentoNro(),$html);
            $pdf->writeHTML($html);
            if (!$recibo->getImpreso()){
                $em = $this->getDoctrine()->getManager();
                $recibo->setImpreso(true);
                $recibo->setFechaImpresion(new \DateTime());
                try{
                    $em->flush();
                } catch (Exception $e) {
                    $pdf->deletePage($pdf->getAliasNumPage());
                }
            }else{
                $pdf->writeHTML('<div style="width:100%;text-align:center">COPIA DEL ORIGINAL</div>');
            }
        }        
    }
    
    /**
     * print Recibos de una Area.
     *
     * @Route("/{area}/area/print", name="tesoreria_recibo_print_area")
     * @Method("GET")
     * @Security("has_role('ROLE_TESORERIA_RECIBO_PRINT')")
     */
    public function printAreaAction(Request $request, Area $area)
    {
        $pdf = new DocumentoPDF();
        $pdf->setTextoHeader('Recibo');
        $pdf->setMargenRight(20);
        $pdf->setMargenTop(30);
        $pdf->init();
        $pdf->deletePage(1);
        foreach ($area->getPersonal() as $per){
            $this->addPage($pdf,$per);
        }
        
        return new Response($pdf->Output('Recibos-'.$area->getNombre().'.pdf','D'));
    }    
}
