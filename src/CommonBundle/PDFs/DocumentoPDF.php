<?php
/**
* TCPDF Bridge 
*
*/
namespace CommonBundle\PDFs;
use jonasarts\Bundle\TCPDFBundle\Services\TCPDF;

class DocumentoPDF extends \jonasarts\Bundle\TCPDFBundle\Services\TCPDF
{
    private $texto_pie;
    private $texto_header;
    
    // MARGENES A4
    protected $_MARGIN_BOTTOM     = 5;
    protected $_MARGIN_LEFT       = 20;
    protected $_MARGIN_TOP        = 5;
    protected $_MARGIN_RIGHT      = 10;
    //protected $_MARGIN_RIGHT_TEXT = 27;
    
    public function Header(){}
     
    public function Footer(){}
    
    public function setTextoPie($texto)
    {
       $this->texto_pie = $texto;
    }
    
    public function getTextoPie()
    {
       return $this->texto_pie;
    }

    public function setTextoHeader($texto)
    {
       $this->texto_header = $texto;
    }
    
    public function getTextoHeader()
    {
       return $this->texto_header;
    }    
    
    public function init()
    {
        $this->AddPage();
        //$this->SetY($this->_MARGIN_TOP);
        
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //$pdf->SetFont('Helvetica', '', 11, '', 'false');
        // set document information       
        $this->SetMargins($this->_MARGIN_LEFT, $this->_MARGIN_TOP, $this->_MARGIN_RIGHT);
        //$this->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$this->SetFooterMargin(PDF_MARGIN_FOOTER);
        $this->SetAutoPageBreak(TRUE, $this->_MARGIN_BOTTOM);
        
        // set margins        
        //$this->SetMargins($this->_MARGIN_LEFT, $this->_MARGIN_TOP, $this->_MARGIN_RIGHT_TEXT);
        //$this->SetHeaderMargin($this->_MARGIN_TOP);
        //$this->SetFooterMargin(35);
        
        // set auto page breaks
        //$this->SetAutoPageBreak(TRUE, $this->_MARGIN_LEFT);
    }
     
    public function setMargenLeft($margenLeft)
    {
       $this->_MARGIN_LEFT = $margenLeft;
    }
    
    public function setMargenRight($margenRight)
    {
       $this->_MARGIN_RIGHT = $margenRight;
    }    
    
    public function setMargenTop($margenTop)
    {
       $this->_MARGIN_TOP = $margenTop;
    }
    
    private function getTextoFinalPie()
    {
       return 'Departamento de Informática - Subsecretaría de Deportes de la Provincia de Buenos Aires - CP 1900 - La Plata - Pcia. Buenos Aires - Argentina';
    }
}