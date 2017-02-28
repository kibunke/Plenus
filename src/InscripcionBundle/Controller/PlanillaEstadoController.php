<?php

namespace InscripcionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use InscripcionBundle\Entity\Planilla;
use InscripcionBundle\Entity\Cargada;
use InscripcionBundle\Entity\Enviada;
use InscripcionBundle\Entity\Presentada;
use InscripcionBundle\Entity\Aprobada;
use InscripcionBundle\Entity\Observada;
use InscripcionBundle\Entity\EnRevision;
use InscripcionBundle\Entity\Segmento;


/**
 * PlanillaEstado controller.
 *
 * @Route("/planilla/estado")
 * @Security("has_role('ROLE_INSCRIPCION')")
 */
class PlanillaEstadoController extends Controller
{    
    /**
     * @Route("/{id}/toggle/enviada", name="planilla_toggle_enviada", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function enviarAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        if ($this->canEdit($planilla)){
            try {
                $estado = new Enviada();
                $estado->setCreatedBy($this->getUser());
                $estado->setObservacion($request->request->get('observacion'));
                $planilla->addEstado($estado);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'La planilla ahora esta en estado Enviada!'));
            }
            catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
            }
        }else{
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no cambiar de estado.'));
        }
    }
    
    
    /**
     * @Route("/{id}/toggle/presentada", name="planilla_toggle_presentada", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function presentarAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        if ($this->canEdit($planilla)){
            try {
                $estado = new Presentada();
                $estado->setCreatedBy($this->getUser());
                $estado->setObservacion($request->request->get('observacion'));
                $planilla->addEstado($estado);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'La planilla ahora esta en estado Presentada!'));
            }
            catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
            }
        }else{
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no cambiar de estado.'));
        }
    }
    
    /**
     * @Route("/{id}/toggle/observada", name="planilla_toggle_observada", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function observarAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        if ($this->canEdit($planilla)){
            if (strlen($request->request->get('observacion')) > 5){
                try {
                    $estado = new Observada();
                    $estado->setCreatedBy($this->getUser());
                    $estado->setObservacion($request->request->get('observacion'));
                    $planilla->addEstado($estado);
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'message' => 'La planilla ahora esta en estado Observada!'));
                }
                catch(\Exception $e ){
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
                }
            }else{
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Para pasar una planilla a estado OBSERVADA debe completar la observación obligatoriamente.'));
            }    
        }else{
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no cambiar de estado.'));
        }
    }
    
    /**
     * @Route("/{id}/toggle/aprobada", name="planilla_toggle_aprobada", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function aprobarAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        if ($this->canEdit($planilla)){
            try {
                $estado = new Aprobada();
                $estado->setCreatedBy($this->getUser());
                $estado->setObservacion($request->request->get('observacion'));
                $planilla->addEstado($estado);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'La planilla ahora esta en estado Aprobada!'));
            }
            catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
            }
        }else{
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no cambiar de estado.'));
        }
    }
    
    /**
     * @Route("/{id}/toggle/enRevision", name="planilla_toggle_enrevision", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function revisarAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        if ($this->canEdit($planilla)){
            try {
                $estado = new EnRevision();
                $estado->setCreatedBy($this->getUser());
                $estado->setObservacion($request->request->get('observacion'));
                $planilla->addEstado($estado);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'La planilla ahora esta en estado En revisión!'));
            }
            catch(\Exception $e ){
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos!', 'debug' => $e->getMessage()));
            }
        }else{
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no cambiar de estado.'));
        }
    }
    
    private function canEdit($planilla)
    {
        if (!$this->isGranted('ROLE_COORDINADOR')){
            if ($this->getUser()->getMunicipio() != $planilla->getMunicipio())
                return false;
        }
        
        if ($this->isGranted('ROLE_INSCRIPCION_FUERA_TERMINO')){
            return true;
        }elseif ($planilla->getSegmento()->getIsActive()){
            return true;
        }
        
        return false;
    }
}