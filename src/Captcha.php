<?php

namespace Captcha;

use Illuminate\Database\Eloquent\Model;

class Captcha extends Model
{
    protected $table = 'captcha';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'id',
        'x',
        'y',
        'type',
    ];
}
