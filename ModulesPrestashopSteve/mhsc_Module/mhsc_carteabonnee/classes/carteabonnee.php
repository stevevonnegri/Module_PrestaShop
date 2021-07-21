<?php

class CarteAbonnee extends ObjectModel
{

    public $email;
    public $numero;
    public $id_customer;

    public static $definition = array(
        'table' => 'carte_abonnee', //table sans prefix
        'primary' => 'id_carte_abonnee', //cle primaire
        'multilang' => false, //pas de champ multilingue
        'fields' => [ //champs de la table
            'email' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isEmail', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'numero' => [
                'type' => self::TYPE_STRING, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
            'id_customer' => [
                'type' => self::TYPE_INT, //type de donnée (string, date, int, float, bool, etc....)
                'validate' => 'isCleanHTML', //nom de la methode de validation de la class validate, pas obligatoire
            ],
        ],
    );

    /**
     * @param objet de la classe CarteAbonnee remplie
     * 
     * @return int nombre de client qui correspond 
     */
    public function countCompteParNumero($numero)
    {
        return Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'carte_abonnee where numero = "'.$numero.'"');
    }

    /**
     * Recuperer un id_carte_abonnee avec un email et un numero
     */
    public function getid($objet)
    {
        return Db::getInstance()->getValue('SELECT id_carte_abonnee FROM '._DB_PREFIX_.'carte_abonnee WHERE numero = "'.$objet->numero.'"');
    }

    /**
     * Recuperer toutes les entrées de la table carte_abonnee
     */
    static function getItems()
    {
        return $data = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'carte_abonnee');
    }

    public function getItemById_customer($objet)
    {
        $data = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'carte_abonnee WHERE id_customer = "'.$objet->id_customer.'"');
        
        foreach($data as $key => $value)
        {
            if(property_exists(get_class($objet), $key)) {
                $objet->{$key} = $value;
            }
        }
        return $objet;
    }
}