<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Disciplina;
use ResultadoBundle\Form\DisciplinaType;
/**
 * Default controller.
 *
 * @Route("/gestion/disciplina")
 * @Security("has_role('ROLE_ADMIN')")
 */
class DisciplinaController extends Controller
{
    /**
     * Lists all Disciplina entities.
     *
     * @Route("/", name="disciplina")
     * @Method("GET")
     * @Template("GestionBundle:Disciplina:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('ResultadoBundle:Disciplina')->findAll();
        //$aux=$em->getRepository('ResultadoBundle:Disciplina')->getOnlyRoot();
        //$tree = $em->getRepository('ResultadoBundle:Disciplina')->getArbolAsArray("All");
        
        return array(
            //'entities' => $entities,
            //'tree' => json_encode($tree),
        );
    }
    
    /**
     *
     * @Route("/tree", name="disciplina_list_tree", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     */
    public function treeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tree = $em->getRepository('ResultadoBundle:Disciplina')->getArbolAsArray("All");
        return new JsonResponse($tree);
    }
    
    /**
     * @Route("/list/datatable", name="disciplina_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Disciplina')->datatable($request->request);
        
        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );
        
        foreach ($filter['rows'] as $disciplina){
            $data['data'][] = array(
                "disciplina"  => array(
                                    "id" => $disciplina->getId(),
                                    "nombre" => $disciplina->getNombreCompleto(),
                                    "eventos" => count($disciplina->getEventos()),
                                    "descripcion"  => $disciplina->getDescripcion()
                            ),
                "actions"   => $this->renderView('GestionBundle:Disciplina:actions.html.twig', array('entity' => $disciplina)),
            );
        }
        return new JsonResponse($data);
    }    
    
    /**
     * Creates a new Disciplina entity.
     *
     * @Route("/new", name="disciplina_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Disciplina:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Disciplina();
        $form = $this->createForm(DisciplinaType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setCreatedBy($this->getUser());
            try{
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se agregó la disciplina.'));
            }catch (\Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    } 
    
    /**
     * Finds and displays a Disciplina entity.
     *
     * @Route("/{id}", name="disciplina_show", condition="request.isXmlHttpRequest()")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Disciplina $id)
    {
    }

    /**
     * Displays a form to edit an existing Disciplina entity.
     *
     * @Route("/{id}/edit", name="disciplina_edit", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Disciplina:edit.html.twig")
     */
    public function editAction(Request $request, Disciplina $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(DisciplinaType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setUpdatedBy($this->getUser());
            $entity->setUpdatedAt(new \DateTime());       
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'La disciplina fue modificada.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }    
    
}
