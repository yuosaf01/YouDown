<?php

namespace App\Http\Controllers\YouTube\Models;

class SplitStream extends AbstractModel
{
    /** @var StreamFormat */
    public $video;
    /** @var StreamFormat */
    public $audio;
}
