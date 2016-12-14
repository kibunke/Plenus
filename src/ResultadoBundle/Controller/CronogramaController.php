<?php

namespace ResultadoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Evento;
use ResultadoBundle\Entity\Cronograma;
use ResultadoBundle\Entity\CronogramaPartido;
use ResultadoBundle\Form\CronogramaType;
use ResultadoBundle\Form\CronogramaGeneralType;

use CommonBundle\PDFs\DocumentoPDF;

/**
 * Cronograma controller.
 *
 * @Route("/resultados/cronograma")
 * @Security("has_role('ROLE_CRONOGRAMA')")
 */
class CronogramaController extends Controller
{
    /**
     * Finds and displays a table of Plazas.
     *
     * @Route("/{id}/reload", name="cronograma_reload")
     * @Method("GET")
     * @Security("has_role('ROLE_COMPETENCIA_SHOW')")
     */
    public function competenciaCronogramaReloadAction(Request $request, Evento $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Competencia entity.');
        }

        return $this->render(
            'ResultadoBundle:Competencia:fixture.html.twig',
            array('evento' => $entity)
        );
    }
    
    /**
     * Finds and displays a table of Plazas.
     *
     * @Route("/cronogramas", name="cronograma_general_list")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_CRONOGRAMA_LIST')")
     * @Template("ResultadoBundle:Cronograma:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $eventos = $em->getRepository('ResultadoBundle:Evento')->getAllPorUsuarioSinSoloInscribe($this->get('security.context'));
        
        /*
        * controla que el array que viene por post no traiga ids de eventos q no puede ver
        * en tal caso reemplaza el array con el los ids que si puede ver
        */
        if ($request->getMethod() == 'POST') {
            $arr=$request->request->get('eventos');
            $eventosAux = [];
            if ($arr) {
                foreach ($eventos as $evento){
                    if (in_array($evento->getId(), $arr)) {
                        $eventosAux[]=$evento;
                    }
                }
            }
            $cronogramas = $em->getRepository('ResultadoBundle:Cronograma')->getAllByEventos($eventosAux);
            return $this->render(
                'ResultadoBundle:Cronograma:index.table.html.twig', array('cronogramas' => $cronogramas)
            );
        }
        
        $tree = array(
               'text' => "Todos mis eventos",
               'id' => 'root',
               'state' => array('selected' => true,'opened'=>true),
               'children' => $em->getRepository('ResultadoBundle:Evento')->getArbolAsArrayByEventos($eventos),
               'icon' => "fa fa-folder text-red fa-lg"
        );
        $cronogramas = $em->getRepository('ResultadoBundle:Cronograma')->getAllByEventos($eventos);
        return array(
            'cronogramas' => $cronogramas,
            'eventos' => $eventos,
            'tree' => json_encode($tree)
        );
    } 
    
    
    /**
     * Creates a new Partido entity.
     *
     * @Route("/create", name="cronograma_general_create")
     * @Method("POST")
     * @Security("has_role('ROLE_CRONOGRAMA_NEW')")
     * @Template("ResultadoBundle:Cronograma:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Cronograma();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO
            ESTO HAY Q TERMINARLO
        */
        
        //if(!$this->getUser()->hasAccessAtEvento($cronograma->getEvento())){
        //    $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
        //    return new JsonResponse(array('success' => false, 'reload' =>true));        
        //}
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.', 'debug'=> $e->getMessage()));
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Partido entity.
     *
     * @param Plaza $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Cronograma $entity)
    {
        $form = $this->createForm(new CronogramaGeneralType(), $entity, array(
            'action' => $this->generateUrl('cronograma_general_create'),
            'method' => 'POST',
            'security' => $this->get('security.context')
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Partido entity.
     *
     * @Route("/new", name="cronograma_general_new")
     * @Method("GET")
     * @Security("has_role('ROLE_CRONOGRAMA_NEW')")
     * @Template("ResultadoBundle:Cronograma:new.html.twig")
     */
    public function newAction(Request $request)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        $entity = new Cronograma();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Displays a form to create a new Partido entity.
     *
     * @Route("{id}/copia/new", name="cronograma_general_new_copia")
     * @Method("GET")
     * @Security("has_role('ROLE_CRONOGRAMA_NEW')")
     * @Template("ResultadoBundle:Cronograma:edit.general.html.twig")
     */
    public function newCopiaAction(Request $request, Cronograma $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        //$entity = new Cronograma();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }    
    
    /**
     * Displays a form to edit an existing Cronograma entity.
     *
     * @Route("/{id}/edit", name="cronograma_general_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_CRONOGRAMA_EDIT')")
     * @Template("ResultadoBundle:Cronograma:edit.general.html.twig")
     */
    public function editAction(Request $request, Cronograma $cronograma)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        //if(!$this->getUser()->hasAccessAtEvento($cronograma->getEvento())){
        //    $this->addFlash('primary', 'No puede ver información de un evento que no coordina.');
        //    return new JsonResponse(array('success' => false, 'reload' =>true));        
        //}
        
        $em = $this->getDoctrine()->getManager();

        if (!$cronograma) {
            return new JsonResponse(array('success' => false, 'msj' => 'No existe el Cronograma.'));
        }

        $editForm = $this->createEditForm($cronograma);

        return array(
            'entity' => $cronograma,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Partido entity.
    *
    * @param Plaza $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Cronograma $cronograma)
    {
        $form = $this->createForm(new CronogramaGeneralType(), $cronograma, array(
            'action' => $this->generateUrl('cronograma_general_edit', array('id' => $cronograma->getId())),
            'method' => 'POST',
            'security' => $this->get('security.context')
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }
    
    /**
     * Edits an existing Partido entity.
     *
     * @Route("/{id}/edit", name="cronograma_general_update")
     * @Method("POST")
     * @Security("has_role('ROLE_CRONOGRAMA_EDIT')")
     * @Template("ResultadoBundle:Cronograma:edit.general.html.twig")
     */
    public function updateAction(Request $request, Cronograma $cronograma)
    {
        if (!$cronograma) {
            return new JsonResponse(array('success' => false, 'msj' => 'No existe el cronograma.'));
        }
        
        $em = $this->getDoctrine()->getManager();
        $editForm = $this->createEditForm($cronograma);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $cronograma->setUpdatedBy($this->getUser());
            $cronograma->setUpdatedAt(new \DateTime());

            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue guardada correctamente'));
            } catch (\Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser guardada correctamente.'.$e->getMessage()));
            }
        }

        return array(
            'entity' => $cronograma,
            'form'   => $editForm->createView(),
        );
    }
    
    /**
     * Deletes a Cronograma entity.
     *
     * @Route("/{id}/remove", name="cronograma_general_delete_flush")
     * @Security("has_role('ROLE_CRONOGRAMA_DELETE')")
     * @Method("DELETE")
     */
    public function deleteFlushAction(Request $request, Cronograma $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        
        if (!$entity->canDelete()) {
            return new JsonResponse(array('success' => false, 'msj' => 'Los Cronogramas de Partidos no pueden eliminarse.'));
        }
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            try{
                $em->remove($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'msj' => 'La información fue eliminada correctamente'));
            } catch (Exception $e) {
                return new JsonResponse(array('success' => false, 'msj' => 'La información no pudo ser eliminada.'));
            }
        }
        return new JsonResponse(array('success' => false, 'msj' => 'Imposible eliminar el Cronograma. La información no es válida.'));
    }

    /**
     * Creates a form to delete a Plaza entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Cronograma $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cronograma_general_delete_flush', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            //->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;        
    }
    
    /**
     * Deletes a Actividad entity.
     *
     * @Route("/{id}/remove", name="cronograma_general_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_CRONOGRAMA_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request, Cronograma $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }

        /* CHEQUEA QUE EL USUARIO TENGA ACCESO AL EVENTO*/
        //if(!$this->getUser()->hasAccessAtEvento($entity->getEvento())){
        //    return new JsonResponse(array('success' => false, 'msj' => 'No puede ver información de un evento que no coordina.'));        
        //}
        
        $em = $this->getDoctrine()->getManager();

        if (!$entity->canDelete()) {
            return new JsonResponse(array('success' => false, 'msj' => 'Los Cronogramas de Partidos no pueden eliminarse.'));
        }
        
        $form = $this->createDeleteForm($entity);
        
        return array(
                'entity' => $entity,
                'form' => $form->createView()  
            );
    }
    
    /**
     * Print a Cronograma entity.
     *
     * @Route("/print/{eventos}/{cronogramas}/{flag}", name="cronograma_print", defaults={"cronogramas" = null,"eventos" = null, "flag" = null})
     * @Method("GET")
     * @Security("has_role('ROLE_CRONOGRAMA_PRINT')")
     */    
    public function printAction(Request $request, $eventos, $cronogramas, $flag)
    {
        $em = $this->getDoctrine()->getManager();
        $ids_cronogramas = explode(",",$cronogramas);
        $cronogramas = $em->getRepository('ResultadoBundle:Cronograma')->getByIds($ids_cronogramas);
        $eventos = $em->getRepository('ResultadoBundle:Evento')->getByIds(explode(",",$eventos));        
        $pdf = new DocumentoPDF();
        $pdf->init();
        
        if ($flag){
            $pdf->deletePage(1);
            foreach($eventos as $evento){
                $pdf->AddPage('P', 'A4');
                $pdf->writeHTML('<div style="text-align:center"><b>'.$evento."(".count($evento->getCronogramas()).')</b><br></div>', true, false, true, false, '');
                $trs = '<tr>
                            <th style="border-bottom: 1px solid silver;width: 25%" align="center"><b>Fecha</b></th>
                            <th style="border-bottom: 1px solid silver;width: 75%"><b>Descripción</b></th>
                        </tr>';                
                foreach($evento->getCronogramas() as $item){

                    if (in_array($item->getId(),$ids_cronogramas)){
                        $trs.='
                            <tr>
                                <td style="border-bottom: 1px solid silver;" align="center">'.$item->getRaw().'</td>
                                <td style="border-bottom: 1px solid silver;">'.$item->getDescripcion().'</td>
                            </tr>
                        ';                        
                    }
                }
                $pdf->writeHTML('<table cellspacing="0" cellpadding="10">'.$trs.'</table>', true, false, true, false, '');
            }
        }else{
            if (count($eventos)==1)
                $title = (String) $eventos;
            else
                $title = "Este cronograma es aplicado a más de un evento";            
            $pdf->writeHTML('<div style="text-align:center"><b>'.$title.'</b><br></div>', true, false, true, false, '');
            $trs = '<tr>
                        <th style="border-bottom: 1px solid silver;width: 25%" align="center"><b>Fecha</b></th>
                        <th style="border-bottom: 1px solid silver;width: 75%"><b>Descripción</b></th>
                    </tr>';
            foreach($cronogramas as $item){
                $trs.='
                    <tr>
                        <td style="border-bottom: 1px solid silver;" align="center">'.$item->getRaw().'</td>
                        <td style="border-bottom: 1px solid silver;">'.$item->getDescripcion().'</td>
                    </tr>
                ';
            }
            //$pdf->writeHTML();
            $pdf->writeHTML('<table cellspacing="0" cellpadding="10">'.$trs.'</table>', true, false, true, false, '');
        }    
        return new Response($pdf->Output('Cronograma.pdf','D'));
    }
    
    /**
     * Print a Cronograma entity.
     *
     * @Route("/evento/print/{evento}", name="cronograma_evento_print")
     * @Method("GET")
     * @Security("has_role('ROLE_CRONOGRAMA_PRINT')")
     */    
    public function printEventoAction(Request $request, Evento $evento)
    {
        $em = $this->getDoctrine()->getManager();
        $cronogramas = $evento->getCronogramas();
        $pdf = new DocumentoPDF();
        $pdf->init();
        $pdf->writeHTML('<div style="text-align:center"><b>'.$evento->getNombreCompleto().'</b><br></div>');
        if (count($cronogramas)>0){
            $trs = '<table cellspacing="0" cellpadding="10"><tr>
                        <th style="border-bottom: 1px solid silver;width: 25%" align="center"><b>Fecha</b></th>
                        <th style="border-bottom: 1px solid silver;width: 75%"><b>Descripción</b></th>
                    </tr>';
            foreach($cronogramas as $item){
                $trs.='
                    <tr>
                        <td style="border-bottom: 1px solid silver;" align="center">'.$item->getRaw().'</td>
                        <td style="border-bottom: 1px solid silver;">'.$item->getDescripcion().'</td>
                    </tr>
                ';
            }
            $pdf->writeHTML('<table cellspacing="0" cellpadding="10">'.$trs.'</table>', true, false, true, false, '');
        }
        
        return new Response($pdf->Output('Cronograma.pdf','D'));
    }    
}
