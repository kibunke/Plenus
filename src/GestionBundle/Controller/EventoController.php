<?php

namespace GestionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ResultadoBundle\Entity\Evento;
use GestionBundle\Form\EventoType;
use InscripcionBundle\Entity\Segmento;
/**
 * Evento controller.
 *
 * @Route("/gestion/evento")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EventoController extends Controller
{
    /**
     * Lists all Evento entities.
     *
     * @Route("/", name="gestion_evento")
     * @Method("GET")
     * @Template("GestionBundle:Evento:index.html.twig")
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/list/datatable", name="gestion_evento_list_datatable", condition="request.isXmlHttpRequest()")
     * @Method("POST")
     */
    public function listDataTableAction(Request $request)
    {
        $em     = $this->getDoctrine()->getManager();
        $filter = $em->getRepository('ResultadoBundle:Evento')->datatable($request->request,$this->getUser());

        $data = array(
                    "draw"            => $request->request->get('draw'),
                    "recordsTotal"    => $filter['total'],
                    "recordsFiltered" => $filter['filtered'],
                    "data"            => array()
        );

        foreach ($filter['rows'] as $evento){
            $data['data'][] = array(
                "evento"  => array(
                                    "id" => $evento->getId(),
                                    "nombre" => $evento->getNombreCompletoRaw(),
                                    "orden" => $evento->getOrden(),
                                    "descripcion"  => $evento->getDescripcion()
                            ),
                "actions"   => $this->renderView('GestionBundle:Evento:actions.html.twig', array('entity' => $evento)),
            );
        }
        return new JsonResponse($data);
    }

    /**
     * Creates a new Evento entity.
     *
     * @Route("/new", name="gestion_evento_new", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Evento:new.html.twig")
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Evento();
        $form = $this->createForm(EventoType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setCreatedBy($this->getUser());
            try{
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se agregó el evento.'));
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

    /**
     * Creates a new Evento entity.
     *
     * @Route("/new/from/segmento/{id}", name="gestion_evento_new_from_segmento", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Evento:new.html.twig")
     */
    public function newFromSegmentoAction(Request $request, Segmento $segmento)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Evento();
        $form = $this->createForm(EventoType::class, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setCreatedBy($this->getUser());
            try{
                $em->persist($entity);
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'Se agregó el evento.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }elseif(!$form->isSubmitted()){
            $entity->setDimensionesFromSegmento($segmento);
            $form = $this->createForm(EventoType::class, $entity);
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    /**
     * Show Evento $evento
     *
     * @Route("/{evento}/show", name="gention_evento_show")
     * @Template()
     * @Method("GET")
     */
    public function showAction(Request $request,Evento $evento)
    {
        return array('entity' => $evento);
    }

    /**
     * Displays a form to edit an existing Evento entity.
     *
     * @Route("/{evento}/edit", name="gestion_evento_edit", condition="request.isXmlHttpRequest()")
     * @Method({"GET", "POST"})
     * @Template("GestionBundle:Evento:edit.html.twig")
     */
    public function editAction(Request $request, Evento $evento)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(EventoType::class, $evento);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $evento->setUpdatedBy($this->getUser());
            $evento->setUpdatedAt(new \DateTime());
            try{
                $em->flush();
                return new JsonResponse(array('success' => true, 'message' => 'El evento fue modificado.'));
            } catch (Exception $e) {
                $error = $e->getMessage();
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar guardar los datos.', 'debug' => $error));
            }
        }
        return array(
            'entity' => $evento,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/{evento}/delete", name="gestion_evento_delete", condition="request.isXmlHttpRequest()")
     * @Method({"POST"})
     */
    public function deleteAction(Request $request,Evento $entity)
    {
        $em = $this->getDoctrine()->getManager();
        if ($entity){
            if(!count($entity->getEtapas()) && !count($entity->getEquipos())){
                try {
                    $em->remove($entity);
                    $em->flush();
                    return new JsonResponse(array('success' => true, 'message' => 'Se eliminó el Evento'));
                }
                catch(\Exception $e ){
                    return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'Ocurrio un error al intentar eliminar el evento!', 'debug' => $e->getMessage()));
                }
            }else{
                return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El evento no debe tener etapas ni equipos asociados'));
            }
        }
        return new JsonResponse(array('success' => false, 'error' => true, 'message' => 'El evento no exite'));
    }

    /**
     * Listado de etpas de un Evento $evento
     *
     * @Route("/{evento}/evento/etapas", name="gestion_evento_etapas")
     * @Method("GET")
     */
    public function etapasAction(Request $request,Evento $evento)
    {
        return new JsonResponse($evento->getEtapasAsArray());
    }

    /**
     * Listado de etpas de un Evento $evento
     *
     * @Route("/evento/{evento}/ordenar/etapas", name="gestion_evento_ordenar_etapas")
     * @Method("POST")
     */
    public function ordenarEtapasAction(Request $request,Evento $evento)
    {
        $etapas = json_decode($request->get('etapas',array()));

        if(!$etapas)
        {
            return new JsonResponse(['success' => false , 'message' => 'No se pudieron obtener las etapas']);
        }

        if(sizof($etapas) != $evento->getEtapas()->count())
        {
            return new JsonResponse(['success' => false , 'message' => 'No conicide la cantidad de etapas enviadas con la cantidad que posee el evento ' . $evento->getId()]);
        }

        $em = $this->getDoctrine()->getManager();
        $i  = 0;

        foreach($etapas as $etapa)
        {
            $etapaObj = $em->getRepository('ResultadoBundle:Etapa')->find($etapa);

            if($etapaObj)
            {
                return new JsonResponse(['success' => false , 'message' => 'No se pudo encontrar la etapa ' . $etapa]);
            }

            if($etapaObj->getEvento()->getId() != $evento->getId())
            {
                return new JsonResponse(['success' => false , 'message' => 'La etapa ' . $etapa . ' no pertenece al evento ' . $evento->getId()]);
            }

            $etapaObj->setOrden($i++);
        }

        $em->flush();

        return new JsonResponse(['success' => true , 'message' => 'Las etapas fueron guardadas con éxito']);
    }

    /**
     * Agregar nueva etapa al final de un Evento $evento
     *
     * @Route("/evento/{evento}/agregar/etapa", name="gestion_evento_agregar_etapa")
     * @Method("POST")
     */
    public function agregarEtapaAction(Request $request,Evento $evento)
    {
        $etapa = $request->get('etapa','');

        try{
            $etapaObj = new $etapa;
        }catch(\Exception $e )
        {
            return new JsonResponse(['success' => false , 'message' => 'Error al crear la nueva etapa']);
        }

        $evento->addEtapaAtTheEnd($etapaObj);

        $em->persist($etapaObj);
        $em->flush();

        return new JsonResponse(['success' => true, 'message' => 'La etapa fue guardada con éxito' , 'etapas' => $evento->getEtapasAsArray()]);
    }
}
