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
        $result = $this->canEdit($planilla);
        if ($result === true){
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
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no pudo cambiar de estado.'. $result));
        }
    }


    /**
     * @Route("/{id}/toggle/presentada", name="planilla_toggle_presentada", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function presentarAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $this->canEdit($planilla);
        if ($result === true){
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
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no pudo cambiar de estado.'. $result));
        }
    }

    /**
     * @Route("/{id}/toggle/observada", name="planilla_toggle_observada", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function observarAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $this->canEdit($planilla,true);
        if ($result === true){
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
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no pudo cambiar de estado.'. $result));
        }
    }

    /**
     * @Route("/{id}/toggle/aprobada", name="planilla_toggle_aprobada", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function aprobarAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $this->canEdit($planilla);
        if ($result === true){
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
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no pudo cambiar de estado.'. $result));
        }
    }

    /**
     * @Route("/{id}/toggle/enRevision", name="planilla_toggle_enrevision", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function revisarAction(Request $request,Planilla $planilla)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $this->canEdit($planilla,true);
        if ($result === true){
            if (strlen($request->request->get('observacion')) > 5){
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
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Para pasar una planilla a estado En Revisión debe completar la observación obligatoriamente.'));
            }
        }else{
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'La planilla no pudo cambiar de estado.'. $result));
        }
    }

    /*
     * $back en true si el cambio de estado es un rollback
    */
    private function canEdit($planilla, $back = false)
    {
        if ($this->isGranted('ROLE_ADMIN')){
            return true;
        }
        if ($planilla->isAprobada() && !$this->isGranted('ROLE_ADMIN')){
            return "Usted no tiene los permisos necesarios para DESAPROBAR una planilla.";
        }
        if ($planilla->isAprobada() && !$this->isGranted('ROLE_ADMIN')){
            return "Usted no tiene los permisos necesarios para DESAPROBAR una planilla.";
        }
        if ($planilla->isEditable($this->getUser())){
            if (!$planilla->isCompleted()){
                return "La planilla no tiene el mínimo de participantes requerido.";
            }
        }
        if (!$this->isGranted('ROLE_COORDINADOR')){
            if ($this->getUser()->getMunicipio()->getId() != $planilla->getMunicipio()->getId())
                return "Esta planilla no pertenece a su municipio.";
        }

        if ($this->isGranted('ROLE_INSCRIPCION_FUERA_TERMINO_ESTADO') || $back){
            return true;
        }elseif ($planilla->getSegmento()->getIsActive()){
            return true;
        }

        return "La inscripción al segmento está cerrada!";
    }
}
