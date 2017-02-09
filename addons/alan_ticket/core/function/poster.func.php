<?php
function make_poster($info, $save_path) {
    $im = make_image($info['background']);
    if ($im == false) die("bg is not exists!");
    draw_qr($im, $info['qrcode']);
    draw_head($im, $info['head']);
    draw_wish($im, $info['wish']);
    draw_name($im, $info['name']);
    imagepng($im, $save_path);

//    imagedestory($im);

}

function make_image($bg_info) {
    $im = imagecreatefromjpeg($bg_info['path']);
    return $im;
}

function draw_qr($img, $qr_info) {
    $path = $qr_info['path'];
    $qr_img = imagecreatefromstring(file_get_contents($path));
    $qr_x = imagesx($qr_img);
    $qr_y = imagesy($qr_img);
    imagecopyresized($img, $qr_img,
        $qr_info['x'], $qr_info['y'],
        0, 0,
        $qr_info['w'], $qr_info['h'],
        $qr_x, $qr_y);
    imagedestroy($qr_img);
}

function draw_head($img, $head_info) {
    $src = imagecreatefromstring(file_get_contents($head_info['path']));
    $w = imagesx($src);
    $h = imagesx($src);
    imagealphablending($src,false);
    $transparent = imagecolorallocatealpha($src, 0, 0, 0, 127);
    $black       = imagecolorallocatealpha($src, 0, 0, 0, 0);
    $r = $w / 2;
    for($x = 0;$x < $w; $x++) {
        for($y = 0; $y < $h; $y++){
            $_x = $x - $w/2;
            $_y = $y - $h/2;
            if((($_x*$_x) + ($_y*$_y)) > ($r*$r)){
                imagesetpixel($src,$x,$y,$transparent);
            }
        }
    }

    imagecopyresized($img, $src,
        $head_info['x'], $head_info['y'],
        0, 0,
        $head_info['w'], $head_info['h'],
        $w, $h);
    imagedestroy($src);
}

function draw_wish($img, $wish_info) {
    $total_width = imagesx($img);
    $width       = $wish_info['width'];
    $font        = $wish_info['font'];
    $text        = $wish_info['text'];
    $size        = $wish_info['size'];
    $start_x     = $wish_info['x'];
    $start_y     = $wish_info['y'];
    $linespace   = $wish_info['linespace'];
    $color_code  = $wish_info['color'];
    list($r, $g, $b, $a) = explode(",", $color_code);
    $color = imagecolorallocatealpha($img, $r, $g, $b, $a);

    $i = max_two_lines($text, $size, $font, $width);
    if ($i == 0) {
        $w = get_text_width($text, $size, $font);
        imagettftext($img, $size, 0, $start_x + ($width - $w) / 2, $start_y, $color, $font, $text);
    } else {
        $len = mb_strlen($text);
        $one = mb_substr($text, 0, $i, 'utf-8');
        $w = get_text_width($one, $size, $font);
        imagettftext($img, $size, 0, $start_x + ($width - $w) / 2, $start_y, $color, $font, $one);
        $two = mb_substr($text, $i, $len - $i, 'utf-8');
        $w = get_text_width($two, $size, $font);
        $start_y += ($size + $linespace);
        imagettftext($img, $size, 0, $start_x + ($width - $w) / 2, $start_y, $color, $font, $two);
    }
}

function max_two_lines($text, $size, $font, $width) {
    $text_width = get_text_width($text, $size, $width);
    if ($text_width < $width) {
        return 0;
    }

    $_min = 999999;
    $cursor = -1;
    $len = mb_strlen($text);
    for ($i = 1; $i <= $len; $i++) {
        $l = mb_substr($text, 0, $i, 'utf-8');
        $r = mb_substr($text, $i, $len - $i, 'utf-8');
        $l_len = get_text_width($l, $size, $font);
        $r_len = get_text_width($r, $size, $font);
        if ($l_len - $r_len && $l_len - $r_len < $_min) {
            $_min = abs($l_len - $r_len);
            $cursor = $i;
        }
    }
    return $cursor;
}

function get_text_width($text, $size, $font) {
    $font = "wqy.ttf";
    $ret = imagettfbbox($size, 0, $font, $text);
    $w = $ret[2];
    return $w;
}


function draw_name($img, $name_info) {
    $size    = $name_info["size"];
    $start_x = $name_info["x"];
    $start_y = $name_info["y"];
    $width   = $name_info['width'];
    $font    = $name_info["font"];
    $text    = $name_info["text"];
    $color_code    = $name_info["color"];

    $ret = imagettfbbox($size, 0, $font, $text);
    $w = $ret[2];
    $x = $start_x + ($width - $w)/2;
    list($r, $g, $b, $a) = explode(",", $color_code);
    $color = imagecolorallocatealpha($img, $r, $g, $b, $a);
    imagettftext($img, $size, 0, $x, $start_y, $color, $font, $text);
}


function get_color($img, $r, $g, $b, $a) {
    return imagecolorallocatealpha($img, $r, $g, $b, $a);
}


function pixel($img, $x, $y, $r, $g, $b, $a) {
    $color = get_color($img, $r, $g, $b, $a);
    imagesetpixel($img, $x, $y, $color);
}

function drawantialiasPixel($img, $x,$y, $r, $g, $b, $a){
    $alpha = $a;
    $xi   = floor($x);
    $yi   = floor($y);
    if ($xi == $x && $yi == $y) {
        pixel($img, $x,$y, $r, $g, $b, $a);
    } else {
        $alpha1 = (1 - ($x - $xi)) * (1 - ($y - $yi)) * $alpha;
        if ($alpha1 > 0) {
            $color['alpha']=$alpha1;
            pixel($img, $xi,$yi, $r, $g, $b, $alpha1);
        }
        $alpha2 = ($x - $xi) * (1 - ($y - $yi)) * $alpha;
        if ($alpha2 > 0) {
            $color['alpha'] = $alpha2;
            pixel($img, $xi+1,$yi, $r, $g, $b, $alpha2);
        }
        $alpha3 = (1 - ($x - $xi)) * ($y - $yi)  * $alpha;
        if ( $alpha3 > 0 ) {
            $color['alpha'] = $alpha3;
            pixel($img, $xi,$yi+1, $r, $g, $b, $alpha3);
        }
        $alpha4 = ($x - $xi) * ($y - $yi) * $alpha;
        if ($alpha4 > 0) {
            $color['alpha']=$alpha4;
            pixel($img, $xi+1,$yi+1, $r, $g, $b, $alpha4);
        }
    }
}
