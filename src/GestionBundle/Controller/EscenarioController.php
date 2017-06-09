<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Escenario;
use ResultadoBundle\Form\EscenarioType;

/**
 * Escenario controller.
 *
 * @Route("/escenario")
 * @Security("has_role('ROLE_ESCENARIO')")
 */
class EscenarioController extends Controller
{
    /**
     * Lists all Escenario entities.
     *
     * @Route("/escenario", name="escenario")
     * @Method("GET")
     * @Template("GestionBundle:Escenario:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        return array();
    }

    /**
     * @Route("/list/datatable", name="escenario_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     * @Security("has_role('ROLE_ESCENARIO_LIST')")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Escenario')->datatable($request->request);

        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );

        foreach ($filter['rows'] as $escenario){
            $data['data'][] = array(
                                "icon" => '<i class="fa fa-building-o"></i>',
                                "id" => $escenario->getId(),
                                "nombre" => $escenario->getNombre(),
                                "direccion" => $escenario->getDireccionRaw(),
                                "georreferencia"  => $escenario->getLatlng() ? '<span class="fa fa-lg text-red fa-map-marker" title="Escenario Georeferenciado"></span>' : '',
                                "actions"   => $this->renderView('GestionBundle:Escenario:actions.html.twig', array('entity' => $escenario)),
                    );
        }
        return new JsonResponse($data);
    }

    /**
     * Displays a form to create a new Escenario entity.
     *
     * @Route("/new", name="escenario_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ESCENARIO_NEW')")
     * @Template("GestionBundle:Escenario:form.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $escenario = new Escenario($this->getUser());
        $form = $this->createForm(EscenarioType::class, $escenario);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $em->persist($escenario);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se agregó el escenario.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }
        return array(
            'entity' => $escenario,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Escenario entity.
     *
     * @Route("/{id}/show", name="escenario_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Escenario $id)
    {
        //$em = $this->getDoctrine()->getManager();
        //
        //$entity = $em->getRepository('SeguridadBundle:Usuario')->find($id);
        //
        //if (!$entity) {
        //    throw $this->createNotFoundException('Unable to find Usuario entity.');
        //}
        //
        //$deleteForm = $this->createDeleteForm($id);
        //
        //return array(
        //    'entity'      => $entity,
        //    'delete_form' => $deleteForm->createView(),
        //);
    }

    /**
     * Displays a form to edit an existing Evento entity.
     *
     * @Route("/{escenario}/edit", name="escenario_edit", condition="request.isXmlHttpRequest()")
     * @Method({"GET","POST"})
     * @Security("has_role('ROLE_ESCENARIO_EDIT')")
     * @Template("GestionBundle:Escenario:form.html.twig")
     */
    public function editAction(Request $request,Escenario $escenario)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$escenario) {
            return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'No existe la escenario que quiere modificar.'));
        }

        $form = $this->createForm(EscenarioType::class, $escenario);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $escenario->setUpdatedBy($this->getUser());
            $escenario->setUpdatedAt(new \DateTime());
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'El escenario fue modificado.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }
        return array(
            'entity' => $escenario,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{escenario}/delete", name="escenario_delete", condition="request.isXmlHttpRequest()", defaults={"escenario":"__00__"})
     * @Method({"POST"})
     * @Security("has_role('ROLE_ESCENARIO_DELETE')")
     */
    public function deleteAction(Request $request,Escenario $escenario)
    {
        $em = $this->getDoctrine()->getManager();
        if ($escenario){
            if(!count($escenario->getCronogramas())){
                try {
                    $em->remove($escenario);
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'message' => 'Se eliminó el Escenario'));
                }
                catch(\Exception $e ){
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar eliminar el escenario!', 'debug' => $e->getMessage()));
                }
            }else{
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El escenario no debe tener cronogramas asociados'));
            }
        }
        return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El escenario no exite'));
    }
}
