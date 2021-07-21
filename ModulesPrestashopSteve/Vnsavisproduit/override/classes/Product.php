<?php

class Product extends ProductCore
{

    /**
     * @param id l'id d'un produit
     */
    public function getMoyenne($id) 
    {
        // $noteMoyenne = Db::getInstance()->getValue("SELECT AVG(note) FROM "._DB_PREFIX_."avis_produit WHERE id_product =".$id_product);
        $sql = new DbQuery();
        $sql->select('ROUND(AVG(note), 1)');
        $sql->from('avis_produit');
        $sql->where('id_product ='.$id);
        $note = Db::getInstance()->getValue($sql);
        return $note;
    }


    /**
     * @param id l'id d'un produit
     */
    public function getNombreCommentaire($id)
    {
        $sql = new DbQuery();
        $sql->select('COUNT(*)');
        $sql->from('avis_produit');
        $sql->where('id_product ='.$id);
        return Db::getInstance()->getValue($sql);
    }
}