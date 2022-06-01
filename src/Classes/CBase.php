<?php

namespace Properos\Users\Classes;

use Properos\Base\Classes\Base;
use Properos\Base\Exceptions\ApiException;
use Illuminate\Support\Facades\Validator;
use Properos\Base\Classes\Helper;

class CBase extends Base
{
    public function __construct($class, $title = "")
    {
        parent::__construct($class, $title);
    }
}
