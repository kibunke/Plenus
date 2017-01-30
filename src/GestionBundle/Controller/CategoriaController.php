<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Categoria;
use GestionBundle\Form\CategoriaType;
/**
 * Categoria controller.
 *
 * @Route("/gestion/categoria")
 * @Security("has_role('ROLE_ADMIN')")
 */
class CategoriaController extends Controller
{
    /**
     * Lists all Categoria entities.
     *
     * @Route("/", name="categoria")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('dimension');
    }
    
    /**
     * @Route("/list/datatable", name="categoria_list_datatable")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Categoria')->datatable($request->request);
        
        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $categoria){
            $data['data'][] = array(
                "categoria"  => array(
                                    "id" => $categoria->getId(),
                                    "nombre" => $categoria->getNombre(),
                                    "descripcion"  => $categoria->getDescripcion(),
                                    "eventos"   => count($categoria->getEventos())
                            ),
                "actions"   => $this->renderView('GestionBundle:Categoria:actions.html.twig', array('entity' => $categoria)),
            );
        }
        return new JsonResponse($data);
    }    
    
    /**
     * Creates a new Categoria entity.
     *
     * @Route("/new", name="categoria_new")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Categoria:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Categoria();
        $form = $this->createForm(CategoriaType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setCreatedBy($this->getUser());
            try{
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se agregó la categoria.'));
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
     * Finds and displays a Categoria entity.
     *
     * @Route("/{id}", name="categoria_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Categoria $id)
    {
    }

    /**
     * Displays a form to edit an existing Categoria entity.
     *
     * @Route("/{id}/edit", name="categoria_edit")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Categoria:edit.html.twig")
     */
    public function editAction(Request $request, Categoria $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(CategoriaType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());       
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'La categoría fue modificada.'));
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
