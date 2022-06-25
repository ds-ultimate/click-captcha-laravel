<?php

namespace Captcha;

use Carbon\Carbon;
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
    
    public static function gc($seconds) {
        $model = new Captcha();
        $model->where('updated_at', '<', Carbon::now()->subSeconds($seconds))->delete();
    }
}
