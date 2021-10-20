<?php

include 'inc/cfg.php';

if (url_query_check()) {
    include 'inc/db.php';

    if ($res = $DB->query("SELECT *, DATE_FORMAT(`dt`, '%d.%m.%Y') AS `d`, DATE_FORMAT(`dt`, '%H:%i') AS `t` FROM `events` WHERE id=".intval($_GET['i']))->fetch_assoc()) {
        $res['org_title']  = $C_orgs[$res['org_id']]['title'];
        $res['lang_title'] = $C_langs[$res['lang_id']]['title'];
        $res['game_title'] = $C_games[$res['game_id']]['title'];
        $res['city_title'] = $C_cities[$res['city_id']]['title'];
    }
    else {
        $res['error'] = 'Error: event with this id already exists.';
    }
}
else {
    $res['error'] = 'Error: wrong digital signature.';
}

echo json_encode($res, JSON_UNESCAPED_UNICODE);

?>