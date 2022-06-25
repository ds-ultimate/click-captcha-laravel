<?php

namespace Captcha\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class CustomAuthServiceProvider extends ServiceProvider
{
    protected $rules = [
        \Captcha\Rules\CustomCaptcha::class,
    ];
    
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/captcha.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'captcha');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->registerValidationRules();
    }
    
    private function registerValidationRules()
    {
        foreach($this->rules as $class ) {
            if(property_exists($class, 'ALIAS')) {
                $alias = $class::$ALIAS;
                Validator::extend($alias, $class .'@passes');
            }
        }
    }
}
