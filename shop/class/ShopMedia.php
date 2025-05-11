<?php
namespace App\Plugin\Shop;
class ShopMedia extends \App\File
{
    function __construct($typeId = null)
    {
        parent::__construct();
        $this->type = 'SHOP';

        if (!is_null($typeId)) {
            $this->typeId = $typeId;
        }
    }
}