<?php

namespace AppEntity;

use DoctrineORMMapping as ORM;

/**
 * @ORMEntity()
 * @ORMTable(name="products")
 */
class Product
{
    /**
     * @ORMId
     * @ORMGeneratedValue
     * @ORMColumn(type="integer")
     */
    private $id;

    /**
     * @ORMColumn(type="string", length=255)
     */
    private $name;

    /**
     * @ORMColumn(type="string", length=255)
     */
    private $image;

    /**
     * @ORMColumn(type="decimal", scale=2)
     */
    private $price;

    /**
     * @ORMColumn(type="decimal", scale=2)
     */
    private $weight;

    // Геттеры и сеттеры...
}
