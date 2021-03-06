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
    const DEFAULT_COUPONS_LIMIT = 70;
    const ENLARGED_COUPONS_LIMIT = 110;

    /**
     * @Route("/", name="admin_store_index")
     * @Template()
     */
    public function indexAction()
    {
        $store_list = $this->getDoctrine()->getRepository("AppBundle:Store")->findAllByName(true);

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
        # set higher memory limit
        ini_set('memory_limit', '384M');
        # set timeout for php-script and for socket-connection
        $timeout = $this->getParameter('feeds_timeout');
        set_time_limit($timeout);
        $context = stream_context_create(['http'=> ['timeout' => $timeout]]);
        $url = $this->getParameter('feeds_url');
        # get feed-data from server according to run-mode
        switch ($type) {
            case 'all':
                $feed_data = file($url, 0, $context);
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
        $store_repo = $entity_manager->getRepository('AppBundle:Store');
        # get old store url from db
        $old_url = $store_repo->getUrlFromDB($store);
        # get old store link from db
        $old_link = $store_repo->findLinkById($store->getId());
        $old_name = $store_repo->findNameById($store->getId());
        # if store link changed
        if ($store->getLink() !== $old_link) {
            if ($store->getUseFeedLinks()) {
                $feed_affiliates = $entity_manager->getRepository("AppBundle:FeedAffiliate")->getAllAsNamedArray();
                $old_network = $this->findUsedNetwork($old_link, $feed_affiliates);
                $cur_network = $this->findUsedNetwork($store->getLink(), $feed_affiliates);
                # if new store link and old store link are from different networks
                if ($old_network !== $cur_network) {
                    # set lastUpdated for all feed coupons to null
                    $store->clearLastUpdatedFeedCoupons();
                    # set store link to all feed coupons
                    $store->resetLinkForAllFeedCoupons();
                }

            }
            # update link of coupons which use store's link
            $this->updateCouponsLink($store, $old_link);
        }
        # save store into db
        $entity_manager->persist($store);
        $entity_manager->flush();

        # add/update store url in database
        $this->updatePageUrls(Store::PAGE_TYPE, $store);
        # return at this point if it is a new store
        if (empty($store->getId())) {
            return;
        }

        $menu_item_repo = $entity_manager->getRepository('AppBundle:MenuItem');
        # update store url in menus if it was changed
        if ($store->getUrl() !== $old_url) {
            $menu_item_repo->updateUrls($old_url, $store->getUrl());
        }
        # update store name in menus  if it was changed
        if ($store->getName() !== $old_name) {
            $menu_item_repo->updateTitles($old_name, $store->getName());
        }
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
    private function fetchCouponsFromFeed(&$feed_data, $incr_mode = false)
    {   
        # get list of all merchant feed ids which are set for stores
        $doctrine = $this->getDoctrine();
        $store_repo = $doctrine->getRepository("AppBundle:Store");
        $used_feed_ids = $store_repo->getAllFeedId();
        # decode feed data from csv-format to array of items
        $rows = array_map('str_getcsv', $feed_data);
        unset($feed_data);
        # get list of feed-item keys
        $header = array_shift($rows);
        # get key of merchant feed id column
        $feed_id_column = array_search('MerchantID', $header);
        # return if column of feed id is not found
        if ($feed_id_column === false) {
            return;
        }

        $data = [];
        # go through array of feed data items
        foreach($rows as $row) {
            # continue if merchant feed id of current item is not found in list of used feed ids
            # continue if count of item subitems is not equival to count of feed-item keys
            if (!in_array($row[$feed_id_column], $used_feed_ids) || count($row) !== count($header)) {
                continue;
            }
            # add item into named array of decoded data
            $data[] = array_combine($header, $row);
        }
        # return array of decoded data is empty
        if (empty($data)) {
            return;
        }

        $feed_coupons = [];
        # run through data array to get coupons
        foreach ($data as $key => $coupon) {
            # replace special symbol in Label if it exists
            $coupon['Label'] = str_replace(chr(239) . chr(191) . chr(189), '', $coupon['Label']);
            # get necessary data for current coupon
            $cur_coupon = [
                'id' => $coupon['CouponID'],
                'label' => $coupon['Label'],
                'network' => strtolower($coupon['Network']),
                'code' => $coupon['CouponCode'],
                'link' => $coupon['AffiliateURL'],
                'starts' => $coupon['StartDate'],
                'expires' => $coupon['EndDate'],
                'discount' => Coupon::findMaxDiscount($coupon['Label']),
                'status' => $coupon['Status'],
                'rating' => $coupon['Rank'],
                'last_updated' =>$coupon['LastUpdated'],
            ];
            # save current coupon into array of coupons, grouped by merchant id
            $feed_coupons[$coupon['MerchantID']][] = $cur_coupon;
        }
        # return if there was found no coupons in feed data
        if (empty($feed_coupons)) {
            return;
        }
        # sort feed_coupons array by codes (coupons with codes go first) and by rating (desc)
        foreach ($feed_coupons as $key => $store_coupons) {
            usort($feed_coupons[$key], function ($a, $b) {
                if (empty($a['code']) && !empty($b['code'])) {
                    return 1;
                }

                if (!empty($a['code']) && empty($b['code'])) {
                    return -1;
                }

                if ($a['rating'] == $b['rating']) {
                    return 0;
                }

                return ($a['rating'] < $b['rating']) ? 1 : -1;
            });
        }

        unset($data);
        $em = $doctrine->getEntityManager();
        $operator_repo = $doctrine->getRepository("AppBundle:Operator");
        $operators =  $operator_repo->getAllOperators();
        $feed_affiliates = $doctrine->getRepository("AppBundle:FeedAffiliate")->getAllAsNamedArray();
        # reset stores list
        $stores_list = [];
        # run through feed merchants array
        foreach ($feed_coupons as $feed_store_id => $feed_store_coupons) {
            # get store-object with current merchant id
            $store = $store_repo->getStoreByFeedId($feed_store_id);
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
            $coupons_count = $store->getManualCouponsCount();
            $cur_date = new \DateTime();
            $cur_date->setTime(0, 0, 0);
            $coupons_limit = ini_get('max_input_vars') > 1999 ? self::ENLARGED_COUPONS_LIMIT : self::DEFAULT_COUPONS_LIMIT;
            $store_network = $this->findUsedNetwork($store->getLink(), $feed_affiliates);
            $store_network_error = false;
            # run through feed-coupons array for current store
            foreach ($feed_store_coupons as $feed_coupon) {
                # break if coupons count for store is more, than limit
                if ($coupons_count > $coupons_limit) {
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
                $code_exists = null;
                if (!empty($feed_coupon['code'])) {
                    $code_exists = $this->findCouponByCode($feed_coupon['code'], $feed_coupon['id'], $store->getCoupons());
                    $code_exists = empty($code_exists) ? $this->findCouponByCode($feed_coupon['code'], $feed_coupon['id'], $new_coupons): $code_exists;
                }

                if (!empty($code_exists)) {
                    if (!empty($store_coupon)) {
                        # if other coupon with code of current feed-coupon exists remove current coupon-object to prevent code duplication
                        $store->removeCoupon($store_coupon);
                    }

                    $coupons_updated = true;
                    # set coupon with code of current feed-coupon as just verified
                    $code_exists->setJustVerified();
                    $feed_last_updated = $this->convertDateFromString($feed_coupon['last_updated'], false);
                    # check if coupon with code of current feed-coupon is updated before current feed-coupon
                    if (!empty($code_exists->getLastUpdated()) && $feed_last_updated < $code_exists->getLastUpdated()) {
                        # goto next feed-coupon
                        continue;
                    }

                    $code_start_date = $code_exists->getStartDate();
                    $feed_start_date = $this->convertDateFromString($feed_coupon['starts']);
                    # check if feed-coupon starts in other day, than coupon-object
                    if (!empty($code_start_date) && !empty($feed_start_date) && $code_start_date != $feed_start_date) {
                        # set start date from feed-coupon
                        $code_exists->setStartDate($feed_start_date);
                        # activate coupon if new start date is less or equal to current date and coupon is not active and old start date is in future
                        if (!$code_exists->isActive() && $feed_start_date <= $cur_date && $code_start_date > $cur_date) {
                            $code_exists->activate();
                        }
                        # deactivate coupon if it has start date in future
                        $code_exists->checkStartDate();
                    }
                    $code_exp_date = $code_exists->getExpireDate();
                    $feed_exp_date = $this->convertDateFromString($feed_coupon['expires']);
                    # check if feed-coupon expires later, than coupon-object
                    if (!empty($code_exp_date) && !empty($feed_exp_date) && $code_exp_date != $feed_exp_date) {
                        # set expire date from feed-coupon
                        $code_exists->setExpireDate($feed_exp_date);
                        # if coupon object is deactivated as expired, activate it
                        if (!$code_exists->isActive() && $code_exp_date < $cur_date && $feed_exp_date >= $cur_date) {
                            $code_exists->activate();
                        }
                    }

                    $code_exists->setLastUpdated($feed_last_updated);
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
                if (!empty($store_coupon) && $store_coupon->getLastUpdated() >= $this->convertDateFromString($feed_coupon['last_updated'], false)) {
                    # add feed-coupon id into coupons list
                    $coupons_list[] = $feed_coupon['id'];
                    $coupons_count++;
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
                # get store link as link for curent feed-coupon 
                $feed_coupon_link = $store->getLink();
                # if flag to use links from feed is set for current store and network of store's link is different from feed-coupon network
                if ($store->getUseFeedLinks() && $feed_coupon['network'] !== $store_network) {
                    # set networ error flag
                    $store_network_error = true;
                }
                # if flag to use links from feed is set for current store and network of store's link is the same as feed-coupons network
                if ($store->getUseFeedLinks() && !empty($store_network) && $feed_coupon['network'] == $store_network) {
                    $feed_affiliate = $feed_affiliates[$feed_coupon['network']];
                    # check, if feed-affiliate replace rule is properly filled: affiliate_id, feed_affiliate_id is not empty and feed_affiliate_id have only one match in feed-coupon link
                    if (!empty($feed_affiliate['feed_affiliate_id']) && !empty($feed_affiliate['affiliate_id']) && substr_count($feed_coupon['link'], $feed_affiliate['feed_affiliate_id']) == 1) {
                        # replace feed_affiliate_id by affilite_id in feed-coupon's link and get it as link for curent feed-coupon 
                        $feed_coupon_link = str_replace($feed_affiliate['feed_affiliate_id'], $feed_affiliate['affiliate_id'], $feed_coupon['link']);
                    }
                }
                # fill information for new coupon-object from feed-coupon
                $store_coupon
                    ->setLabel($feed_coupon['label'])
                    ->setCode(empty($feed_coupon['code']) ? null : $feed_coupon['code'])
                    ->setLink($feed_coupon_link)
                    ->setStartDate($this->convertDateFromString($feed_coupon['starts']))
                    ->setExpireDate($this->convertDateFromString($feed_coupon['expires']))
                    ->setLastUpdated($this->convertDateFromString($feed_coupon['last_updated'], false))
                    ->setRating($feed_coupon['rating'])
                    ->setJustVerified()
                    ->setMaxDiscount()
                ;
                # deactivate coupon-object if it has startDate in future or has expire date in past
                $store_coupon->checkStartDate();
                $coupon_expire_date = $store_coupon->getExpireDate();
                if (!empty($coupon_expire_date) &&  $coupon_expire_date < $cur_date) {
                    $store_coupon->deactivate();
                }
                # if coupon-object is new add it into new coupons array
                if (empty($store_coupon->getId())) {
                    $new_coupons[] = $store_coupon;
                }
                # set coupons updated flag
                $coupons_updated = true;
                # add feed-coupon id into coupons list
                $coupons_list[] = $feed_coupon['id'];
                $coupons_count++;
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
                # set position for all store coupons
                $store->actualiseCouponsPosition();
                # if current store network error flag is not equival for store->networkError from db
                if ($store->getNetworkError() !== $store_network_error) {
                    # set new value for store->networkError
                    $store->setNetworkError($store_network_error);
                }
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
    private function convertDateFromString($date, $limiter = true)
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

    /**
     * Search for coupon with given code and feedId different from given feedId. Return target coupon if it exists or null, if it does not exist
     *
     * @param string $code
     * @param int $feedId
     *
     * @return StoreCoupon|null
     * @author Michael Strohyi
     **/
    private function findCouponByCode($code, $feedId, $coupons)
    {
        if (empty($code)) {
            return;
        }

        foreach($coupons as $coupon) {
            $exists = strtolower($coupon->getCode()) == strtolower($code) && $coupon->getFeedId() != $feedId ? true : false;
            if ($exists) {
                return $coupon;
            }
        }
    }

    /**
     * Search for coupons with given link and set them new link - current store's link
     *
     * @param string $old_link
     *
     * @return null
     * @author Michael Strohyi
     **/
    private function updateCouponsLink($store, $old_link)
    {
        $coupons = $store->getCoupons();
        $new_link = $store->getLink();
        foreach ($coupons as $coupon) {
            if ($coupon->getLink() === $old_link) {
                $coupon->setLink($new_link);
            }
        }
    }

    /**
     * Find if given link use one of networks from given feed_affiliates array.
     * Return name of used network or null
     *
     * @param string $link
     * @param array $feed_affiliates
     * @return string|null
     * @author Michael Strohyi
     **/
    private function findUsedNetwork($link, $feed_affiliates)
    {
        $res = [];
        # go through feed_affiliates
        foreach ($feed_affiliates as $key => $feed_affiliate) {
            # if affiliate_id of current feed affiliate have only one match in link
            if (!empty($feed_affiliate['affiliate_id']) && substr_count($link, $feed_affiliate['affiliate_id']) == 1) {
                # add key (feed network name) to results
                $res[] = $key;
            }
        }
        # if only one result was found return it else return null
        return count($res) == 1 ? $res[0] : null;
    }

}
