# Click CAPTCHA - A Visual CAPTCHA for Human Authentication #

Click CAPTCHA is a concept PHP implementation of a visual CAPTCHA which requires a single mouse click to authenticate that the user is human. The user must select a circle in a raster image hidden among dashes and like-colored distractors.

The CAPTCHA generates a 320x200 PNG image using PHP. The distracting squares and dashes make image processing approach difficult.

Example generated image. The target circle is on the far right, middle:

![alt tag](https://raw.github.com/ds-ultimate/click-captcha-laravel/master/example_image.png)

A running example of the code this is based on: http://www.rabbitfury.com/captcha/

## Usage ##

Requirements:
- Fontawsome 5
- JQuery

Use the folloing code in the laravel blades:
```
<div class="form-group row text-center">
    <div class="d-inline-block ml-auto mr-auto">
        <x-captcha::elm/>
        @if($errors->has('custom_captcha'))
            <div class="text-danger">
                {{ $errors->first('custom_captcha') }}
            </div>
        @endif
    </div>
</div>
```

Use the folloing for validation:
```
    'custom_captcha' => ['required|customCaptcha'],
```
