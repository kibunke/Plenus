<?php

namespace SeguridadBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Gregwar\Captcha\CaptchaBuilder;
use CommonBundle\Entity\MCrypt;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use SeguridadBundle\Entity\Log;
use SeguridadBundle\Entity\Usuario;
use SeguridadBundle\Entity\Perfil;
use SeguridadBundle\Form\UsuarioType;
use CommonBundle\Entity\Persona;
use CommonBundle\Entity\Email;

/**
 * @Route("/")
 */
class SecurityController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Template()
     */
    public function indexAction()
    {
        return $this->redirectToRoute('homepage', array(), 301);
    }
    
    /**
     * @Route("/system/users/logout", name="logout_all_users")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function logoutAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $sesiones = $em->getRepository('SeguridadBundle:Sessions')->findAll();
       
        foreach($sesiones as $sesion)
        {
            $datos = stream_get_contents($sesion->getSessData());
            $idUserSession = $this->getUserIdFromStream($datos);
            if( $idUserSession !=  $this->getUser()->getId() )
            {
                $em->remove($sesion);
            }
        }
        try{
            $em->flush();
            $this->addFlash('success','Usuarios Deslogueados con éxito');
        }catch(\Exception $e)
        {
            $this->addFlash('error','No pudieron ser deslogueados los usuarios');
        }
        
        return $this->redirectToRoute('user_list');
    }
    
    private function getUserIdFromStream($datos)
    {
        $haystak = 'haveUser";i:';
        $length_haystak = strlen($haystak);
        if($pos = stripos($datos,$haystak))
        {
            $inicio = $pos + $length_haystak;
            $fin    = stripos($datos,';',$inicio);
            return intval(substr($datos,$inicio,$fin - $inicio));
        }
        
        return 0;
    }
    
    /**
     * @Route("/system/force/{user}/logout", name="force_logout_user", defaults={"user":"__00__"})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function forceLogoutUserAction(Request $request,Usuario $user)
    {
        $em = $this->getDoctrine()->getManager();
        $sesiones = $em->getRepository('SeguridadBundle:Sessions')->findAll();
       
        foreach($sesiones as $sesion)
        {
            $datos = stream_get_contents($sesion->getSessData());
            $idUserSession = $this->getUserIdFromStream($datos);
            if( $idUserSession ==  $user->getId() )
            {
                $em->remove($sesion);
            }
        }
        
        try{
            $em->flush();
            return new JsonResponse(array('resultado' => 0, 'mensaje' => 'Usuario deslogueado con éxito'));
        }
        catch(\Exception $e ){
             return new JsonResponse(array('resultado' => 1, 'mensaje' => 'Error al desloguear el usuario' . $e->getMessage()));
        }
    }
    
    /**
     * @Route("/login", name="_login")
     * @Template("SeguridadBundle:Security:login.html.twig")
     */
    public function loginAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //if user is logged, redirecto to homepage
        if ($this->getUser()){
            return $this->redirectToRoute('homepage');
        }
        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        //var_dump($error);
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $d1 = new \DateTime();
        $d1->modify('-1 hour');
        /* recupera los intentos de login en la ultima hora */
        $logs = $em->getRepository('SeguridadBundle:Log')->getLoginActivitySinceByIp($request->getClientIp(),$d1);
        //
        $builder = new CaptchaBuilder();
        $builder->build();
        $captchaPhrase = null;
        if (count($logs) < 2){
            $captchaPhrase = $builder->getPhrase();
        }
        $request->getSession()->set('captcha',$builder->getPhrase());
        //var_dump($error);
        return array(
            'captcha' => $builder,
            'captcha_phrase'=> $captchaPhrase,
            'last_username' => $lastUsername,
            'panel' => 'login'
        );
    }

    /**
     * @Route("/login_check", name="_security_check")
     */
    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logoutAction()
    {
        // The security layer will intercept this request
    }

    /**
     * @Route("/show/template/{item}/email", name="_show_email_template")
     */
    public function showEmailTemplatesAction(Request $request, $item)
    {
        switch ($item){
            case 'emailPaso1':
                    return new Response($this->renderView('SeguridadBundle:Security:resetPasswordStep1.email.html.twig', array('link' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')));
                break;
            case 'emailPaso2':
                    return new Response($this->renderView('SeguridadBundle:Security:resetPasswordStep2.email.html.twig', array('pass' => 'xxxxxxxx')));
                break;
            case 'nuevaCuenta':
                    return new Response($this->renderView('SeguridadBundle:Security:newAccount.email.html.twig', array('pass' => 'xxxxxxxxx')));
                break;
            case 'activacionDeCuenta':
                    return new Response($this->renderView('SeguridadBundle:Usuario:cuenta.activada.email.html.twig', array()));
                break;            
        }
        return new Response($this->renderView('SeguridadBundle:Security:resetPasswordStep1.email.html.twig', array('link' => 'xxxxxxxx')));
    }
    
    /**
     * @Route("/resetPassword/{salt}/{user}", name="_reset_password_step2")
     * @Template()
     */
    public function resetPasswordStep2Action(Request $request, $salt, Usuario $user)
    {
        $em = $this->getDoctrine()->getManager();
        $error = null;

        if ($user){
            $crypt = new MCrypt($user->getSalt());
            $arr = explode("::",$crypt->desencriptar(urldecode($salt)));
            try{
                $datetime1 = new \DateTime($arr[2]);
                $datetime2 = new \DateTime();
                $interval = $datetime1->diff($datetime2);
                $diff = (int) $interval->format('%i');
                //Si pasó menos de 120 minutos
                if ($diff < 120){
                        $newPassword = $this->resetPassword($user);
                        
                        $message = \Swift_Message::newInstance()
                            ->setSubject("PLENUS - Recupero de clave")
                            ->setFrom($this->container->getParameter('mail_from_no_reply'))
                            ->setTo($arr[1])
                            ->setBody(
                                $this->renderView(
                                    'SeguridadBundle:Security:resetPasswordStep2.email.html.twig', array('pass' => $newPassword)
                                ),
                                'text/html'
                            );
                            
                        $log = new Log($user,$request->getClientIp(),"resetPassword","Step-2");
                        $log->getDescription("Regeneración de Clave Paso 2 | diff = ".$diff);
                        
                        try {
                            $em->persist($log);
                            $em->flush();
                            $this->get('mailer')->send($message);
                            $error = null;
                        }
                        catch(\Exception $e )
                        {
                            $error = "Ocurrio un error al intentar enviar el email.";
                        }                  
                }else{
                    $error = "El link de recuperación esta vencido. Vuelva a generarlo y tenga en cuenta que la duración es de 2 Hs";
                }
            }catch(\Exception $e){
                $error = "El link de recuperación es inválido. Si el problema persiste, vuelva a generar el link. Tenga en cuenta que cada link tiene una duración de 2 Hs";
            }
        }else{
            $error = "El link de recuperación es inválido. Si el problema persiste, vuelva a generar el link. Tenga en cuenta que cada link tiene una duración de 2 Hs";
        }
        if ($error){
                $log = new Log($user,$request->getClientIp(),"resetPassword","Step-2-ERROR");
                $log->getDescription("ERROR en la Regeneración de Clave Paso 2. SALT: ".urldecode($salt)."::".$encriptador->desencriptar(urldecode($salt)));
                $em->persist($log);
                $em->flush();
                $this->addFlash('error', $error);
        }else{
            $this->addFlash('success', "Se invió un e-mail a su cuenta registrada con una clave provisoria que deberá modificar la proxima vez que ingrese al sistema.");        
        }
        return $this->redirectToRoute('_login');
    }

    /**
     * @Route("/resetPassword/success", name="_reset_password_step1_success")
     * @Template()
     */
    public function resetPasswordStep1SuccessAction(Request $request)
    {
    }
    
    /**
     * Create resetPassword Form
     */
    public function createResetPasswordForm()
    {
        return $this->createFormBuilder(array())
                    ->add('_username', TextType::class, array("attr" => array('placeholder' => 'Ingrese su nombre de usuario')))
                    ->add('_email', EmailType::class, array("attr" => array('placeholder' => 'Ingrese su e-mail registrado')))
                    ->getForm();
    }
        
    /**
     * @Route("/resetPassword", name="_reset_password_step1")
     * @Method({"GET", "POST"})
     */
    public function resetPasswordStep1Action(Request $request)
    {
        if ($this->get('app.plenusConfig')->isResetPasswordActive()){
            $em = $this->getDoctrine()->getManager();
            $error = "";
    
            $form = $this->createResetPasswordForm();
            $form->handleRequest($request);
    
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $em->getRepository('SeguridadBundle:Usuario')->findOneBy(array('username' => $form["_username"]->getData()));
                if ($user && $user->isValidEmail($form["_email"]->getData())){
                    //If user isn't active the ActivityListener catch
                    $crypt = new MCrypt($user->getSalt());
                    //create a string with temporal link.
                    $post_string = urlencode($crypt->encriptar($user->getUsername()."::".$form["_email"]->getData()."::".date("Y-m-d H:i:s")));
                    $link = $this->generateUrl('_reset_password_step2',
                            array(
                                  'salt' => $post_string,
                                  'user' => $user->getId()
                                )
                    );
                    $message = \Swift_Message::newInstance()
                        ->setSubject("Plenus - Recupero de clave")
                        ->setFrom($this->container->getParameter('mail_from_no_reply'))
                        ->setTo($form["_email"]->getData())
                        ->setBody(
                            $this->renderView(
                                'SeguridadBundle:Security:resetPasswordStep1.email.html.twig',
                                array('link' => $request->getHttpHost().$link)
                            ),
                            'text/html'
                        );
                    $log = new Log($user,$request->getClientIp(),"resetPassword","Step-1");
                    $log->setDescription("Regeneración de Clave Paso 1");
                    
                    try {
                        $em->persist($log);
                        $em->flush();
                        $this->get('mailer')->send($message);
                        return new JsonResponse(array('success' => true, 'message' => '<h4>Excelente!</h4><p>Su <strong>contraseña</strong> fue reseteada con éxito. Se envió una <strong>e-mail</strong> con las instrucciones para <strong>completar el proceso</strong>.</p><p> Gracias por utilizar Plenus!.</p>'));
                    }
                    catch(\Exception $e )
                    {
                        //echo $e->getMessage();die;
                        $error = "Ocurrio un error al intentar enviar el email. Disculpe las molestias.";
                    }                    
                }
            }else if($form->isSubmitted()){
                $error = "Los datos no son válidos";
            }
            if ($error)
                return new JsonResponse(array('success' => false, 'message' => $error));
            return $this->render("SeguridadBundle:Security:renderResetPasswordForm.html.twig",
                array(
                    'form' => $form->createView(),
                )
            );
        }else{
            return new Response('<div class="alert alert-warning"><h4>Atención!</h4><p>El servicio de <strong>recuperacipon</strong> de contraseñas se encuentra <strong>inhabilitado</strong> por el momento.</p><p> Disculpe las molestias, gracias por utilizar Plenus!.</p>');
        }
    }
    
    private function resetPassword($user)
    {
        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        $pass = strtoupper(substr(md5(uniqid(null,true)), 0, 7));
        $new = $encoder->encodePassword($pass, $user->getSalt());
        $user->setPassword($new);
        $user->setChangePassword(true);
        //agrega al historial de claves la nueva
        $history = $user->getPasswordHistory();
        $history[] = array(
                    "fecha" => new \DateTime(),
                    "pass" => $pass,
                    "passHash" => $new,
                    "observacion" => "Generada automaticamente durante la regeneración de usuario"
                );
        $user->setPasswordHistory($history);
        return $pass;
    }

    /**
     * Create newAccount Form
     */
    public function createNewAccountForm($user)
    {
        //return $this->createFormBuilder(array())
        //            ->add('username', TextType::class, array("attr" => array('placeholder' => 'Ingrese un nombre de usuario')))
        //            ->add('persona', PersonaType
        //            
        //            ->getForm();
    }                        

    /**
     * @Route("/new/account/check", name="_new_account_check_data")
     * @Method({"GET", "POST"})
     */
    public function checkNewAccountDataAction(Request $request)
    {
        $response = array('success' => true, 'states' => []);
        $data = $request->request;
        if ($data->get('username') != ''){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('SeguridadBundle:Usuario')->findBy(array("username" => $data->get('username')));
            if ($user){            
                $response['states'][] = array('type' => 'usuario_username', 'hasError' => true, 'message' => 'El nombre de usuario ya está en uso.');
            }else{
                $response['states'][] = array('type' => 'usuario_username', 'hasError' => false, 'message' => '');
            }
        }
        if ($data->get('nroDocumento') != ''){
            $em = $this->getDoctrine()->getManager();
            $persona = $em->getRepository('CommonBundle:Persona')->findBy(array("tipoDocumento" => $data->get('tipoDocumento'),"dni" => $data->get('nroDocumento')));
            if ($persona){            
                $response['states'][] = array('type' => 'usuario_persona_dni','hasError' => true, 'message' => 'El dni ya existe.');
            }else{
                $response['states'][] = array('type' => 'usuario_persona_dni','hasError' => false, 'message' => '');
            }
        }
        if ($data->get('email') != ''){
            $em = $this->getDoctrine()->getManager();
            $persona = $em->getRepository('CommonBundle:Persona')->findBy(array("email" => $data->get('email')));
            if ($persona){
                $response['states'][] = array('type' => 'usuario_persona_email', 'hasError' => true, 'message' => 'El e-mail ya existe.');
            }else{
                $response['states'][] = array('type' => 'usuario_persona_email', 'hasError' => false, 'message' => '');
            }
        }        
        return new JsonResponse($response);
    }
    
    /**
     * @Route("/new/account", name="_new_account")
     * @Method({"GET", "POST"})
     * @Template("SeguridadBundle:Security:login.html.twig")
     */
    public function newAccountAction(Request $request)
    {
        if ($this->get('app.plenusConfig')->isNewAccountActive())
        {
            $em = $this->getDoctrine()->getManager();
            $user = new Usuario();
            $form = $this->createForm(UsuarioType::class, $user);
            //$form = $this->createNewAccountForm($user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid())
            {
                $newPassword = $this->resetPassword($user);
                        
                $message = \Swift_Message::newInstance()
                    ->setSubject("PLENUS - Nueva cuenta de usuario")
                    ->setFrom($this->container->getParameter('mail_from_no_reply'))
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'SeguridadBundle:Security:newAccount.email.html.twig', array('pass' => $newPassword)
                        ),
                        'text/html'
                    );
                    
                $log = new Log($user,$request->getClientIp(),"newAccount","");
                $log->getDescription("Se creo la cuenta de usuario");
                
                try {
                    $em->persist($user);
                    $em->persist($log);
                    $em->flush();
                    $this->get('mailer')->send($message);
                    return new JsonResponse(array('success' => true, 'message' => '<h4>Excelente!</h4><p>Su <strong>cuenta</strong> fue creada con éxito. Se envió una <strong>e-mail</strong> con las instrucciones para <strong>completar el proceso</strong>.</p><p> Gracias por utilizar Plenus!.</p>'));
                }
                catch(\Exception $e ){
                    //$error = 'DEBUG '.$e->getMessage();
                    if (strpos($e->getMessage(), 'constraint violation') === false )
                        $error = 'Ocurrio un error al intentar enviar el email.';
                    elseif(strpos($e->getMessage(), 'unique_dni') === false){
                        if(strpos($e->getMessage(), 'unique_email') === false){
                            if(strpos($e->getMessage(), 'unique_username') === false){
                                $error = 'Ocurrio un error al intentar guardar los datos. Si el error persiste, contacte al adminsitrador.';   
                            }else{
                                $error = 'El nombre de usuario ya esta en uso.';
                            }
                        }else{
                            $error = 'El EMAIL ingresado ya esta registrado en el sistema.';
                        }
                    }else{
                        $error = 'El DNI ingresado ya esta registrado en el sistema.';
                    }
                }
                return new JsonResponse(array('success' => false, 'message' => $error));
            }
            return $this->render("SeguridadBundle:Security:renderNewAccountForm.html.twig",
                array(
                    'form' => $form->createView(),
                )
            );
        }else{
            return new Response('<div class="alert alert-warning"><h4>Atención!</h4><p>La <strong>creación</strong> de cuentas de usuario se encuentra <strong>inhabilitada</strong> por el momento.</p><p> Disculpe las molestias, gracias por utilizar Plenus!.</p>');
        }
    }
    
    /**
     * @Route("/error403", name="error403")
     * @Template()
     */
    public function error403Action()
    {
        return array();
    }
    
    /**
     * @Route("perfil/{perfil}/data", name="perfil_data", defaults={"perfil":"__00__"})
     * @Method({"POST"})
     */
    public function dataAction(Request $request,Perfil $perfil)
    {
       return  new JsonResponse($perfil->toArray());
    }
}