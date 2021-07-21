<?php

class mhsc_video extends ObjectModel {

	public $id_mhsc_video;
	public $id_products;
	public $name;
	public $active;

	//tableau de définition
	public static $definition = array(
		'table' => 'mhsc_video', 
		'primary' => 'id_mhsc_video', 
		'fields' => array( 
			'id_products' => array(
				'type' => self::TYPE_INT, 
				'validate' => 'isCleanHtml', 
				'required' => true 
			),
			'name' => array(
				'type' => self::TYPE_STRING,	
				'required' => true 
			),
			'active' => array(
				'type' => self::TYPE_INT, 
				'validate' => 'isUrl',
				'required' => true 
			)
		),

	);

	static function getItemById_product($id)
	{
		$sql = new DbQuery();
		$sql->select('*');
		$sql->from('mhsc_video', 'mv');
		$sql->where('id_products = '.$id);
		return Db::getInstance()->executeS($sql);
	}

	public function updateMhsc ($mhsc_video)
    {
		$sql = "UPDATE "._DB_PREFIX_."mhsc_video SET id_products=".$mhsc_video->id_products.", name='".$mhsc_video->name."', active=".$mhsc_video->active." WHERE id_mhsc_video=".$mhsc_video->id_mhsc_video;
		return Db::getInstance()->execute($sql);
    }

	

}