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
        $this->removeExpiredCoupons();

        return new Response('Daily job has been finished');
    }

    /**
     * @Route("/tmp", name="admin_job_tmp")
     */
    public function tmpAction()
    {
/*        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('AppBundle:Article');
        $articles = $repo->findAllByHeader();
        foreach ($articles as $article) {
            $body = $article->getBody();
            $body = str_replace('/articles/images', '/bc/img/articles/images', $body);
            $article->setBody($body);
            $em->persist($article);
        }

        $em->flush();*/
        return new Response('Tmp job has been finished');
    }

    /**
     * Remove expired coupons
     *
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function removeExpiredCoupons()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $coupons = $em->getRepository('AppBundle:Coupon')->findExpired();
        foreach ($coupons as $coupon) {
            $em->remove($coupon);
        }
        $em->flush();
    }
}
