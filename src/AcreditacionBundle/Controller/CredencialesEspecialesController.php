<?php

namespace AcreditacionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AcreditacionBundle\Entity\CredencialEspecial;
use AcreditacionBundle\Form\CredencialEspecialType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use CommonBundle\Entity\Persona;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Description of CredencialesEspecialesController
 *
 * @author kibunke
 */

/**
 * CredencialesEspecialesController controller.
 *
 * @Route("/acreditacion/credenciales_esp")
 * @Security("has_role('ROLE_COORDINADOR') or has_role('ROLE_ADMIN')")
 */
class CredencialesEspecialesController extends Controller {

    /**
     * Valida la imagen subida para que cumpla con las restricciones del sistema retornando true en caso afirmativo y false en caso contrario
     * 
     * @param UploadedFile $file
     * @return boolean
     */
    private function validImage(UploadedFile $file) {
        if (($file->getClientSize() < 500000) && (($file->getClientMimeType() == 'image/jpeg') || ($file->getClientMimeType() == 'image/wbmp') || ($file->getClientMimeType() == 'image/jpg') || ($file->getClientMimeType() == 'image/png'))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Valida y setea la imagen en la entidad PersonalJuegos
     * 
     * @param PersonalJuegos $personal
     * @param UploadedFile $file
     * @return boolean
     */
    private function setImage(CredencialEspecial $credencial, UploadedFile $file) {
        if ($this->validImage($file)) {
            //Se cambia el tamaño de la imagen
            list($ancho, $alto) = getimagesize($file->getPathname());
            $nuevo_ancho = 200;
            $nuevo_alto = 200;
            $thumb = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
            switch ($file->getClientMimeType()) {
                case 'image/jpeg':
                    $origen = imagecreatefromjpeg($file->getPathname());
                    break;
                case 'image/png':
                    $origen = imagecreatefrompng($file->getPathname());
                    break;
                default :
                    $origen = imagecreatefromgif($file->getPathname());
                    break;
            }
            imagecopyresized($thumb, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
            ob_start();
            switch ($file->getClientMimeType()) {
                case 'image/jpeg':
                    imagejpeg($thumb);
                    break;
                case 'image/png':
                    imagepng($thumb);
                    break;
                default :
                    imagegif($thumb);
                    break;
            }
            $stringdata = ob_get_contents(); // read from buffer
            ob_end_clean(); // delete buffer
            $credencial->getAvatar()->setArchivo(base64_encode($stringdata));
            $credencial->getAvatar()->setNombre($file->getClientOriginalName());
            $credencial->getAvatar()->setTipo($file->getClientMimeType());
            return true;
        } else {
            return false;
        }
    }

    /**
     * Pagina inicial del módulo.
     *
     * @Route("/", name="acreditacion_credenciales_especiales")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $credenciales = $em->getRepository('AcreditacionBundle:CredencialEspecial')->getAll();
        } else {
            $credenciales = $em->getRepository('AcreditacionBundle:CredencialEspecial')->getCredenciales($this->getUser()->getId());
        }
        return array(
            'credenciales' => $credenciales,
        );
    }

    /**
     * Pagina inicial para la creación de una nueva Credencial Especial.
     *
     * @Route("/new", name="acreditacion_credenciales_especiales_new")
     * @Template()
     */
    public function newAction(Request $request) {
        $entity = new CredencialEspecial();
        $entity->setAvatar(new \AcreditacionBundle\Entity\Avatar());
        $em = $this->getDoctrine()->getManager();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $hasError = false;
        if (!$form->isValid()) {
            if ($form->isSubmitted()) {
                $hasError = true;
                $this->addFlash('error', 'El formulario contiene campos por completar y/o incorrectos');
            }
        } else {
            //Se procesa avatar
            if ($form->get("avatarCapture")->getData() != '') {//Si se tomo una foto con la webcam
                $imgStr = $form->get("avatarCapture")->getData();
                $entity->getAvatar()->setArchivo($imgStr);
                $entity->getAvatar()->setNombre('captura.png');
                $entity->getAvatar()->setTipo('image/png');
                $form = $this->createCreateForm($entity);
            } elseif ($form->get('avatar')->get('archivoInput')->getData()) { //si se cargo una foto desde un archivo
                $archivo = $form->get('avatar')->get('archivoInput')->getData();
                if (!$this->setImage($entity, $archivo)) {
                    $this->addFlash('error', 'La foto subida no cumple con los requerimientos del sistema. Verifique que el tamaño no supera los 500kb');
                    $hasError = true;
                }
            } else {
                $imgStr = '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';
                $entity->getAvatar()->setArchivo($imgStr);
                $entity->getAvatar()->setNombre('avatar.png');
                $entity->getAvatar()->setTipo('image/png');
                $form = $this->createCreateForm($entity);
            }
            if (!$hasError) {//Persistir
                $now = new \DateTime();
                $user = $this->getUser();
                $entity->setCreatedAt($now);
                $entity->setCreatedBy($user);
                $entity->getAvatar()->setCreatedAt($now);
                $entity->getAvatar()->getCreatedBy($user);
                $em->persist($entity);
                try {
                    $em->flush();
                    $this->addFlash('exito', 'La información fue guardada correctamente.');
                    return $this->redirectToRoute('acreditacion_credenciales_especiales');
                } catch (\Exception $e) {
                    if (strpos($e->getMessage(), 'Duplicate entry ') !== false) {
                        $this->addFlash('error', 'Ocurrio un error y se intentó dar de alta un participante con DNI duplicado');
                    }
                    $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
                }
            }
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'hasError' => false
        );
    }

    /**
     * Displays a form to edit an existing PersonalJuegos entity.
     *
     * @Route("/{id}/edit", name="acreditacion_credenciales_especiales_edit")
     * @Template()
     */
    public function editAction(Request $request, $id) {
        $entity = new CredencialEspecial();
        $em = $this->getDoctrine()->getManager();
        $hasError = false;
        $entity = $em->getRepository('AcreditacionBundle:CredencialEspecial')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PersonalJuegos entity.');
        }
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if (!$em->getRepository('AcreditacionBundle:Area')->esResponsableArea($this->getUser()->getId(), $entity->getArea()->getId())) {
                throw $this->createNotFoundException('No tiene permisos para editar la credencial.');
            }
        }
        $form = $this->createEditForm($entity);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            if ($form->isSubmitted()) {
                $hasError = true;
                $this->addFlash('error', 'El formulario contiene campos por completar y/o incorrectos');
            }
        } else {
            //Se procesa avatar
            if (($form->get("avatarCapture")->getData() != '')) {
                $imgStr = $form->get("avatarCapture")->getData();
                $entity->getAvatar()->setArchivo($imgStr);
                $entity->getAvatar()->setNombre('captura.png');
                $entity->getAvatar()->setTipo('image/png');
            } elseif ($form->get('avatar')->get('archivoInput')->getData()) {
                $archivo = $form->get('avatar')->get('archivoInput')->getData();
                if (!$this->setImage($entity, $archivo)) {
                    $this->addFlash('error', 'La foto subida no cumple con los requerimientos del sistema. Verifique que el tamaño no supera los 500kb');
                    $hasError = true;
                }
            }
        }

        if ($form->get('submit')->isClicked() && (!$hasError)) {
            $now = new \DateTime();
            $user = $this->getUser();
            $entity->setUpdatedAt($now);
            $entity->setUpdatedBy($user);
            $entity->getAvatar()->setUpdatedAt($now);
            $entity->getAvatar()->getUpdatedBy($user);
            $em->persist($entity);
            try {
                $em->flush();
                $this->addFlash('exito', 'La información fue guardada correctamente.');
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry ') !== false) {
                    $this->addFlash('error', 'Ocurrio un error y se intentó dar de alta un participante con DNI duplicado');
                }
                $this->addFlash('error', 'La información no pudo ser guardada correctamente.');
            }
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'hasError' => $hasError
        );
    }

    /**
     * Displays a form to edit an existing Credenciales Especiales entity.
     *
     * @Route("/{id}/show", name="acreditacion_credenciales_especiales_show")
     * @Template()
     */
    public function showAction(Request $request, $id) {
        $entity = new CredencialEspecial();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AcreditacionBundle:CredencialEspecial')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PersonalJuegos entity.');
        }
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if (!$em->getRepository('AcreditacionBundle:Area')->esResponsableArea($this->getUser()->getId(), $entity->getArea()->getId())) {
                throw $this->createNotFoundException('No tiene permisos para editar la credencial.');
            }
        }
        $form = $this->createForm(new CredencialEspecialType($this->getUser()), $entity, array(
            'action' => $this->generateUrl('acreditacion_credenciales_especiales_show', array('id' => $entity->getId())),
            'method' => 'POST',
            'security' => $this->get('security.authorization_checker')
        ));
        $form->add('submit', 'submit', array(
            'label' => 'Guardar',
        ));
        $form->handleRequest($request);

        return array(
            'entity' => $entity,
            'form' => $form->createView()
        );
    }

    /**
     * Deletes a Credenciales Especiales entity.
     *
     * @Route("/{id}/delete", name="acreditacion_credenciales_especiales_delete")
     * @Method("POST")
     */
    public function deletePersonalAction(Request $request, $id) {
        $entity = new CredencialEspecial();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AcreditacionBundle:CredencialEspecial')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PersonalJuegos entity.');
        }
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            if (!$em->getRepository('AcreditacionBundle:Area')->esResponsableArea($this->getUser()->getId(), $entity->getArea()->getId())) {
                throw $this->createNotFoundException('No tiene permisos para editar la credencial.');
            }
        }
        try {
            $em->remove($entity);
            $em->flush();
            $this->addFlash('exito', 'La Credencial Especial fue eliminada correctamente.');
            return new JsonResponse(array('error' => false));
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return new JsonResponse(array('error' => true));
        }
    }

    /**
     * Creates a form to create a Credencial Especial entity.
     *
     * @param CredencialEspecial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(CredencialEspecial $entity) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new CredencialEspecialType($this->getUser()), $entity, array(
            'action' => $this->generateUrl('acreditacion_credenciales_especiales_new'),
            'method' => 'POST',
            'security' => $this->get('security.authorization_checker')
        ));
        $form->add('submit', 'submit', array(
            'label' => 'Guardar',
        ));
        return $form;
    }

    /**
     * Creates a form to edit a Credencial Especial entity.
     *
     * @param CredencialEspecial $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(CredencialEspecial $entity) {
        $form = $this->createForm(new CredencialEspecialType($this->getUser()), $entity, array(
            'action' => $this->generateUrl('acreditacion_credenciales_especiales_edit', array('id' => $entity->getId())),
            'method' => 'POST',
            'security' => $this->get('security.authorization_checker')
        ));
        $form->add('submit', 'submit', array(
            'label' => 'Actualizar'
        ));

        return $form;
    }

}
