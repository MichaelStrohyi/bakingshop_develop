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
}
