<?php

namespace TesoreriaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use TesoreriaBundle\Form\FondoType;
use TesoreriaBundle\Entity\Fondo;
use CommonBundle\PDFs\DocumentoPDF;

/**
 * Fondo controller.
 *
 * @Route("/tesoreria/fondo")
 * @Security("has_role('ROLE_TESORERIA_FONDO')")
 */
class FondoController extends Controller
{
    /**
     * Lists all Fondo entities.
     *
     * @Route("/list", name="tesoreria_fondo_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('TesoreriaBundle:Fondo')->findAll();
        return array(
            'fondos' => $entities,
        );
    }

    /**
     * Consulta saldosIniciales.
     *
     * @Route("/query/saldosIniciales", name="tesoreria_fondo_saldosIniciales")
     * @Method("GET")
     * @Template()
     */
    public function saldosInicialesAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('TesoreriaBundle:Fondo')->findAll();
        return array(
            'fondos' => $entities,
        );
    }
    
    /**
     * Consulta balanceDeSaldos.
     *
     * @Route("/query/balanceDeSaldos", name="tesoreria_fondo_balanceDeSaldos")
     * @Method("GET")
     * @Template()
     */
    public function balanceDeSaldosAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('TesoreriaBundle:Fondo')->findAll();
        return array(
            'fondos' => $entities,
        );
    }
    
    /**
     * Json info all Fondo entities.
     *
     * @Route("/json", name="tesoreria_fondo_json")
     * @Method("GET")
     */
    public function jsonAction() {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('TesoreriaBundle:Fondo')->findAll();
        $response = array(
                            "montoTotal" => array(
                                    "total" => 0,
                                    "reservado" => 0,
                                    "utilizado" => 0,
                                    "disponible" => 0
                                ),
                            "cantidad" => count($entities),
                            "disponibles" => 0,
                            "fondos" => []
                          );
        foreach ($entities as $fondo){
            $response['montoTotal']['total'] += $fondo->getMonto();
            $response['montoTotal']['reservado'] += $fondo->getMontoReservado();
            $response['montoTotal']['utilizado'] += $fondo->getMontoUtilizado();
            $response['montoTotal']['disponible'] += $fondo->getMontoDisponible();
            if ($fondo->getMontoDisponible() > 0) {
                $response['disponibles'] ++;
            }
            $response['fondos'][] = array(
                    "id"                => $fondo->getId(),
                    "nombre"            => $fondo->getNombre(),
                    "monto"             => $fondo->getMonto(),
                    "entidad"           => $fondo->getEntidad(),
                    "montoDisponible"   => $fondo->getMontoDisponible(),
                    "montoReservado"    => $fondo->getMontoReservado(),
                    "montoUtilizado"    => $fondo->getMontoUtilizado(),
                    "movimientos"       => $fondo->getMovimientosJson()
            );
        }
        return new JsonResponse(array('success' => true, 'data' => $response));
    }    
    
    /**
     * Creates a new Fondo entity.
     *
     * @Route("/create", name="tesoreria_fondo_create")
     * @Method("POST")
     * @Security("has_role('ROLE_TESORERIA_FONDO_NEW')")
     * @Template("TesoreriaBundle:Fondo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Fondo();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedBy($this->getUser());            
            try{
                $em->persist($entity);
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                return new JsonResponse(array('success' => true));
            } catch (Exception $e) {
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
                return new JsonResponse(array('success' => false));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Fondo entity.
     *
     * @param Fondo $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Fondo $entity)
    {
        $form = $this->createForm(new FondoType(), $entity, array(
            'action' => $this->generateUrl('tesoreria_fondo_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar'));

        return $form;
    }

    /**
     * Displays a form to create a new Fondo entity.
     *
     * @Route("/new", name="tesoreria_fondo_new")
     * @Method("GET")
     * @Security("has_role('ROLE_TESORERIA_FONDO_NEW')")
     * @Template()
     */    
    public function newAction()
    {
        $entity = new Fondo();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing Fondo entity.
     *
     * @Route("/{id}/edit", name="tesoreria_fondo_edit")
     * @Method("GET")
     * @Security("has_role('ROLE_TESORERIA_FONDO_EDIT')")
     * @Template()
     */
    public function editAction(Fondo $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('No existe el Fondo que quiere modificar.');
        }
        
        $form = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to edit a Fondo entity.
    *
    * @param Fondo $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Fondo $entity)
    {
        $form = $this->createForm(new FondoType(), $entity, array(
            'action' => $this->generateUrl('tesoreria_fondo_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Actualizar'));

        return $form;
    }
    
    /**
     * Edits an existing Fondo entity.
     *
     * @Route("/{id}/update", name="tesoreria_fondo_update")
     * @Method("PUT")
     * @Security("has_role('ROLE_TESORERIA_FONDO_EDIT')")
     * @Template("TesoreriaBundle:Fondo:edit.html.twig")
     */
    public function updateAction(Request $request,Fondo $entity)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Fondo entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        
        if (!$entity->editCheck()){
            return new JsonResponse(array('success' => false, 'error' => true, 'msj' => 'Para poder editar el Fondo, el monto NO puede ser inferior a la suma de monto utilizado más monto reservado.'));
        }
        
        if ($editForm->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());
            try{
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
                return new JsonResponse(array('success' => true));
                //return $this->redirectToRoute('categoria');
            } catch (Exception $e) {
                return new JsonResponse(array('success' => false, 'error' => true, 'msj' => 'La información no pudo ser guardada correctamente.'));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }
    
    /**
     * Deletes a Fondo entity.
     *
     * @Route("/{id}/remove", name="tesoreria_fondo_remove")
     * @Security("has_role('ROLE_TESORERIA_FONDO_DELETE')")
     * @Method("DELETE")
     */
    public function deleteFlushAction(Request $request, Fondo $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Fondo entity.');
            }

            $em->remove($entity);
            try{
                $em->flush();
                $this->addFlash('exito', 'El Fondo fue eliminado con exito ');
            } catch (Exception $e) {
                $this->addFlash('error', 'Ocurrio un error al intentar eliminar el Fondo.');
            }
        }
        return $this->redirectToRoute('tesoreria_fondo_list');
    }

    /**
     * Creates a form to delete a Fondo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Fondo $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tesoreria_fondo_remove', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar'))
            ->getForm()
        ;
    }
    
    /**
     * Deletes a Fondo entity.
     *
     * @Route("/{id}/delete", name="tesoreria_fondo_delete")
     * @Method("GET")
     * @Security("has_role('ROLE_TESORERIA_FONDO_DELETE')")
     * @Template()
     */
    public function deleteAction(Request $request, Fondo $entity)
    {
        if (!$request->isXMLHttpRequest()){
            return $this->redirectToRoute('tesoreria_fondo_list');
        }
        
        $em = $this->getDoctrine()->getManager();
        if (!$entity) {
            throw $this->createNotFoundException('No existe la Entidad.');
        }
        
        if (!$entity->deleteCheck()){
            return new JsonResponse(array('success' => false, 'error' => true, 'msj' => 'Para poder borrar el Fondo no debe tener monto utilizado, ni monto reservado.'));
        }
        $form = $this->createDeleteForm($entity);
        return array(
                'entity' => $entity,
                'form' => $form->createView()  
            );
    }
    
    /**
     * print Recibo de Fondo.
     *
     * @Route("/{id}/print", name="tesoreria_fondo_printRecibo")
     * @Method("GET")
     * @Template()
     */
    public function printAction(Request $request, Fondo $fondo)
    {
        $pdf = new DocumentoPDF();
        $pdf->setTextoHeader('Recibo');
        $pdf->setMargenRight(20);
        $pdf->setMargenTop(30);
        $pdf->init();
        $pdf->deletePage(1);
        $pdf->AddPage('P', 'A4');
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
                <td style="font-size:15px;text-align:right;"><b>Rendición Nº</b> '.str_pad(0, 10, "0", STR_PAD_LEFT).'</td>
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
        $pdf->writeHTML($fondo->getModeloRecibo());
        
        //$pdf->writeHTML();
        
        return new Response($pdf->Output('ModeloRecibo.pdf','D'));
    }    
}
