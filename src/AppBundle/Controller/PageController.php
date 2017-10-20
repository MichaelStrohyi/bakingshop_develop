<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Menu;

class PageController extends Controller
{
    /**
     * @Route("/{prefix}", name="homepage",
     *     requirements={"prefix": "amp/|"},
     *     defaults={"prefix": ""},
     * )
     */
    public function homepageAction(Request $request)
    {
        $article_repo = $this->getDoctrine()->getRepository('AppBundle:Article');

        $article = $article_repo->getHomepage();
        if (!$article) {
            throw $this->createNotFoundException();
        }

        $parameters['request'] = $request;
        $parameters['article'] = $article;

        return $this->forward('AppBundle:Article:page', $parameters);
    }

    /**
     * @Template()
     */
    public function sidebarAction()
    {
        $menus = $this->getDoctrine()->getRepository('AppBundle:Menu')->findAll();

        return [
            'menus' => $menus,
        ];
    }

    /**
     * @Template()
     */
    public function headerSlideshowAction()
    {
        return [];
    }

    /**
     * Render menu for amp-page by name and type.
     *
     * @param Menu|string  $name
     * @param string $link_prefix
     * @param string $type
     *
     */
    public function menuAction($name, $link_prefix = '', $type = null)
    {
        if (is_string($name)) {
            $menu = $this->getDoctrine()->getRepository('AppBundle:Menu')->findOneByName($name);
        } elseif ($name instanceof Menu) {
            $menu = $name;
        } else {
            $menu = null;
        }
        $parameters['menu'] = $menu;
        $parameters['menu_type'] = $type;
        $parameters['link_prefix'] = $link_prefix;

        return empty($link_prefix) ? $this->render('AppBundle:Page:menu.html.twig', $parameters) : $this->render('AppBundle:amp/Page:menu.html.twig', $parameters);
    }
}
