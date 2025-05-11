<?php

namespace App\Plugin\ItemGlue;

use App\File;

class ArticleMedia extends File
{
    function __construct($idArticle = null)
    {
        parent::__construct();
        $this->type = 'ITEMGLUE';

        if (!is_null($idArticle)) {
            $this->typeId = $idArticle;
        }
    }
}