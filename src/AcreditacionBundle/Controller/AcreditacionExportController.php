<?php

namespace AcreditacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AcreditacionBundle\Entity\PersonalJuegos;
use \jonasarts\Bundle\TCPDFBundle\Services\TCPDF;

/**
 * Default controller.
 *
 * @Route("/acreditacion/export")
 * @Security("has_role('ROLE_ACREDITACION_PRINT')")
 */
class AcreditacionExportController extends Controller {

    private $param = array(
        0 => array(
            'background' => array(
                'x' => 36,
                'y' => 28,
            ),
            'codigo_barra' => array(
                'x' => 18.5,
                'y' => 65.5,
            ),
            'avatar' => array(
                'x' => 71,
                'y' => 38.4,
            ),
            'letra' => array(
                'x' => 45.8,
                'y' => 74.7,
            ),
            'area_sec' => array(
                'x' => 66,
                'y' => 80.5,
            ),
            'accesos' => array(
                'x' => 69.8,
                'y' => 98.8,
            ),
            'datos_pers' => array(
                'x' => 52.5,
                'y' => 109.5,
            ),
        ),
        1 => array(
            'background' => array(
                'x' => 107.2,
                'y' => 28,
            ),
            'codigo_barra' => array(
                'x' => -52.8,
                'y' => 65.5,
            ),
            'avatar' => array(
                'x' => 142.2,
                'y' => 38.4,
            ),
            'letra' => array(
                'x' => 117.2,
                'y' => 74.7,
            ),
            'area_sec' => array(
                'x' => 137,
                'y' => 80.5,
            ),
            'accesos' => array(
                'x' => 140.8,
                'y' => 98.8,
            ),
            'datos_pers' => array(
                'x' => 123.5,
                'y' => 109.5,
            ),
        ),
        2 => array(
            'background' => array(
                'x' => 36,
                'y' => 159.5,
            ),
            'codigo_barra' => array(
                'x' => 18.5,
                'y' => -66,
            ),
            'avatar' => array(
                'x' => 71,
                'y' => 169.9,
            ),
            'letra' => array(
                'x' => 45.8,
                'y' => 206.2,
            ),
            'area_sec' => array(
                'x' => 66,
                'y' => 212,
            ),
            'accesos' => array(
                'x' => 69.8,
                'y' => 230.3,
            ),
            'datos_pers' => array(
                'x' => 52.5,
                'y' => 241.5,
            ),
        ),
        3 => array(
            'background' => array(
                'x' => 107.2,
                'y' => 159.5,
            ),
            'codigo_barra' => array(
                'x' => -52.8,
                'y' => -66,
            ),
            'avatar' => array(
                'x' => 142.2,
                'y' => 169.9,
            ),
            'letra' => array(
                'x' => 117,
                'y' => 206.2,
            ),
            'area_sec' => array(
                'x' => 137,
                'y' => 212,
            ),
            'accesos' => array(
                'x' => 140.8,
                'y' => 230.3,
            ),
            'datos_pers' => array(
                'x' => 123.5,
                'y' => 241.5,
            ),
        )
    );

    /**
     * Convierte el string recibido en Mayuscula
     * 
     * @param string $str String a convertir
     */
    private function mayuscula($str) {
        return strtr(strtoupper($str), "àèìòùáéíóúçñäëïöü", "ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
    }

    /**
     * Lista todos los usuario de un area determinada
     * 
     * @param TCPDF $pdf Pdf actual
     * @param PersonalJuegos $personal Personal de los juegos a procesar
     * @param array $param Parámetros de posiconamiento de la acreditación a procesar
     */
    private function certificadoDraw(TCPDF $pdf, PersonalJuegos $personal, $param) {
// set font
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetLineStyle(array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
// define barcode style
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => 'C',
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );
        // Image    ethod signature:
        $pdf->Image('bundles/acreditacion/images/credencial.png', $param['background']['x'], $param['background']['y'], 71, 106, 'PNG', '', '', true, 150, '', false, false, 1, false, false, false);
        // Start Transformation
        $pdf->StartTransform();
        $pdf->Rotate(180, 70, 110);
        // Stop Transformation
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        // The width is set to the the same as the cell containing the name.  
        // The Y position is also adjusted slightly.
        $pdf->write1DBarcode(strval($personal->getDatosPersonales()->getDocumentoNro()), 'C128B', $param['codigo_barra']['x'], $param['codigo_barra']['y'], 100, 16, 0.45, $style, 'M');
        //Reset X,Y so wrapping cell wraps around the barcode's cell.
        $pdf->StopTransform();
        $pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 2, 'color' => array(0, 0, 0)));
        $pdf->MultiCell(71, 131.5, '', 1, 'C', 1, 0, $param['background']['x'], $param['background']['y'], true, 0, false, true, 0);
        $pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        //set avatar
        $pdf->Image('@' . base64_decode($personal->getAvatar()->getArchivo()), $param['avatar']['x'], $param['avatar']['y'], 32, 31.4, '', '', '', true, 150, '', false, false, 1, false, false, false);
        //Set Letra

        $pdf->SetFont('helvetica', 'B', 45);
        if ($personal->getLetraIdentificacion() == '+') {
            $pdf->SetTextColor(255, 3, 3);
            $pdf->SetFont('helvetica', 'B', 55);
            $pdf->Text($param['letra']['x'], $param['letra']['y'] - 4, $this->mayuscula($personal->getLetraIdentificacion()), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        } elseif ($personal->getLetraIdentificacion() == 'M') {
            $pdf->SetTextColor(255, 3, 3);
            $pdf->Text($param['letra']['x'], $param['letra']['y'], $this->mayuscula($personal->getLetraIdentificacion()), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        } else {
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Text($param['letra']['x'], $param['letra']['y'], $this->mayuscula($personal->getLetraIdentificacion()), $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        }
        $pdf->SetTextColor(255, 255, 255);
        //set Area
        $pdf->SetFont('helvetica', 'B', 11);
        if (strlen($this->mayuscula($personal->getArea()->getNombre())) > 15) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->MultiCell(38, 5, $this->mayuscula($personal->getArea()->getNombre()), $border = 1, $align = 'C', $fill = true, $ln = 1, $x = $param['area_sec']['x'], $y = $param['area_sec']['y'] - 4, $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'M', $fitcell = true);
        } else {
            $pdf->MultiCell(38, 5, $this->mayuscula($personal->getArea()->getNombre()), $border = 1, $align = 'C', $fill = true, $ln = 1, $x = $param['area_sec']['x'], $y = $param['area_sec']['y'], $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'M', $fitcell = true);
        }
        //set Funcion dentro del area
        $pdf->SetFont('helvetica', 'B', 9.5);

        if (strlen($this->mayuscula($personal->getFuncion()->getNombre())) > 15) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->MultiCell(38, 5, $this->mayuscula($personal->getFuncion()->getNombre()), $border = 1, $align = 'C', $fill = true, $ln = 1, $x = $param['area_sec']['x'], $y = $param['area_sec']['y'] + 6, $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'M', $fitcell = true);
        } else {
            $pdf->MultiCell(38, 5, $this->mayuscula($personal->getFuncion()->getNombre()), $border = 1, $align = 'C', $fill = true, $ln = 1, $x = $param['area_sec']['x'], $y = $param['area_sec']['y'] + 5, $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'M', $fitcell = true);
        }

        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(64, 64, 64);
        //Set Accesos 1
        if ($personal->getAccesoSector1()) {
            $pdf->Text($param['accesos']['x'], $param['accesos']['y'], '1', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        }
        //Set Accesos 2
        if ($personal->getAccesoSector2()) {
            $pdf->Text($param['accesos']['x'] + 7, $param['accesos']['y'], '2', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        }
        //Set Accesos 3
        if ($personal->getAccesoSector3()) {
            $pdf->Text($param['accesos']['x'] + 14, $param['accesos']['y'], '3', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        }
        //Set Accesos 4
        if ($personal->getAccesoSector4()) {
            $pdf->Text($param['accesos']['x'] + 21, $param['accesos']['y'], '4', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        }
        //Set Accesos 5
        if ($personal->getAccesoSector5()) {
            $pdf->Text($param['accesos']['x'] + 28, $param['accesos']['y'], '5', $fstroke = false, $fclip = false, $ffill = true, $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M', $rtloff = false);
        }
        $pdf->SetTextColor(255, 255, 255);
        //set Apellido
        
         
        $pdf->SetFont('helvetica', 'B', 10);
        if (strlen($this->mayuscula($personal->getDatosPersonales()->getApellido())) > 15) {
            $pdf->SetFont('helvetica', 'B', 8);
        }
        
          
        $pdf->MultiCell(38, 5, $this->mayuscula($personal->getDatosPersonales()->getApellido()), $border = 1, $align = 'C', $fill = true, $ln = 1, $x = $param['datos_pers']['x'], $y = $param['datos_pers']['y'], $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'M', $fitcell = true
        );
        //set Nombre
        if (strlen($this->mayuscula($this->mayuscula($personal->getDatosPersonales()->getNombre()))) > 15) {
            $pdf->SetFont('helvetica', 'B', 8);
        }
        
        $pdf->MultiCell(38, 5, $this->mayuscula($personal->getDatosPersonales()->getNombre()), $border = 1, $align = 'C', $fill = true, $ln = 1, $x = $param['datos_pers']['x'], $y = $param['datos_pers']['y'] + 5, $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'M', $fitcell = true
        );
        //set DNI tipo
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->MultiCell(38, 5, $this->mayuscula($personal->getDatosPersonales()->getDocumentoTipo()->getNombre()) . ' ' . $personal->getDatosPersonales()->getDocumentoNro(), $border = 1, $align = 'C', $fill = true, $ln = 1, $x = $param['datos_pers']['x'], $y = $param['datos_pers']['y'] + 10, $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'M', $fitcell = true
        );
    }

    /**
     * Crea un pdf con las acreditaciones recibidas como parámetro
     * 
     * @Route("/imprimir/{data}", name="acreditacion_imprimir", defaults={"data": ""},)
     * @Method("GET")
     */
    public function AcreditacionPrintAction($data) {
        $param = json_decode($data);
        $entity = new \AcreditacionBundle\Entity\PersonalJuegos();
        $em = $this->getDoctrine()->getManager();
        $pdf = new TCPDF($orientation = 'P', $unit = 'mm', $format = 'LEGAL', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false);
// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Credenciales');
        $pdf->SetSubject('Credenciales Personal Juegos Bonaerenses');
        $pdf->SetKeywords('Credenciales, Juegos Bonaerenses, Personal');
// set default header data
        $pdf->SetHeaderData('', 12, 'Juegos Bonaerenses 2016', 'Acreditaciones');
// set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        // set JPEG quality
        $pdf->setJPEGQuality(100);
// set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }
        $cantPaginas = ceil(count($param) / 4);
        for ($index = 1; $index <= $cantPaginas; $index++) {
            $pdf->AddPage($orientation = 'P', $format = 'LEGAL', $keepmargins = false, $tocpage = false);
            $pdf->Image('bundles/acreditacion/images/header.png', 15.5, 2.5, 10, 12, 'PNG', 2, 2, true, 150, '', false, false, 0, false, false, false);
            for ($indexj = 1; $indexj <= 4; $indexj++) {
                if ((($index - 1) * 4) + $indexj <= count($param)) {
                    $entity = $em->getRepository('AcreditacionBundle:PersonalJuegos')->find($param[(($index - 1) * 4) + $indexj - 1]);
                    if (!($entity)) {
                        return 'error';
                    }
                    $this->certificadoDraw($pdf, $entity, $this->param[$indexj - 1]);
                }
            }
        }
//Close and output PDF document
        $pdf->Output('Acreditaciones.pdf', 'I');
    }

}
