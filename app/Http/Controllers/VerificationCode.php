<?php
namespace App\Http\Controllers;

use Intervention\Image\Facades\Image;

class VerificationCode extends Controller
{
    public function run($name = '')
    {
        $length = 4;
        //one font config
        $font_size = $weight = $height = 25;
        $offset    = $weight / 2;

        $img = Image::canvas($weight * $length, $height, [rand(200, 255), rand(200, 255), rand(200, 255)]);

        $code = mRandStr('vc', $length);
        $this->saveCode($name, $code);
        for ($i = 0; $i < $length; $i++) {
            $img->text($code[$i], $weight * $i + $offset, $height / 2, function ($font) use ($font_size) {
                $font->file(resource_path('fonts/' . rand(1, 9) . '.ttf'));
                $font->size($font_size);
                $font->color([rand(100, 150), rand(100, 150), rand(100, 150)]);
                $font->align('center');
                $font->valign('center');
                $font->angle(rand(-45, 45));
            });
        }

        for ($i = 0; $i < ($height * 0.2); $i++) {
            $rand_xs = rand(0, $weight);
            $rand_xe = rand($weight * ($length - 1), $weight * $length);
            $rand_ys = rand(0, $height);
            $rand_ye = rand(0, $height);
            $img->line($rand_xs, $rand_ys, $rand_xe, $rand_ye, function ($draw) {
                $draw->color([rand(100, 150), rand(100, 150), rand(100, 150)]);
            });
        }

        return $img->response('jpg', 70);
    }

    public static function verify($code, $name = '')
    {
        $sessionKey = (new static)->getSessionKey($name);
        return session($sessionKey) === strtoupper($code);
    }

    protected function saveCode($name, $code)
    {
        $sessionKey = $this->getSessionKey($name);
        session([$sessionKey => strtoupper($code)]);
    }

    protected function getSessionKey($name)
    {
        return $name ? 'VerificationCode_' . $name : 'VerificationCode';
    }
}