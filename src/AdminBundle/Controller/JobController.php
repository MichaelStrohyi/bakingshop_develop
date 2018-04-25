<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/job")
 */
class JobController extends PageController
{
    /**
     * @Route("/autoupdate", name="admin_job_autoupdate")
     */
    public function autoupdateAction()
    {
        $date = new \DateTimeImmutable();
        $hours = $date->format('H');
        if ($hours % 12 == 0) {
            $parameters['type'] = "all";
        } else {
            $parameters['type'] = "new";
        }

        $response = $this->forward("AdminBundle:Store:autoupdate", $parameters);
        
        return $response;
    }

    /**
     * @Route("/daily", name="admin_job_daily")
     */
    public function dailyAction()
    {
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Coupon');
        $repo->deactivateExpired();
        $repo->removeOldDates();

        return new Response('Daily job has been finished');
    }
}
