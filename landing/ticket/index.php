<?php

/**
 *  Ticket generation script
 *
 *  It validates the ticket id by the specified key hash,
 *  takes corrsponding template, integrates QR-code,
 *  and saves the ticket image in the folder
 *
 *  Usage:
 *    1) default: //localhost/83a33a77f3b10b6c15fbda84eaf050d4/34981-60059-21.png
 *    2) direct:  //localhost/ticket/?key=83a33a77f3b10b6c15fbda84eaf050d4&id=34981-60059-21&type=gif
 */

error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Europe/Moscow');


define('SALT_KEY', '7QU=64&3F4a%');
define('IMG_ADDR', 'img/');
define('TPL_ADDR', 'tpl/');
define('QR_S', 300);
define('IMG_TYPE', explode('.', $_SERVER['REDIRECT_URL'])[1] ?? $_GET['type'] ?? 'png');


function load_ticket_file($id) {
    $fn = IMG_ADDR.$id.'.'.IMG_TYPE;

    header('Content-type: image/'.IMG_TYPE);
    header('Cache-control: public');
    header('Pragma: cache');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time()+ 86400*14) . ' GMT');
    header('Content-Length: ' . filesize($fn));

    @readfile($fn);
}

/**
 *  Input params
 *      @id: Full ticket number consists of <code>-<id>
 *      @key: Authorization key
 */

$t_no = $_GET['id'];
$key  = $_GET['key'];

$o = 0; // $o > 0 if @key is a right key

for ($i=1; $i<=4; $i++) {
    if (md5($t_no.$i.SALT_KEY) == $key) {
        $o = $i;

        break;
    }
}

// @key is Ok
if ($o) {
    // The ticket image was already generated earlier. Let's read and output the ticket image for faster serving
    if (file_exists(IMG_ADDR.$t_no.'.'.IMG_TYPE)) {
        load_ticket_file($t_no);
    }
    // First-time access to the ticket image. Let's generate the ticket image on the fly and save for later usage
    else {
        $check_url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/checkin/?t='.$t_no;
        $qr_url = 'https://chart.googleapis.com/chart?'.
                   http_build_query(['chs' => QR_S.'x'.QR_S, 'cht' => 'qr', 'chl' => $check_url, 'choe'=>'UTF-8']);

        $tpl = @imagecreatefrompng(TPL_ADDR.$o.'.png');
        $qr  = @imagecreatefromstring(file_get_contents($qr_url));

        if($tpl && $qr) {
            $font = 5;
            $font_h = imagefontheight($font);
            $font_w = imagefontwidth($font);

            $fo = IMG_ADDR.$t_no.'.'.IMG_TYPE;

            imagestring($qr, 5, round((QR_S-strlen($t_no)*$font_w)/2), round(QR_S-(34+$font_h)/2), $t_no,imagecolorallocate($qr, 0, 0, 0));
            imagecopy($tpl, $qr, 0, imagesy($tpl)-QR_S, 0, 0, QR_S, QR_S);

            header('Content-type: image/'.IMG_TYPE);

            if(IMG_TYPE == 'jpg') {
                @imagejpeg($tpl, null, 100);
                @imagejpeg($tpl, $fo, 100);
            }
            elseif(IMG_TYPE == 'png') {
                @imagepng($tpl, null, 9);
                @imagepng($tpl, $fo, 9);
            }
            elseif(IMG_TYPE == 'gif') {
                @imagegif($tpl);
                @imagegif($tpl, $fo);
            }
        }
        // No template and QR-code. Let's output default "no image"
        else {
            load_ticket_file('no-image');
        }
    }
}
// Wrong @key. Let's output default "no image"
else {
  load_ticket_file('no-image');
}

?>