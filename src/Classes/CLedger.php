<?php

namespace Properos\Users\Classes;

use Properos\Base\Exceptions\ApiException;
use Properos\Base\Classes\Base;
use Properos\Users\Models\UserLedger;

class CLedger extends Base
{
	function __construct()
	{
		parent::__construct(UserLedger::class, 'UserLedger');     
	}

	public function init_fillable()
    {
        foreach ($this->model->getFillable() as $key) {
            $this->fillable[$key] = null;
        }
    }

}