<?php

namespace SeguridadBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use SeguridadBundle\Entity\Usuario;
use SeguridadBundle\Form\UsuarioType;

/**
 * Usuario controller.
 *
 * @Route("/usuario")
 * @Security("has_role('ROLE_USER')")
 */
class UsuarioController extends Controller
{

    /**
     * Lists all Usuario entities.
     *
     * @Route("/", name="usuario")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('SeguridadBundle:Usuario')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Usuario entity.
     *
     * @Route("/", name="usuario_create")
     * @Method("POST")
     * @Template("SeguridadBundle:Usuario:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Usuario();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedBy($this->getUser());
            /* se encripta la pass*/
            $factory = $this->container->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $entity->setPasswordGenerada($entity->getPassword());
            $pwd = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
            $entity->setPassword($pwd);
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('usuario_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Usuario entity.
     *
     * @param Usuario $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Usuario $entity)
    {
        $form = $this->createForm(new UsuarioType(), $entity, array(
            'action' => $this->generateUrl('usuario_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Usuario entity.
     *
     * @Route("/new", name="usuario_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Usuario();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Usuario entity.
     *
     * @Route("/{id}", name="usuario_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SeguridadBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Usuario entity.
     *
     * @Route("/{id}/edit", name="usuario_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SeguridadBundle:Usuario')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Usuario entity.
    *
    * @param Usuario $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Usuario $entity)
    {
        $form = $this->createForm(new UsuarioType(), $entity, array(
            'action' => $this->generateUrl('usuario_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Usuario entity.
     *
     * @Route("/{id}", name="usuario_update")
     * @Method("PUT")
     * @Template("SeguridadBundle:Usuario:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SeguridadBundle:Usuario')->find($id);
        $oldEntity = clone $entity;

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Usuario entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            //$this->setAuditoriaModify($entity);
            $entity->setUpdatedAt(new \DateTime());
            $entity->setUpdatedBy($this->getUser());
            if($entity->getPassword()!= NULL)
            {
                $factory = $this->container->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $pwd = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($pwd);
             }else{
                $entity->setPassword($oldEntity->getPassword());
            }            
            $em->flush();

            return $this->redirect($this->generateUrl('usuario_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Usuario entity.
     *
     * @Route("/{id}", name="usuario_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SeguridadBundle:Usuario')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Usuario entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('usuario'));
    }

    /**
     * Creates a form to delete a Usuario entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usuario_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    /**
     * Primer ingreso
     *
     * @Route("/{id}/firtLogin", name="usuario_firstLogin")
     * @Method({"GET","POST"})
     * @Template("SeguridadBundle:Usuario:firstLogin.html.twig")
     */    
    public function firstLoginAction(Request $request,Usuario $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $flash = $this->get('session')->getFlashBag();
        $usuario = $this->getUser();
        if ($entity->getId()!=$usuario->getId()) {
            throw $this->createNotFoundException('No puede modificar información de otra persona!.');
        }        
        $form = $this->createFormBuilder($entity)
                ->add('nombre','text',array('required'=>true))
                ->add('apellido','text',array('required'=>true))
                ->add('email','email',array('required'=>true))
                ->add('telefono','text',array('required'=>true))
                ->add('password', 'repeated', array(
                                                'type' => 'password',
                                                'invalid_message' => 'Las contraseñas no coinciden.',
                                                'options' => array('attr' => array('class' => 'password-field')),
                                                'required' => true,
                                                'first_options'  => array('label' => 'Password'),
                                                'second_options' => array('label' => 'Confirme la Password'),
                                          )
                  )
                ->setAction($this->generateUrl('usuario_firstLogin', array('id' => $entity->getId())))
                ->setMethod('POST')
                ->add('save', 'submit', array('label' => 'Guardar'))
                ->getForm();
                
        if($request->getMethod() == 'POST')
        {
            $form->handleRequest($request);
    
            if ($form->isValid()){
                $factory = $this->container->get('security.encoder_factory');
                $encoder = $factory->getEncoder($usuario);
            
                $nueva = $encoder->encodePassword($request->get('form')['password']['first'], $usuario->getSalt());

                if($usuario->getPassword() == $nueva)
                {
                    $flash->add('primary','La contraseña nueva debe ser distinta a la actual.');
                    return $this->redirectToRoute('usuario_firstLogin', array('id' => $usuario->getId()));
                }
                
                
                if($request->get('form')['password']['first'] != $request->get('form')['password']['second'])
                {
                    $flash->add('error','La contraseña nueva no coincide con su verificación');
                    return $this->redirectToRoute('usuario_firstLogin', array('id' => $usuario->getId()));
                }
                
                $re = '/
                       # Match password with 6-15 chars with letters and digits
                          ^                 # Anchor to start of string.
                          (?=.*?[A-Z])      # Assert there is at least one mayus letter, AND
                          (?=.*?[a-z])      # Assert there is at least one minus letter, AND
                          (?=.*?[0-9])      # Assert there is at least one digit, AND
                          (?=.{6,15}\z)     # Assert the length is from 6 to 15 chars.
                          /x';
                if(!preg_match($re, $request->get('form')['password']['first']))
                {
                    $flash->add('error','La contraseña debe poseer entre 6 y 15 caracteres, al menos una mayúscla, al menos un número y sólo admite caracteres alfanuméricos');
                    return $this->redirectToRoute('usuario_firstLogin', array('id' => $usuario->getId()));
                }
                
                
                $usuario->setPassword($nueva);
                $usuario->setPasswordGenerada('');
                $em->flush();
                $flash->add('exito','Cambio de contraseña realizado con éxito');
                //return $this->render('CommonBundle::layout.html.twig');
                return $this->redirectToRoute('homepage');
            }
        }
        
        return array('entity' => $entity, 'form' => $form->createView());
    }
    
    /**
     * Reseteo de contraseña
     *
     * @Route("/{id}/resetPassword", name="usuario_resetPassword")
     * @Method({"GET","POST"})
     * @Template("SeguridadBundle:Usuario:firstLogin.html.twig")
     */    
    public function resetPasswordAction(Request $request,Usuario $entity)
    {
        //$em = $this->getDoctrine()->getManager();
        //$flash = $this->get('session')->getFlashBag();
        //$usuario = $this->getUser();
        //if ($entity->getId()!=$usuario->getId()) {
        //    throw $this->createNotFoundException('No puede modificar información de otra persona!.');
        //}        
        //$form = $this->createFormBuilder($entity)
        //        ->add('nombre','text',array('required'=>true))
        //        ->add('apellido','text',array('required'=>true))
        //        ->add('email','email',array('required'=>true))
        //        ->add('telefono','text',array('required'=>true))
        //        ->add('password', 'repeated', array(
        //                                        'type' => 'password',
        //                                        'invalid_message' => 'Las contraseñas no coinciden.',
        //                                        'options' => array('attr' => array('class' => 'password-field')),
        //                                        'required' => true,
        //                                        'first_options'  => array('label' => 'Password'),
        //                                        'second_options' => array('label' => 'Confirme la Password'),
        //                                  )
        //          )
        //        ->setAction($this->generateUrl('usuario_firstLogin', array('id' => $entity->getId())))
        //        ->setMethod('POST')
        //        ->add('save', 'submit', array('label' => 'Guardar'))
        //        ->getForm();
        //        
        //if($request->getMethod() == 'POST')
        //{
        //    if($request->get('form')['password']['first'])
        //    $form->handleRequest($request);
        //
        //    if ($form->isValid()){
        //        $factory = $this->container->get('security.encoder_factory');
        //        $encoder = $factory->getEncoder($usuario);
        //    
        //        $nueva = $encoder->encodePassword($request->get('form')['password']['first'], $usuario->getSalt());
        //
        //        if($usuario->getPassword() == $nueva)
        //        {
        //            $flash->add('primary','La contraseña nueva debe ser distinta a la actual.');
        //            return $this->redirectToRoute('usuario_firstLogin', array('id' => $usuario->getId()));
        //        }
        //        
        //        
        //        if($request->get('form')['password']['first'] != $request->get('form')['password']['second'])
        //        {
        //            $flash->add('error','La contraseña nueva no coincide con su verificación');
        //            return $this->redirectToRoute('usuario_firstLogin', array('id' => $usuario->getId()));
        //        }
        //        
        //        $re = '/
        //               # Match password with 6-15 chars with letters and digits
        //                  ^                # Anchor to start of string.
        //                  (?=.*?[A-Za-z])  # Assert there is at least one letter, AND
        //                  (?=.*?[0-9])     # Assert there is at least one digit, AND
        //                  (?=.{6,15}\z)    # Assert the length is from 6 to 15 chars.
        //                  /x';
        //        if(!preg_match($re, $request->get('nueva')))
        //        {
        //            $flash->add('error','La contraseña debe poseer entre 6 y 15 caracteres, al menos una mayúsucla, al menos un número y sólo admite caracteres alfanuméricos');
        //            return $this->redirectToRoute('usuario_firstLogin', array('id' => $usuario->getId()));
        //        }
        //        
        //        
        //        $usuario->setPassword($nueva);
        //        $usuario->setPasswordGenerada('');
        //        $em->flush();
        //        $flash->add('exito','Cambio de contraseña realizado con éxito');
        //        //return $this->render('CommonBundle::layout.html.twig');
        //        return $this->redirectToRoute('homepage');
        //    }
        //}
        //
        //return array('entity' => $entity, 'form' => $form->createView());
    }    
}
