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
     * Action used to updatecoupons from feed-server
     *
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
     * Action used to delete expired coupons (after two weeks from expireDate),
     * deactivate just expired coupons (next day after expireDate) and to remove
     * old startDates (after two years)
     *
     * @Route("/daily", name="admin_job_daily")
     */
    public function dailyAction()
    {
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Coupon');
        $repo->deactivateExpired();
        $repo->activateStartedToday();
        $repo->removeOldDates();
        $this->removeExpiredCoupons();

        return new Response('Daily job has been finished');
    }

    /**
     * Action used to run any job needed now
     *
     * @Route("/tmp", name="admin_job_tmp")
     */
    public function tmpAction()
    {
//        $em = $this->getDoctrine()->getEntityManager();
//        $repo = $em->getRepository('AppBundle:Article');
//        $articles = $repo->findAllByHeader();
//        foreach ($articles as $article) {
//            $body = $article->getBody();
//            $body = str_replace('/articles/images', '/bc/img/articles/images', $body);
//            $article->setBody($body);
//            $em->persist($article);
//        }

//        $em->flush();
        return new Response('Tmp job has been finished');
    }

    /**
     * Action used to search and delete files, which were uploaded but are not used in articles
     *
     * @Route("/parse", name="admin_job_parse")
     */
    public function parseAction()
    {
        $web_dir = $this->getParameter('assetic.write_to');
        $kc_config = require($web_dir . DIRECTORY_SEPARATOR . 'admin/kcfinder-3.20-test2/conf/config.php');
        $upload_dir = $web_dir . $kc_config['uploadURL'];
        if (!is_dir($upload_dir)) {
            return new Response("Directory $upload_dir does not exist. Please, check configuration");
        }

        $res = $this->scan($upload_dir, $upload_dir);
        if (empty($res)) {
            $res = 'No unlinked files were found';
        }

        return new Response($res);
    }

    /**
     * Remove expired coupons
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

    /**
     * Search files in given dir and it's subdirectories and delete files (and their thumbs if exist), which are not used in articles
     *
     * @param string $dir
     * @param string $root_dir
     *
     * @return string
     * @author Michael Strohyi
     **/
    private function scan($dir, $root_dir)
    {
        # get list of files in given directory
        $files_list = opendir($dir);
        # get thumbs directory
        $thumbs_dir = $root_dir . DIRECTORY_SEPARATOR . '.thumbs' . substr($dir, strlen($root_dir));
        # get web directory from given directory
        $web_dir = str_replace('\\', '/', substr($dir, strlen(dirname($root_dir))));
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('AppBundle:Article');
        $res = '';
        # read files in directory
        while ($filename = readdir($files_list))
        {
            if ($filename == '.' || $filename == '..' || $filename == ".htaccess" || $filename == ".thumbs") {
                continue;
            }
            # if current file is not directory
            if (!is_dir($dir . DIRECTORY_SEPARATOR . $filename)) {
                # replace special symbols in filename for html-equivalents
                $web_filename = str_replace(
                    ['@','#','$','%','^','&','+','}','{','`', '=',';',','],
                    ['%40', '%23', '%24', '%25', '%5E', '%26', '%2B', '%7D', '%7B', '%60', '%3D', '%3B', '%2C', ],
                    $filename
                );
                # if file is not used in articles
                if (!$repo->isFileUsed($web_dir . '/' . $web_filename)) {
                    # get full path for file
                    $file = $dir . DIRECTORY_SEPARATOR . $filename;
                    # remove file and save result string
                    if (file_exists($file)) {
                        $res .= unlink($file) ? "File " . $file . " has been deleted<br>" : "Error! File " . $file . " has not  been deleted<br>";
                    }
                    # get full path for file's thumb
                    $file = $thumbs_dir . DIRECTORY_SEPARATOR . $filename;
                    # remove file thumb and save result string
                    if (file_exists($file)) {
                        $res .= unlink($file) ? "File " . $file . " has been deleted<br>" : "Error! File " . $file . " has not  been deleted<br>";
                    }
                }
            }
            # if current file is directory
            if (is_dir($dir . DIRECTORY_SEPARATOR . $filename)) {
                # scan this directory for files and subdirectories
                $res .= $this->scan($dir.DIRECTORY_SEPARATOR . $filename, $root_dir);
            }
        }

        return $res;
    }
}
