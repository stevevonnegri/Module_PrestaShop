<?php

//nom du fichier et nom de class identique
class AvisProduit extends ObjectModel
{

    public $id_avis_produit;
    public $id_product;
    public $note;
    public $avis;
    public $date_add;
    public $id_customer;
    public $nom;
    public $prenom;
    public $email;

    //tableau de definition de la classe
    public static $definition = array(
        'table' => 'avis_produit', //table sans prefix
        'primary' => 'id_avis_produit', //cle primaire
        'multilang' => false, //pas de champ miltilingue
        'fields' => [ //champs de la table
            'id_product' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'required' => true, //on precise seulement si c'est obligatoire
            ],
            'note' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
            ],
            'avis' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'required' => true, //on precise seulement si c'est obligatoire
            ],
            'date_add' => [
                'type' => self::TYPE_DATE, //type de donnée (string, date, int, float, bool, etc....)
                'required' => true, //on precise seulement si c'est obligatoire
            ],
            'id_customer' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
            ],
            'nom' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
            ],
            'prenom' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
            ],
            'email' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
            ],
        ]
    );


    /**
     * @param id_product id d'un produit
     * @param limit nombre limite delement a afficher
     */
    public function getCommentaireAvis($id_product, $limit)
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('avis_produit');
        $sql->where('id_product = '.$id_product);
        $sql->orderBy('id_avis_produit DESC');
        $sql->limit($limit);
        return Db::getInstance()->executeS($sql);
    }





}