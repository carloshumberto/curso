<?php

namespace Dashboard\CourseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {

        $session = $this->getRequest()->getSession();

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository("LoginLoginBundle:Users");

        if ($session->has("login")) {

            $login = $session->get("login");
            $username = $login->getUsername();
            $password = $login->getPassword();

            $user = $repository->findOneBy(array('userName' => $username, 'password' => $password));
            $name = $user->getFirstName();
        } else {
            $name = "Estou no Dashboard sem usuário";
        }

        return $this->render('DashboardCourseBundle:Default:index.html.twig', array('name' => $name));
    }

}
