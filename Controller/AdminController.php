<?php

namespace Basefony\PageBundle\Controller;

use Basefony\PageBundle\Form\PageType;
use Basefony\AppBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Basefony\PageBundle\Entity\Page;

class AdminController extends BaseController
{
    /**
     * Show pages
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getRepository('BasefonyPageBundle:Page');
        $total = $em->findAll();

        $pages = $em->findPage(
            $request->query->getInt('page', 1),
            $this->getParameter('admin_per_page'),
            $request->query->getAlpha('sort', 'id'),
            $request->query->getAlpha('direction', 'ASC')
        );

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $total,
            $request->query->getInt('page', 1),
            $this->getParameter('admin_per_page') // per page
        );

        return $this->render('BasefonyPageBundle:Admin:index.html.twig', [
            'pages' => $pages,
            'pagination' => $pagination
        ]);
    }

    /**
     * Search pages
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getRepository('BasefonyPageBundle:Page');
        $keyword = $request->query->get('keyword');
        $total = $em->getTotalCount($keyword);

        $pages = $em->searchResultArr(
            $keyword,
            $request->query->getInt('page', 1),
            $this->getParameter('admin_per_page'),
            $request->query->getAlpha('sort', 'id'),
            $request->query->getAlpha('direction', 'ASC')
        );

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $total,
            $request->query->getInt('page', 1),
            10 // per page
        );


        return $this->render('BasefonyPageBundle:Admin:index.html.twig', [
            'pages' => $pages,
            'pagination' => $pagination
        ]);
    }

    /**
     * Add page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $page = new Page();

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        $error = [];

        if ($form->isValid()) {

            $page->setCreatedAt(new \DateTime('Now'));
            $page->setUpdatedAt(new \DateTime('Now'));
            $page->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            $this->addFlash('success','Sayfa eklendi.');

            return $this->redirectToRoute('basefony_admin_page_index');
        }

        return $this->render('BasefonyPageBundle:Admin:add.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    /**
     * Delete page
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Page $page)
    {
        if (!$page) {
            throw $this->createNotFoundException('No guest found');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($page);
        $em->flush();

        $this->addFlash('success','Sayfa silindi.');

        return $this->redirectToRoute('basefony_admin_page_index');
    }

    /**
     * Edit page
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('BasefonyPageBundle:Page')->find($id);

        if (!$page) {
            throw $this->createNotFoundException(
                'No product found for id ' . $id
            );
        }

        $form = $this->createForm(PageType::class, $page, ['method'=>'PATCH']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            $this->addFlash('success','Sayfa düzenlendi.');
            return $this->redirectToRoute('basefony_admin_page_index');
        }

        return $this->render('BasefonyPageBundle:Admin:edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
