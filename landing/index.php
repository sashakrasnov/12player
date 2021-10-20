<?php

error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Europe/Moscow');

define('L_PARAM', 'lang');
define('TG_PROXY', 'tg://resolve?domain=proxy&server=209.97.168.227&port=443&secret=ff493703bd0885b42c6895b769103419');

$LANGS = ['ru', 'en', 'de', 'fr', 'es', 'pt'];
$L = $_GET[L_PARAM] && in_array($_GET[L_PARAM], $LANGS) ? $_GET[L_PARAM] : 'en'; // English by default

$HOME = '/?'.L_PARAM.'='.$L;
$BOT =  'https://t-do.ru/a_12player_bot?start=lang='.$L.'_src=landing';


include 'langs/'.$L.'.php';

?>
<!DOCTYPE html>
<html lang="<?=$L;?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="canonical" href="<?=$HOME;?>">
    <link rel="icon" type="image/png" sizes="16x16" href="/logo/web_icon_16_blue.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/logo/web_icon_32_blue.png">
    <link rel="icon" type="image/png" sizes="48x48" href="/logo/web_icon_48_blue.png">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/css/styles.css" />

    <title><?=L_TITLE;?></title>
</head>
<body>
    <div class="container my-4">
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                <div class="nav justify-content-xl-start justify-content-lg-start justify-content-md-start justify-content-sm-center justify-content-center">
                    <a href="<?=$HOME;?>" id="logo"><img src="/css/img/logo.png" alt="<?=L_TITLE;?>"><span class="align-middle ml-1"><?=L_12_PLAYER;?></span></a>
                </div>
            </div> 
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 mb-2">
                <div class="nav justify-content-xl-end justify-content-lg-end justify-content-md-end justify-content-sm-center justify-content-center">
                    <?php foreach($LANGS as $l): ?>
                    <a href="/?<?=L_PARAM;?>=<?=$l;?>"><img src="/css/img/flags/<?=$l;?>.svg" class="flag-<?=$l;?> mx-1"></a>
                    <?php endforeach; ?>
                </div>
            </div> 
        </div>
        <div class="row mt-5">
            <div class="col">
                <h1><?=L_HEADER;?></h1>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col">
                <h2 style="font-weight: normal;"><?=L_SUBHEADER;?></h2>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-xl-5 col-lg-5 col-md-6 col-sm-12 col-12">
                <?=L_LIST_1;?>
            </div>
            <div class="col-xl-7 col-lg-7 col-md-6 col-sd-12 col-12">
                <?=L_LIST_2;?>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12 mt-3">
                <h3><small><?=L_WE_PLAY;?>:</small></h3>
                <div class="mt-3" style="line-height: 2.2;">
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-blue.svg" class="bullet"><span class="align-middle font-weight-medium bullet-text"><?=L_QUIZZES;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-blue.svg" class="bullet"><span class="align-middle font-weight-medium bullet-text">VR</span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-blue.svg" class="bullet"><span class="align-middle font-weight-medium bullet-text"><?=L_QUESTS;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-blue.svg" class="bullet"><span class="align-middle font-weight-medium bullet-text"><?=L_PAINTBALL;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-blue.svg" class="bullet"><span class="align-middle font-weight-medium bullet-text">FIFA, CS:GO, DOTA</span></span>
                </div>
                <div class="mt-2">
                    <a href="<?=TG_PROXY;?>" class="text-blue tg-link"><?=L_TG_PROXY;?></a>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 mt-2">
                <div class="text-blue justify-content-xl-end justify-content-lg-end d-flex" id="text-bot-setup"><?=L_TG_BOT;?></div>
                <div class="justify-content-xl-end justify-content-lg-end d-flex mt-4">
                    <a href="<?=$BOT;?>" class="tg-subscribe"><div class="text-rectangle text-uppercase text-center"><span class="align-middle"><?=L_SUBSCRIBE;?></span></div></a>
                    <div class="d-inline-block d-flex ml-1"><a href="http://t-do.ru/a12player"><img src="/css/img/tg.svg" class="tg-shape"></a></div>
                    <div class="d-inline-block d-flex ml-1"><a href="https://www.facebook.com/12playercup"><img src="/css/img/fb.svg" class="fb-shape"></a></div>
                </div>
            </div>
        </div>

        <a href="<?=$BOT;?>">

        <div class="row mt-4">
            <div class="col">
                <img src="/css/bnr/pub-quizzes-<?=$L;?>.png" srcset="/css/bnr/pub-quizzes-<?=$L;?>@2x.png 2x, /css/bnr/pub-quizzes-<?=$L;?>@3x.png 3x" class="img-fluid">
            </div>
        </div>
        <div class="row mt-1">
            <div class="col">
                <div class="subline-purple text-white text-center p-2">
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_FOOD;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_COMMUNICATION;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_DATING;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_PRIZES;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"></span>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col">
                <img src="/css/bnr/vr-<?=$L;?>.png" srcset="/css/bnr/vr-<?=$L;?>@2x.png 2x, /css/bnr/vr-<?=$L;?>@3x.png 3x" class="img-fluid">
            </div>
        </div>
        <div class="row mt-1">
            <div class="col ">
                <div class="subline-teal text-white text-center p-2">
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_ADRENALIN;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_ELBOW;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_DATING_2;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_PRIZES_2;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"></span>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col">
                <img src="/css/bnr/quest-<?=$L;?>.png" srcset="/css/bnr/quest-<?=$L;?>@2x.png 2x, /css/bnr/quest-<?=$L;?>@3x.png 3x" class="img-fluid">
            </div>
        </div>
        <div class="row mt-1">
            <div class="col">
                <div class="subline-orange text-white text-center p-2">
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_MINUTES;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_SPACE;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_LABYRINTH;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_PRIZES_3;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"></span>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col">
                <img src="/css/bnr/paintball-<?=$L;?>.png" srcset="/css/bnr/paintball-<?=$L;?>@2x.png 2x, /css/bnr/paintball-<?=$L;?>@3x.png 3x" class="img-fluid">
            </div>
        </div>
        <div class="row mt-1">
            <div class="col">
                <div class="subline-black text-white text-center p-2">
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_ADRENALIN;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_ELBOW;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_BALLS;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_PRIZES_4;?></span></span>
                    <span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"></span>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col">
                <img src="/css/bnr/fifa-cs-go-dota-all.png" srcset="/css/bnr/fifa-cs-go-dota-all@2x.png 2x, /css/bnr/fifa-cs-go-dota-all@3x.png 3x" class="img-fluid">
            </div>
        </div>
        <div class="row mt-1">
            <div class="col">
                <div class="subline-blue text-white text-center p-2">
                    <span class="d-inline-block text-nowrap2"><img src="/css/img/bullet-white.svg" class="bullet"><span class="align-middle font-weight-normal"><?=L_MANY_THINK;?></span><span class="d-inline-block text-nowrap"><img src="/css/img/bullet-white.svg" class="bullet"></span></span>
                </div>
            </div>
        </div>

        </a>

        <div class="row">
            <div class="col mt-5 mb-3">
                <div class="justify-content-xl-end justify-content-lg-end justify-content-md-end justify-content-sm-center justify-content-center d-flex">
                    <a href="<?=$BOT;?>" class="tg-subscribe"><div class="text-rectangle text-uppercase text-center"><span class="align-middle"><?=L_SUBSCRIBE;?></span></div></a>
                    <div class="d-inline-block d-flex ml-1"><a href="http://t-do.ru/a12player"><img src="/css/img/tg.svg" class="tg-shape"></a></div>
                    <div class="d-inline-block d-flex ml-1"><a href="https://www.facebook.com/12playercup"><img src="/css/img/fb.svg" class="fb-shape"></a></div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col text-center">
                <div class="top-border"><h3 class="mt-3"><?=L_PARTNERS;?>:</h3></div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col text-center">
                <img src="/css/img/quiz-please.png" srcset="/css/img/quiz-please@2x.png 2x, /css/img/quiz-please@3x.png 3x" class="quiz-please mx-xl-4 mx-lg-4 mx-md-3 mx-sm-3 mx-4 mb-4 align-middle">
                <img src="/css/img/mozgva.png" srcset="/css/img/mozgva@2x.png 2x, /css/img/mozgva@3x.png 3x" class="mozgva mx-xl-4 mx-lg-4 mx-md-3 mx-sm-3 mx-4 mb-4 align-middle">
                <img src="/css/img/mos-quiz.png" srcset="/css/img/mos-quiz@2x.png 2x, /css/img/mos-quiz@3x.png 3x" class="mos-quiz mx-xl-4 mx-lg-4 mx-md-3 mx-sm-3 mx-4 mb-4 align-middle">
                <img src="/css/img/60sec.png" srcset="/css/img/60sec@2x.png 2x, /css/img/60sec@3x.png 3x" class="60sec mx-xl-4 mx-lg-4 mx-md-3 mx-sm-3 mx-4 mb-4 align-middle">
            </div>
        </div>
    </div>
</body>
</html>
