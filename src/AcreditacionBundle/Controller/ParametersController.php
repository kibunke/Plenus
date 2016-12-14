<?php

namespace AcreditacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AcreditacionBundle\Form\AcreditacionType;
use AcreditacionBundle\Entity\JuegosParametros;
/**
 * Parameters controller.
 *
 * @Route("/acreditacion/parametros")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ParametersController extends Controller
{
    /**
     * .Edicion de las dimensiones de Acreditación
     *
     * @Route("/", name="acreditacion_parameters")
     * @Template("AcreditacionBundle:Parameters:acreditacion.html.twig")
     */
    public function acreditacionParamAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $areas = $em->getRepository('AcreditacionBundle:Area')->findAll();
        $funciones = $em->getRepository("AcreditacionBundle:FuncionJuegos")->findAll();
        $parametrosJuegos = $em->getRepository("AcreditacionBundle:JuegosParametros")->findAll();
        $form = null;
        $entity = null;
        $now = new \DateTime();
        $user = $this->getUser();
        if (count($parametrosJuegos) == 0) {
            $entity = new JuegosParametros();
            $entity->setCreatedAt($now);
            $entity->setCreatedBy($user);
        } else {
            $entity = $parametrosJuegos[0];
        }
        $form = $this->createForm(new AcreditacionType(), $entity);
        $form->handleRequest($request);
        if ($form->get('submit')->isClicked()) {
            if ($form->isValid()) {
                $entity->setUpdatedAt($now);
                $entity->setUpdatedBy($user);
                $em->persist($entity);
                try {
                    $em->flush();
                    $this->addFlash('exito', 'La información fue guardada correctamente.');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
                }
            } else {
                $this->addFlash('error', 'El formulario contiene campos por completar y/o incorrectos');
            }
        }
        return array(
            'areas' => $areas,
            'funciones' => $funciones,
            'parametros' => $form->createView()
        );
    }

}
