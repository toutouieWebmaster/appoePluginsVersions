<?php
namespace App\Plugin\InteractiveMap;
class InterMapMedia extends \App\File
{
    function __construct($id = null)
    {
        parent::__construct();
        $this->type = 'INTERACTIVEMAP';

        if (!is_null($id)) {
            $this->typeId = $id;
        }
    }
}