<?php

require_once(_PS_ROOT_DIR_ . '/modules/mhsc_videoproduct/classes/mhsc_video.php');

class ProductController extends ProductControllerCore
{
    public function initContent()
    {
        $liste = mhsc_video::getItemById_product($_GET['id_product']);

        if($liste != NULL) 
        {
            $video = new mhsc_video($liste['0']['id_mhsc_video']);

            $this->context->smarty->assign(
                [
                    'mhsc_video' => $video,
                    'mhsc_video_url' => 'modules/mhsc_videoproduct/views/assets/img/'
                ]);
        }

        return parent::initContent();
    }
}