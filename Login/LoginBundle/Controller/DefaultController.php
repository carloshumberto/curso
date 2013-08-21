<?php

namespace Login\LoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Login\LoginBundle\Entity\Users;
use Login\LoginBundle\Models\Login;

class DefaultController extends Controller {

    public function indexAction(Request $request) {

        $session = $this->getRequest()->getSession();

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("LoginLoginBundle:Users");

        if ($request->getMethod() == "POST") {

            $session->clear();

            $username = $request->get('username');
            $password = sha1($request->get('password'));
            $remember = $request->get('remember');

            $user = $repository->findOneBy(array('userName' => $username, 'password' => $password));

            if ($user) {
                if ($remember == "remember-me") {

                    $login = new Login();
                    $login->setUsername($username);
                    $login->setPassword($password);

                    $session->set("login", $login);
                }
                return $this->render('DashboardCourseBundle:Default:index.html.twig', array('name' => $user->getFirstName()));
            } else {
                return $this->render('LoginLoginBundle:Default:login.html.twig', array('name' => "Login Failed"));
            }
        } else {
            if ($session->has("login")) {

                $login = $session->get("login");
                $username = $login->getUsername();
                $password = $login->getPassword();

                $user = $repository->findOneBy(array('userName' => $username, 'password' => $password));

                if ($user) {
                    //return $this->render('LoginLoginBundle:Default:welcome.html.twig', array('name' => $user->getFirstName()));
                    //return $this->render('DashboardCourseBundle:Default:index.html.twig', array('name' => $user->getFirstName()));
                    $name = $user->getFirstName();
                } else {
                    $name = "System Failed on getting user info!";
                }
                return $this->render('LoginLoginBundle:Default:login.with.session.html.twig', array('name' => $name));
            } else {
               //Sem sessão
               return $this->render('LoginLoginBundle:Default:login.html.twig');
            }
        }
    }

    public function signupAction(Request $request) {

        if ($request->getMethod() == "POST") {

            $username = $request->get('username');
            $firstname = $request->get('firstname');
            $password = sha1($request->get('password'));

            $user = new Users();
            $user->setUserName($username);
            $user->setFirstName($firstname);
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->emailAction($user);

            return $this->render('LoginLoginBundle:Default:login.html.twig', array('name' => "Usuário inserido com sucesso!"));
        } else {
            return $this->render('LoginLoginBundle:Default:signup.html.twig');
        }
    }

    public function logoutAction(Request $request) {

        $session = $this->getRequest()->getSession();
        $session->clear();
        return $this->render('LoginLoginBundle:Default:login.html.twig', array('name' => null));
        
    }

    private function emailAction(Users $user) {
        if ($user) {
            $message = \Swift_Message::newInstance()
                    ->setSubject('Hello: ' . $user->getUserName())
                    ->setFrom('carloshumberto@gmail.com')
                    ->setTo('carloshumberto@gmail.com')
                    ->setBody(
                    $this->renderView(
                            'LoginLoginBundle:Default:email.html.twig', array('name' => $user->getUserName())
                    )
                    )
            ;
            $this->get('mailer')->send($message);
        }
        //return $this->render('LoginLoginBundle:Default:welcome.html.twig');
    }

}