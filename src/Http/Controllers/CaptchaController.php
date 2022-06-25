<?php

namespace Captcha\Http\Controllers;

use Captcha\Captcha;
use Captcha\Http\Util\ImageGenerator;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class CaptchaController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function generate(){
        $img = ImageGenerator::generateImage();
        
        $model = new Captcha();
        $model->x = $img['x'];
        $model->y = $img['y'];
        $model->type = $img['type'];
        $model->save();
        
        return response()->json([
            'id' => $model->id,
            'img' => "data:image/png;base64," . base64_encode($img['img']),
        ]);
    }
    
    public function try(Request $request){
        $data = $request->validate([
            'id' => 'required|integer',
            'x' => 'required|integer',
            'y' => 'required|integer',
        ]);
        
        $model = Captcha::findOrFail($data['id']);
        $x = $data['x'] - $model->x;
        $y = $data['y'] - $model->y;
        $ds = ImageGenerator::$DOT_SIZE / 2;
        
        switch($model->type) {
            case 0:
                $dist = $x*$x + $y*$y;
                $hit = $dist <= $ds*$ds;
                break;
            case 1:
                $hit = abs($x) <= $ds;
                $hit = $hit && (abs($y) <= $ds);
                break;
            case 2:
                //     #
                //    # #
                //   #   #
                //  #     #
                // #########
                $hit = $y <= $ds;
                $hit = $hit && (- $y/2 - $x <= $ds / 2);
                $hit = $hit && (- $y/2 + $x <= $ds / 2);
                break;
            default:
                abort(404);
        }
        
        if($hit) {
            $model->solve_key = Str::random(40);
            $model->save();
            
            return response()->json([
                'hit' => 1,
                'id' => $model->id,
                'solveKey' => $model->solve_key,
            ]);
        } else {
            //drop if user entered wrong
            $model->delete();
            
            return response()->json([
                'hit' => 0,
            ]);
        }
    }
}
