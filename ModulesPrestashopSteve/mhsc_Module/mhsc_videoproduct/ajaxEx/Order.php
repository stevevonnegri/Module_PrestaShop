<?php

class Order extends OrderCore
{

    public function getOrderByReference($reference)
    {
        $liste = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'orders WHERE reference = "'.$reference.'"');
        return $liste;
    }
}