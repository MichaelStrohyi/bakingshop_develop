<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Coupon;
use AppBundle\Entity\Store;
use AppBundle\Entity\StoreCoupon;
use AdminBundle\Form\StoreType;

/**
 * @Route("/store")
 */
class StoreController extends PageController
{
    const COUPONS_LIMIT = 200;

    /**
     * @Route("/", name="admin_store_index")
     * @Template()
     */
    public function indexAction()
    {
        $store_list = $this->getDoctrine()->getRepository("AppBundle:Store")->findAllByName();

        return [
            'store_list' => $store_list,
        ];
    }

    /**
     * Create new store
     *
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/new", name="admin_store_create")
     * @Template()
     **/
    public function createAction(Request $request)
    {
        $store = new Store;
        $form = $this->createStoreForm($store, $request);

        if ($form->isValid()) {
            $this->persistStore($store);

            return $this->redirectToRoute("admin_store_index");
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Edit store
     *
     * @param  Store  $store
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/{id}/edit", name="admin_store_edit", requirements={"id": "\d+"})
     * @ParamConverter("store", class="AppBundle:Store")
     * @Template()
     **/
    public function editAction(Store $store, Request $request)
    {
        $form = $this->createStoreForm($store, $request);

        if ($form->isValid()) {
            $this->handleLogo($store, $request);
            $this->persistStore($store);

            return $this->redirectToRoute("admin_store_index");
        }

        return [
            'store' => $store,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete given store
     *
     * @param  Store  $store
     * @param  Request  $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/{id}/delete", name="admin_store_delete")
     * @ParamConverter("store", class="AppBundle:Store")
     * @Template()
     **/
    public function deleteAction(Store $store, Request $request)
    {
        $form = $this->createFormBuilder([])->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity_manager = $this->getDoctrine()->getEntityManager();
            $store_id = $store->getId();
            # delete store
            $entity_manager->remove($store);
            $entity_manager->flush();
            # delete urls for current store from db and from menu-items
            $this->deleteFromMenus(Store::PAGE_TYPE, $store, $store_id);
            $this->deletePageUrls(Store::PAGE_TYPE, $store, $store_id);

            return $this->redirectToRoute("admin_store_index");
        }

        return [
            'store' => $store,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/autoupdate/{type}", name="admin_store_autoupdate",
     *     requirements={"type": "all|new"},
     *     defaults={"type": "new"},
     * ))
     */
    public function autoupdateAction($type)
    {
        # set timeout for php-script and for socket-connection
        $timeout = $this->getParameter('feeds_timeout');
        set_time_limit($timeout);
        $context = stream_context_create(['http'=> ['timeout' => $timeout]]);
        $url = $this->getParameter('feeds_url');
        # get feed-data from server according to run-mode
        switch ($type) {
            case 'all':
                $feed_data = file_get_contents($url, false, $context);
                # fetch coupons data from feed
                $this->fetchCouponsFromFeed($feed_data);
                break;
            case 'new':
                $url .= "&incremental=1";
                $feed_data = file_get_contents($url);
                # fetch coupons data from feed
                $this->fetchCouponsFromFeed($feed_data, true);
                break;
        }

        return new Response('Coupons updated successfully');
    }

    /**
     * Save given store into database
     *
     * @param  Store  $store
     * 
     * @return void
     * @author Michael Strohyi
     **/
    private function persistStore(Store $store)
    {
        $entity_manager = $this->getDoctrine()->getEntityManager();
        # get old store url from db
        $old_url = $entity_manager->getRepository('AppBundle:Store')->getUrlFromDB($store);
        # save store into db
        $entity_manager->persist($store);
        $entity_manager->flush();
        # add/update store url in database
        $this->updatePageUrls(Store::PAGE_TYPE, $store);

        # update store url in menus
        $entity_manager->getRepository('AppBundle:MenuItem')->updateUrls($old_url, $store->getUrl());
    }

     /**
     * Return form for create/edit store
     *
     * @param Store $store
     * @param Request $request
     *
     * @return Form
     * @author Michael Strohyi
     **/
    private function createStoreForm(Store $store, Request $request)
    {
        $form = $this->createForm(new StoreType(), $store);
        $form->handleRequest($request);

        return $form;
    }

    /**
     * Remove store logo if crrent logo was deleted and new logo was not selected
     *
     * @param Store $store
     * @param Request $request
     *
     * @return void
     * @author Michael Strohyi
     **/
    private function handleLogo(Store $store, Request $request)
    {
        if (null === $request->request->get('current_logo') && (null === $store->getLogo() || null === $store->getLogo()->getImageFile())) {
            $store->removeLogo();
        }
    }

    /**
     * Fetch coupons from the given JSON-string and save them into db
     *
     * @param string $feed_data
     * @param boolean $incr_mode
     *
     * @return void
     * @author Michael Strohyi
     **/
    private function fetchCouponsFromFeed($feed_data, $incr_mode = false)
    {   
        # get feed data from json-format
        $data = json_decode($feed_data);
        if (empty($data)) {
            return;
        }

        $feed_coupons = [];
        # run through data array to get coupons
        foreach ($data as $key => $value) {
            if (!is_object($value)) {
                continue;
            }
            # get necessary data for current coupon
            $coupon = get_object_vars($value);
            $cur_coupon = [
                'id' => $coupon['nCouponID'],
                'label' => $coupon['cLabel'],
                'code' => $coupon['cCode'],
                'link' => $coupon['cAffiliateURL'],
                'starts' => $coupon['dtStartDate'],
                'expires' => $coupon['dtEndDate'],
                'discount' => Coupon::findMaxDiscount($coupon['cLabel']),
                'status' => $coupon['cStatus'],
                'rating' => $coupon['fRating'],
                'last_updated' =>$coupon['cLastUpdated'],
            ];
            # save current coupon into array of coupons, grouped by merchant id
            $feed_coupons[$coupon['nMerchantID']][] = $cur_coupon;
        }
        # return if there was found no coupons in feed data
        if (empty($feed_coupons)) {
            return;
        }

        $doctrine = $this->getDoctrine();
        $em = $doctrine->getEntityManager();
        $coupon_repo = $doctrine->getRepository("AppBundle:Coupon");
        $operator_repo = $doctrine->getRepository("AppBundle:Operator");
        $operators =  $operator_repo->getAllOperators();
        # reset stores list
        $stores_list = [];
        # run through feed merchants array
        foreach ($feed_coupons as $feed_store_id => $feed_store_coupons) {
            # get store-object with current merchant id
            $store = $doctrine->getRepository("AppBundle:Store")->getStoreByFeedId($feed_store_id);
            # return if no store has current merchant id
            if (empty($store)) {
                continue;
            }
            # save feedId of current store into stores list
            $stores_list[] = $store->getFeedId();
            # reset flag of updated coupons
            $coupons_updated = false;
            # reset lists of coupons and new coupons for current store
            $coupons_list = [];
            $new_coupons = [];
            $coupons_count = 0;
            # run through feed-coupons array for current store
            foreach ($feed_store_coupons as $feed_coupon) {
                # break if coupons count for store is more, than limit
                if (++$coupons_count > self::COUPONS_LIMIT) {
                    break;
                }
                # get coupon-object with given feed id from db
                $store_coupon = $store->findCouponByFeedId($feed_coupon['id']);
                # check if current feed-coupon has active status
                if (strtolower($feed_coupon['status']) != "active") {
                    if (!empty($store_coupon)) {
                        # if status is not active and exists coupon-object with this feed id delete this coupon-object
                        $store->removeCoupon($store_coupon);
                        $coupons_updated = true;
                    }
                    # if status is not active goto next feed-coupon
                    continue;
                }
                # check if in current store exists other coupon with code of current feed-coupon
                $code_exists = !empty($feed_coupon['code']) ? $store->findCouponByCode($feed_coupon['code'], $feed_coupon['id']) : null;
                if (!empty($code_exists)) {
                    if (!empty($store_coupon)) {
                        # if other coupon with code of current feed-coupon exists remove current coupon-object to prevent code duplication
                        $store->removeCoupon($store_coupon);
                        $coupons_updated = true;
                    }

                    # set coupon with code of current feed-coupon as just verified
                    $code_exists->setJustVerified();
                    $code_exp_date = $code_exists->getExpireDate();
                    $feed_exp_date =$this->convertDateFromFeed($feed_coupon['expires']);
                    # check if feed-coupon expires later, than coupon-object
                    if (!empty($code_exp_date) && $code_exp_date < $feed_exp_date) {
                        # set expire date from feed-coupon
                        $code_exists->setExpireDate($feed_exp_date);
                        # if coupon object is deactivated as expired and has no start date in future, activate it
                        if ($code_exists->getActivity() == 0 && $code_exp_date <= new \DateTimeImmutable()) {
                            $code_exists->setActivity(1);
                        }
                    }

                    $coupons_updated = true;
                    # goto next feed-coupon
                    continue;
                }
                # if coupon-object for current feed-coupon exists and type (code or simple coupon) or rating of coupon changed
                if (!empty($store_coupon) && (empty($feed_coupon['code']) != empty($store_coupon->getCode()) || $feed_coupon['rating'] != $store_coupon->getRating())) {
                    # delete this coupon-object
                    $store->removeCoupon($store_coupon);
                    $store_coupon = null;
                    $coupons_updated = true;
                }
                # check if feed-coupon has been updated later than coupon-object from db
                if (!empty($store_coupon) && $store_coupon->getLastUpdated() >= $this->convertDateFromFeed($feed_coupon['last_updated'], false)) {
                    # add feed-coupon id into coupons list
                    $coupons_list[] = $feed_coupon['id'];
                    continue;
                }
                # if coupon-object for current feed-coupon exists (feed-coupon is new for store) create new coupon-object
                if (empty($store_coupon)) {
                    $store_coupon = new StoreCoupon();
                    $store_coupon
                        ->setFeedId($feed_coupon['id'])
                        ->setStore($store)
                        ->setAddedBy($operator_repo->getRandomItem($operators))
                    ;
                }
                # fill information for new coupon-object from feed-coupon
                $store_coupon
                    ->setLabel($feed_coupon['label'])
                    ->setCode(empty($feed_coupon['code']) ? null : $feed_coupon['code'])
//                    ->setLink($feed_coupon['link'])
                    ->setLink($store->getLink())
                    ->setStartDate($this->convertDateFromFeed($feed_coupon['starts']))
                    ->setExpireDate($this->convertDateFromFeed($feed_coupon['expires']))
                    ->setLastUpdated($this->convertDateFromFeed($feed_coupon['last_updated'], false))
                    ->setRating($feed_coupon['rating'])
                    ->setJustVerified()
                    ->setMaxDiscount()
                ;
                # if coupon-object is new add it into new coupons array
                if (empty($store_coupon->getId())) {
                    $new_coupons[] = $store_coupon;
                }
                # set coupons updated flag
                $coupons_updated = true;
                # add feed-coupon id into coupons list
                $coupons_list[] = $feed_coupon['id'];
            }

            # if mode of autoupdate is not incremental remove from current store coupons, which are absent coupons list
            # set coupons updated flag if any coupon has been removed
            $coupons_updated = !$incr_mode && $store->removeFeedCoupons($coupons_list) !== false ? true : $coupons_updated;
            # check if new coupons array is not empty
            if (!empty($new_coupons)) {
                # get positions of last code and last coupon offset from last code for current store
                $last_code_pos = $store->getLastCodePosition();
                $last_coupon_offset = count($store->getCoupons()) - 1 - $last_code_pos;
                # run through new coupons array
                foreach ($new_coupons as $value) {
                    # find position interval for current coupon according to its type (code or simple coupon)
                    $max_pos = empty($value->getCode()) ? ++$last_coupon_offset + $last_code_pos : ++$last_code_pos;
                    $min_pos = empty($value->getCode()) ? $last_code_pos + 1 : 0;
                    # insert coupon into store coupons list into position, calculated according to rating and position interval
                    $store->insertCouponOnPosition($value, $store->findCouponPositionByRating($value->getRating(), $max_pos, $min_pos));
                }
                # unset new coupons array to empty memory
                unset($new_coupons);
                # set coupons updated flag
                $coupons_updated = true;
            }
            # check if coupons updated flag is set
            if ($coupons_updated) {
                # set position for all store coupons according to actual position in list
                $store->actualiseCouponsPosition(self::COUPONS_LIMIT);
                # save store into db
                $em->persist($store);
            }
        }
        # check if incremental mode is off and remove all feed coupons for stores, which are absent in stores list
        if (!$incr_mode) {
            # get list of stores, which are absent in stores list and have not null feedId
            $remove_stores = $doctrine->getRepository("AppBundle:Store")->getAllFeedStores($stores_list);
            # go through remove stores list
            foreach ($remove_stores as $value) {
                # try to delete all feed coupons for current store
                if ($value->removeFeedCoupons()) {
                    # if any coupon was deleted set position for all store coupons according to actual position in list
                    $value->actualiseCouponsPosition();
                    # save store into db
                    $em->persist($value);
                }
            }

        }

        $em->flush();
    }

    /**
     * Convert given $date string into DateTime object to store in db (with converting timezone according to current server timezone)
     *
     * @param string $date
     * @param boolean $limiter
     *
     * @return DateTime|null
     * @author Michael Strohyi
     **/
    private function convertDateFromFeed($date, $limiter = true)
    {
        try {
            $date = new \DateTime($date);
            # convert imezone of given date to current server timezone)
            $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            $cur_date = new \DateTimeImmutable();
            # return date or null, if year of date is more, than 19 years after and 2 years before current date
            return $limiter && ($date->format("Y") > $cur_date->format("Y") + 19 || $date->format("Y") < $cur_date->format("Y") - 2) ? null : $date;
        } catch (\Exception $e) {
            return null;
        }
    }
}
