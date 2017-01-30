<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Modalidad;
use GestionBundle\Form\ModalidadType;
/**
 * Modalidad controller.
 *
 * @Route("/gestion/modalidad")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ModalidadController extends Controller
{
    /**
     * Lists all Modalidad entities.
     *
     * @Route("/modalidad", name="modalidad")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('dimension');
    }
    
        /**
     * @Route("/list/datatable", name="modalidad_list_datatable")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Modalidad')->datatable($request->request);
        
        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $modalidad){
            $data['data'][] = array(
                "modalidad"  => array(
                                    "id" => $modalidad->getId(),
                                    "nombre" => $modalidad->getNombre(),
                                    "descripcion"  => $modalidad->getDescripcion(),
                                    "eventos"   => count($modalidad->getEventos())
                            ),
                "actions"   => $this->renderView('GestionBundle:Modalidad:actions.html.twig', array('entity' => $modalidad)),
            );
        }
        return new JsonResponse($data);
    }    
    
    /**
     * Creates a new Modalidad entity.
     *
     * @Route("/new", name="modalidad_new")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Modalidad:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Modalidad();
        $form = $this->createForm(ModalidadType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setCreatedBy($this->getUser());
            try{
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se agregó la modalidad.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    } 
    
    /**
     * Finds and displays a Modalidad entity.
     *
     * @Route("/{id}", name="modalidad_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Modalidad $id)
    {
    }

    /**
     * Displays a form to edit an existing Modalidad entity.
     *
     * @Route("/{id}/edit", name="modalidad_edit")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Modalidad:edit.html.twig")
     */
    public function editAction(Request $request, Modalidad $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ModalidadType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());       
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'La modalidad fue modificada.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
}
