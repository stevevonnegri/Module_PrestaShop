<?php

class Clubmembre extends ObjectModel
{
    public $lastname;
    public $firstname;
    public $email;


    public static $definition = array(
        'table' => 'clubmembre',
        'primary' => 'id_clubmembre', 
        'multilang' => false, 
        'fields' => [ 
            'prenom' => [
                'type' => self::TYPE_STRING, 
                'validate' => 'isCleanHTML', 
            ],
            'nom' => [
                'type' => self::TYPE_STRING, 
                'validate' => 'isCleanHTML', 
            ],
            'email' => [
                'type' => self::TYPE_STRING, 
                'validate' => 'isEmail', 
            ],
            'id_client' => [
                'type' => self::TYPE_INT, 
                'validate' => 'isCleanHTML', 
            ],
        ],
    );

    /**
     * Recuperer toutes les entrées de la table carte_abonnee
     */
    static function getItems()
    {
        return $data = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'clubmembre');
    }

    public function getItemById_customer($objet)
    {
        $data = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'clubmembre WHERE id_customer = "'.$objet->id_customer.'"');
        
        foreach($data as $key => $value)
        {
            if(property_exists(get_class($objet), $key)) {
                $objet->{$key} = $value;
            }
        }
        return $objet;
    }

    static function getItemByMail($email)
    {
        $data = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'clubmembre WHERE email = "'.$email.'"');

        return $data;
    }

}