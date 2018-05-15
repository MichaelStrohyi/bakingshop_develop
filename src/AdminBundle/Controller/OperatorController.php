<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Operator;
use AdminBundle\Form\OperatorType;

/**
 * @Route("/operator")
 */
class OperatorController extends PageController
{
    /**
     * @Route("/", name="admin_operator_index")
     * @Template()
     */
    public function indexAction()
    {        
        $operator_list = $this->getDoctrine()->getRepository("AppBundle:Operator")->getAllOperators();
        return [
            'operator_list' => $operator_list,
        ];
    }

    /**
     * Create new operator
     *
     * @param Request $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/new", name="admin_operator_create")
     * @Template()
     **/
    public function createAction(Request $request)
    {
        $operator = new Operator;
        $form = $this->createOperatorForm($operator, $request);

        if ($form->isValid()) {
            $this->persistOperator($operator);

            return $this->redirectToRoute("admin_operator_index");
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * Update operator
     *
     * @param Operator $operator
     * @param Request $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/{id}/edit", name="admin_operator_edit", requirements={"id": "\d+"})
     * @ParamConverter("operator", class="AppBundle:Operator")
     * @Template()
     **/
    public function editAction(Operator $operator, Request $request)
    {
        $form = $this->createOperatorForm($operator, $request);

        if ($form->isValid()) {
            $this->persistOperator($operator);

            return $this->redirectToRoute("admin_operator_index");
        }
        return [
            'operator' => $operator,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete given operator
     *
     * @param Operator $operator
     * @param Request $request
     * 
     * @return Template
     * @author Michael Strohyi
     *
     * @Route("/{id}/delete", name="admin_operator_delete")
     * @ParamConverter("operator", class="AppBundle:Operator")
     * @Template()
     **/
    public function deleteAction(Operator $operator, Request $request)
    {
        $form = $this->createFormBuilder([])->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity_manager = $this->getDoctrine()->getEntityManager();
            $operator_id = $operator->getId();
            # delete given operator from coupons
            $this->deleteFromCoupons($operator_id);
            # delete operator
            $entity_manager->remove($operator);
            $entity_manager->flush();

            return $this->redirectToRoute("admin_operator_index");
        }

        return [
            'operator' => $operator,
            'form' => $form->createView(),
        ];
    }

    /**
     * Save given operator into database
     *
     * @param Operator $operator
     * 
     * @return void
     * @author Michael Strohyi
     **/
    private function persistOperator(Operator $operator)
    {
        $entity_manager = $this->getDoctrine()->getEntityManager();
        # save operator into db
        $entity_manager->persist($operator);
        $entity_manager->flush();
    }

     /**
     * Return form for create/edit operator
     *
     * @param Operator $operator
     * @param Request $request
     *
     * @return Form
     * @author Michael Strohyi
     **/
    private function createOperatorForm(Operator $operator, Request $request)
    {
            $form = $this->createForm(new OperatorType(), $operator);
            $form->handleRequest($request);

            return $form;
    }

    /**
     * Remove operator with given id from coupons: set id of other operator if any exists or null
     *
     * @return void
     * @author Michael Strohyi
     **/
    public function deleteFromCoupons($operator_id)
    {
        # get list of coupons, associated with given operator_id
        $coupons_list = $this->getDoctrine()->getRepository("AppBundle:Coupon")->findByOperator($operator_id);
        # return if no coupons are in the list
        if (empty($coupons_list)) {
            return;
        }
        # get from db list of operators with id != given operator_id
        $operator_repo = $this->getDoctrine()->getRepository("AppBundle:Operator");
        $operators_list = $operator_repo->getAllOperators([$operator_id]);
        $em = $this->getDoctrine()->getEntityManager();
        # set new random operator from operators_list for each coupon from coupons_list
        foreach ($coupons_list as $coupon) {
            $coupon->setAddedBy($operator_repo->getRandomItem($operators_list));
            $em->persist($coupon);
        }

        $em->flush();
    }
}
