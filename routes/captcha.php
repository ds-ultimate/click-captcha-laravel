<?php

Route::middleware('web')->group(function() {
    Route::get('captcha/generate', [\Captcha\Http\Controllers\CaptchaController::class, 'generate'])->name('captcha.generate');
    
    Route::post('captcha/try', [\Captcha\Http\Controllers\CaptchaController::class, 'try'])->name('captcha.try');
});
