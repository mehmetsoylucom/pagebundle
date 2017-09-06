<?php

namespace Basefony\PageBundle\Controller;

use Basefony\AppBundle\Controller\BaseController;

class FrontController extends BaseController
{
    /**
     * Show page
     *
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($slug)
    {
        if (!$slug) {
            throw $this->createNotFoundException('Page not found');
        }

        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('BasefonyPageBundle:Page')->findOneBy(['slug'=>$slug]);

        if (!$page) {
            throw $this->createNotFoundException('Page not found');
        }

        return $this->render('BasefonyPageBundle:Front:show.html.twig',[
            'page' => $page
        ]);
    }
}
