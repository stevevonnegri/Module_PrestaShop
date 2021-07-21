<?php

//nom du fichier et nom de class identique
class EditionsProducts extends ObjectModel
{

    public $id_product;
    public $id_supplier;
    public $id_manufacturer;
    public $id_category_default;
    public $id_shop_default;
    public $id_tax_rules_group;
    public $on_sale;
    public $online_only;
    public $ean13;
    public $isbn;
    public $upc;
    public $mpn;
    public $ecotax;
    public $quantity;
    public $minimal_quantity;
    public $low_stock_threshold;
    public $low_stock_alert;
    public $price;
    public $wholesale_price;
    public $unity;
    public $unit_price_ratio;
    public $additional_shipping_cost;
    public $reference;
    public $supplier_reference;
    public $location;
    public $width;
    public $height;
    public $depth;
    public $weight;
    public $out_of_stock;
    public $additional_delivery_times;
    public $quantity_discount;
    public $customizable;
    public $uploadable_files;
    public $text_fields;
    public $active;
    public $redirect_type;
    public $id_type_directed;
    public $available_for_order;
    public $available_date;
    public $show_condition;
    public $condition;
    public $show_price;
    public $indexed;
    public $visibility;
    public $cache_is_pack;
    public $cache_has_attachments;
    public $is_virtual;
    public $cache_default_attribute;
    public $date_add;
    public $date_upd;
    public $advanced_stock_management;
    public $pack_stock_type;
    public $state;

    //tableau de definition de la classe
    public static $definition = array(
        'table' => 'product', //table sans prefix
        'primary' => 'id_product', //cle primaire
        'multilang' => true, //pas de champ multilingue
        'fields' => [ //champs de la table
            'id_supplier' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'id_manufacturer' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'id_category_default' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'id_shop_default' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire^
                'required' => true,
            ],
            'id_tax_rules_group' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'on_sale' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'online_only' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'ean13' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'isbn' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'upc' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML',
            ],
            'mpn' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML',
            ],
            'ecotax' => [ 
                'type' => self::TYPE_FLOAT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'quantity' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'minimal_quantity' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'low_stock_threshold' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'low_stock_alert' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'price' => [
                'type' => self::TYPE_FLOAT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'wholesale_price' => [
                'type' => self::TYPE_FLOAT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'unity' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'unit_price_ratio' => [
                'type' => self::TYPE_FLOAT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'additional_shipping_cost' => [
                'type' => self::TYPE_FLOAT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'reference' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'supplier_reference' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'location' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'width' => [
                'type' => self::TYPE_FLOAT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'height' => [
                'type' => self::TYPE_FLOAT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'depth' => [
                'type' => self::TYPE_FLOAT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'weight' => [
                'type' => self::TYPE_FLOAT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'out_of_stock' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'additional_delivery_times' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'quantity_discount' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'customizable' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'uploadable_files' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'text_fields' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'active' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'redirect_type' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'id_type_directed' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'available_for_order' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'available_date' => [
                'type' => self::TYPE_DATE, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'show_condition' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'condition' => [ //@Warning
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'show_price' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'indexed' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'visibility' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'cache_is_pack' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'cache_has_attachments' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'is_virtual' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'cache_default_attribute' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'date_add' => [
                'type' => self::TYPE_DATE, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'date_upd' => [
                'type' => self::TYPE_DATE, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'advanced_stock_management' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'pack_stock_type' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ],
            'state' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
                'required' => true,
            ]
        ],

    );

}