<?php
namespace AdminBundle\Service;

use Doctrine\ORM\Event\onFlushEventArgs;

class onFlushListener
{
    public function onFlush(onFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $classMetadata = $em->getClassMetadata('AppBundle\Entity\StoreCoupon');
        $uow = $em->getUnitOfWork();
        $recalculate = false;
        # go through new entities sheduled for insertion
        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            # check if current entity has StoreCoupon
            if (get_class($entity) == 'AppBundle\Entity\StoreCoupon') {
                # set new coupon as just verified
                $entity->setJustVerified();
                # find max discount for current coupon
                $entity->setMaxDiscount();
                # set addedBy for current coupon
                $entity->setAddedBy($em->getRepository('AppBundle:Operator')->getRandomOperator());
                # check if coupon has start date in future and in this case deactivate it
                $entity->checkStartDate();
                # save changes
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
        # go through entities sheduled for update
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            # check if current entity has StoreCoupon
            if (get_class($entity) == 'AppBundle\Entity\StoreCoupon') {
                # check if coupon has changes, which need additional manipulations with coupon and do these manipulations
                if (!$this->analyzeCouponChanges($entity, $uow->getEntityChangeSet($entity))) {
                    # continue if additional manipulations were not perfomed
                    continue;
                }
                # save changes
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
    }

    /**
     * Analize changes for given coupon, make needed additional manipulations with coupon and return true, if entity recompute is needed
     *
     * @return boolean
     * @author Michael Strohyi
     **/
    public function analyzeCouponChanges($coupon, $changes)
    {
        if (empty($changes) || empty($coupon)) {
            return;
        }

        $just_verified = false;
        # go through all changes
        foreach ($changes as $key => $value) {
            switch ($key) {
                case 'link':
                    # if link was changed
                    $old_link = $coupon->transformData($value[0]);
                    if ($old_link != $value[1]) {
                        # set justVerified flag to true
                        $just_verified = true;
                    }

                    break;

                case 'activity':
                    # if activity was changed
                    # if current coupon is active and has no startDate in future(and if it has deactivate it)
                    if ($value[1] == 1 && $value[1] != $value[0] && !$coupon->checkStartDate()) {
                        # set justVerified flag to true
                        $just_verified = true;
                    }

                    break;
                
                case 'position':
                    # if position was changed skip current iteration
                    break;

                case 'label':
                    # if label was changed
                    # find max discount for current coupon
                    $coupon->setMaxDiscount();
                    # set justVerified flag to true
                    $just_verified = true;

                    break;

                case 'startDate':
                    # if startDate was changed
                    # check if coupon has startDate in future and if it has deactivate it
                    if (!$coupon->checkStartDate()) {
                        # set justVerified flag to true
                        $just_verified = true;
                    }

                    break;

                default:
                    # in all other cases set justVerified flag to true
                    $just_verified = true;
                    break;
            }
        }
        # if justVerified flag is set mark coupon as just verified
        if ($just_verified) {
            $coupon->setJustVerified();
        }

        return $just_verified;
    }
}
