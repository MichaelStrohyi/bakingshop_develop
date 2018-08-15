<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\FeedAffiliate;
use AdminBundle\Form\FeedAffiliatesType;

/**
 * @Route("/feed")
 */
class FeedController extends Controller
{
  
    /**
     * Display all FeedAffiliate items.
     *
     * @param Request $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/", name="admin_feed_index")
     * @Template()
     **/
    public function indexAction(Request $request)
    {
        # get all items
        $repo = $this->getDoctrine()->getRepository('AppBundle:FeedAffiliate');
        $feed_affiliates = $repo->findAll();
        # create form
        $form = $this->createFeedAffiliateForm($feed_affiliates, $request);
        # check if form has been submitted and is valid
        if ($form->isValid()) {
            # save all feed affiliates
            $this->saveFeedAffiliatesChanges($form->getData()['items']);

            return $this->redirectToRoute("admin_panel_index");
        }

        return [
            'form' => $form->createView(),
        ];
    }


    /**
     * Return form for manage feed affiliates
     *
     * @param array $feed_affiliates
     * @param Request $request
     *
     * @return FormBuilder
     *
     * @author Michael Strohyi
     **/
    private function createFeedAffiliateForm($feed_affiliates, Request $request)
    {
        $form = $this->createForm(new FeedAffiliatesType, ['items' => $feed_affiliates]);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Get feed affiliates info from given array and save them
     *
     * @param array $feed_affiliates
     * @return void
     * @author Michael Strohyi
     **/
    private function saveFeedAffiliatesChanges($feed_affiliates)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $feed_affiliates_ids = [];
        # go through feed_affiliates
        foreach ($feed_affiliates as $feed_affiliate) {
            # check if current feed_affiliate is new or already exists
            if (!empty($feed_affiliate->getId())) {
                # if feed_affiliate is not new
                # save id of current $feed_affiliate into array
                $feed_affiliates_ids[] = $feed_affiliate->getId();
            }

            $em->persist($feed_affiliate);
        }

        # find deleted feed_affiliates
        $deleted_feed_affiliates = $this->getDoctrine()->getRepository('AppBundle:FeedAffiliate')->getAllFeedAffiliates($feed_affiliates_ids);
        # go through deleted feed_affiliates
        foreach ($deleted_feed_affiliates as $feed_affiliate) {
            # remove deleted feed_affiliate from db
            $em->remove($feed_affiliate);
        }

        $em->flush();
    }
}
