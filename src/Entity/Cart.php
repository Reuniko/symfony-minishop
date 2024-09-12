<?php

namespace AppEntity;

use DoctrineORMMapping as ORM;

/**
 * @ORMEntity()
 * @ORMTable(name="carts")
 */
class Cart
{
    /**
     * @ORMId
     * @ORMGeneratedValue
     * @ORMColumn(type="integer")
     */
    private $id;

    /**
     * @ORMColumn(type="integer")
     */
    private $userId;

    /**
     * @ORMColumn(type="integer", nullable=true)
     */
    private $deliveryServiceId;

    /**
     * @ORMColumn(type="integer", nullable=true)
     */
    private $paymentServiceId;

    /**
     * @ORMColumn(type="decimal", scale=2)
     */
    private $deliveryPrice;

    /**
     * @ORMColumn(type="integer")
     */
    private $deliveryMinDays;

    /**
     * @ORMColumn(type="integer")
     */
    private $deliveryMaxDays;

    /**
     * @ORMColumn(type="datetime")
     */
    private $createdAt;

    /**
     * @ORMColumn(type="boolean")
     */
    private $isPay;

    /**
     * @ORMColumn(type="decimal", scale=2)
     */
    private $totalPaymentSum;

}
