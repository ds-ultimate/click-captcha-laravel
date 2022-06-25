<?php

namespace Captcha\Http\Util;



class ImageGenerator {
    // Click Captcha 0.1.1
    // Created by Jonathan Decker

    public static $HEIGHT = 200;
    public static $WIDTH = 320;
    public static $DOT_SIZE = 20;
    private static $DISTRACTORS = 10;
    private static $CONFETTI = 300;
    private static $FETTI_WIDTH = 8;
    private static $FETTI_HEIGHT = 1.2;
    public static $DESCRIPTION_SIZE = 20;
    public static $TASKS = [
        'Captcha.task.circle',
        'Captcha.task.rect',
        'Captcha.task.triangle',
    ];

    public static function generateImage()
    {
        $x = random_int(0, static::$WIDTH - static::$DOT_SIZE) + static::$DOT_SIZE / 2;
        $y = random_int(0, static::$HEIGHT - static::$DOT_SIZE) + static::$DOT_SIZE / 2;
        $type = random_int(0, count(static::$TASKS) - 1);
        $imgData = static::createImage($x, $y, $type, public_path("/fonts/arial_b.ttf"));

        return [
            "img" => $imgData,
            "x" => $x,
            "y" => $y,
            "type" => $type,
        ];
    }

    private static function createImage($x, $y, $task, $font) {
        $img = imagecreatetruecolor(static::$WIDTH, static::$HEIGHT + static::$DESCRIPTION_SIZE); 
        imagefill($img, 0, 0, imagecolorallocate($img,64,64,64)); 

        $hsv = array(0,0.8,0.8);
        $hsv[0] = (random_int(0, PHP_INT_MAX) / PHP_INT_MAX) * 0.2 + 0.8;
        $color = static::HSVtoRGB($hsv);
        
        $imgColor = imagecolorallocate($img,intval($color[0]*255.0),intval($color[1]*255.0),intval($color[2]*255.0));
        static::renderTaskPart($task, $img, $x, $y, $imgColor);
        
        for( $i = 0; $i < static::$DISTRACTORS; $i++ )
        {
            $dx = random_int(0,static::$WIDTH);
            $dy = random_int(0,static::$HEIGHT);
            $randTask = random_int(0, count(static::$TASKS) - 2);
            if($randTask >= $task) $randTask++;
            
            $temp = array($dx-$x,$dy-$y);
            $delta = $temp[0]*$temp[0] + $temp[1]*$temp[1];
            while( $delta < static::$DOT_SIZE*static::$DOT_SIZE*2)
            {
                $dx = random_int(0,static::$WIDTH);
                $dy = random_int(0,static::$HEIGHT);
                $temp = array($dx-$x,$dy-$y);
                $delta = $temp[0]*$temp[0] + $temp[1]*$temp[1];
            }
            
            static::renderTaskPart($randTask, $img, $dx, $dy, $imgColor);
        }
        
        
        imagettftext($img, static::$DESCRIPTION_SIZE, 0, 5, static::$HEIGHT + static::$DESCRIPTION_SIZE - 5,
                imagecolorallocate($img, 255, 255, 255), $font, __(static::$TASKS[$task]));

        $values = array( 0, 0, 0, 0, 0, 0, 0, 0 );
        $hsv = array(0,0.60,0.60);
        $temp = array(0,0);

        for( $i = 0; $i < static::$CONFETTI; $i++ )
        {
            $cx = random_int(0,static::$WIDTH);
            $cy = random_int(0,static::$HEIGHT + static::$DESCRIPTION_SIZE);
            $theta = 3.1415926513 * (random_int(0, PHP_INT_MAX) / PHP_INT_MAX);
            $hsv[0] = (random_int(0, PHP_INT_MAX) / PHP_INT_MAX) * 0.70;
            $color = static::HSVtoRGB($hsv);
            
            $c = cos($theta);
            $s = sin($theta);

            $values[0] = static::$FETTI_WIDTH;
            $values[1] = static::$FETTI_HEIGHT;

            $values[2] = static::$FETTI_WIDTH;
            $values[3] = -static::$FETTI_HEIGHT;

            $values[4] = -static::$FETTI_WIDTH;
            $values[5] = -static::$FETTI_HEIGHT;

            $values[6] = -static::$FETTI_WIDTH;
            $values[7] = static::$FETTI_HEIGHT;

            // upper left
            $temp[0] = $c * $values[0] + -$s * $values[1];
            $temp[1] = $s * $values[0] +  $c * $values[1];
            $values[0] = $cx+$temp[0];
            $values[1] = $cy+$temp[1];

            // bottom left
            $temp[0] = $c * $values[2] + -$s * $values[3];
            $temp[1] = $s * $values[2] +  $c * $values[3];
            $values[2] = $cx+$temp[0];
            $values[3] = $cy+$temp[1];

            // bottom right
            $temp[0] = $c * $values[4] + -$s * $values[5];
            $temp[1] = $s * $values[4] +  $c * $values[5];
            $values[4] = $cx+$temp[0];
            $values[5] = $cy+$temp[1];

            // upper right
            $temp[0] = $c * $values[6] + -$s * $values[7];
            $temp[1] = $s * $values[6] +  $c * $values[7];
            $values[6] = $cx+$temp[0];
            $values[7] = $cy+$temp[1];

            $imgColor = imagecolorallocate($img,intval($color[0]*255.0),intval($color[1]*255.0),intval($color[2]*255.0));

            imagefilledpolygon ($img, $values, 4, $imgColor);
        }
        
        for($i = 0; $i < static::$WIDTH; $i++) {
            for($j = 0; $j < static::$HEIGHT + static::$DESCRIPTION_SIZE; $j++) {
                $col = imagecolorat($img, $i, $j);
                $r = max(min((($col >> 16) & 0xFF) + random_int(0, 150) - 75, 255), 0);
                $g = max(min((($col >> 8) & 0xFF) + random_int(0, 150) - 75, 255), 0);
                $b = max(min((($col) & 0xFF) + random_int(0, 150) - 75, 255), 0);
                imagesetpixel($img, $i, $j, imagecolorallocate($img, $r, $g, $b));
            }
        }
        
        ob_start();
        imagepng($img);
        $imdata = ob_get_clean();    
        imagedestroy($img);
        return $imdata;
    }
    
    private static function HSVtoRGB(array $hsv) 
    {
        list($H,$S,$V) = $hsv;

        $H *= 6;

        $I = floor($H);
        $F = $H - $I;

        $M = $V * (1 - $S);
        $N = $V * (1 - $S * $F);
        $K = $V * (1 - $S * (1 - $F));

        switch ($I) {
            case 0:
                list($R,$G,$B) = array($V,$K,$M);
                break;
            case 1:
                list($R,$G,$B) = array($N,$V,$M);
                break;
            case 2:
                list($R,$G,$B) = array($M,$V,$K);
                break;
            case 3:
                list($R,$G,$B) = array($M,$N,$V);
                break;
            case 4:
                list($R,$G,$B) = array($K,$M,$V);
                break;
            case 5:
            case 6: //for when $H=1 is given
                list($R,$G,$B) = array($V,$M,$N);
                break;
        }
        return array($R, $G, $B);
    }
    
    private static function renderTaskPart($task, $img, $x, $y, $imgColor) {
        $ds = static::$DOT_SIZE;
        switch($task) {
            case 0:
                imagefilledarc ($img, $x, $y, $ds, $ds, 0, 360, $imgColor, IMG_ARC_PIE);
                break;
            case 1:
                imagefilledrectangle($img, $x - $ds/2, $y - $ds/2, $x + $ds/2, $y + $ds/2, $imgColor);
                break;
            case 2:
                $points = [
                    $x, $y - $ds/2,
                    $x - $ds/2, $y + $ds/2,
                    $x + $ds/2, $y + $ds/2,
                ];
                imagefilledpolygon($img, $points, $imgColor);
                break;
        }
    }
}
