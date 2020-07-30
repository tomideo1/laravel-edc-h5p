<?php

/*
 *
 * @Project        Expression project.displayName is undefined on line 5, column 35 in Templates/Licenses/license-default.txt.
 * @Copyright      Djoudi
 * @Created        2018-02-21
 * @Filename       H5PEvent.php
 * @Description
 *
 */

namespace Djoudi\LaravelH5p\Events;

use Illuminate\Support\Facades\Auth;

//use Illuminate\Contracts\Events;

class H5pResultEvent
{
    public $user_id;
    
    public $result;

    public function __construct($type, $sub_type = null, $result)
    {
        $this->user_id = Auth::id();
        $this->result = $result;
    }

}
