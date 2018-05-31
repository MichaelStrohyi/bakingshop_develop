<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Redirect;
use AdminBundle\Form\RedirectsType;

class RedirectController extends Controller
{
    /**
     * Display all items in given menu.
     *
     * @param Request $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/redirect", name="admin_redirect")
     * @Template()
     **/
    public function indexAction(Request $request)
    {
        # get all redirects
        $repo = $this->getDoctrine()->getRepository('AppBundle:Redirect');
        $redirects = $repo->findAll();
        # create form
        $form = $this->createRedirectsForm($redirects, $request);
        # check if form has been submitted and is valid
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            # save all redirects
            $redirects = [];
            foreach ($form->getData()['items'] as $redirect) {
                # save ids of saved redirects into array
                if (!empty($redirect->getId())) {
                    $redirects[] = $redirect->getId();
                }

                $em->persist($redirect);
            }
            # find deleted redirects
            $deleted_redirects = $repo->getAllRedirects($redirects);
            foreach ($deleted_redirects as $key => $redirect) {
                # remove deleted redirects from db
                $em->remove($redirect);
            }

            $em->flush();

            return $this->redirectToRoute("admin_panel_index");
        }

        return [
            'form' => $form->createView(),
        ];
    }


    /**
     * Return form for manage redirects
     *
     * @param array $redirects
     * @param Request $request
     *
     * @return FormBuilder
     *
     * @author Michael Strohyi
     **/
    private function createRedirectsForm($redirects, Request $request)
    {
        $form = $this->createForm(new RedirectsType, ['items' => $redirects]);
        $form->handleRequest($request);

        return $form;
    }
}
