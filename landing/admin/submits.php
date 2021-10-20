<?php

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__))
    die;

/**
 *  Submits processing
 */

if ($_POST['submit']):

    // Login
    if ($_REQUEST['task'] == 'logout') {
        unset_auth();

        header($ADMIN_PAGE_LOC);
        exit;
    }

    // Logout
    if ($_REQUEST['task'] == 'login') {

        // User exists
        if ($_POST['email'] && $_POST['passw'] && $U = get_user('email', $_POST['email'])) {

            // Password check
            if (hash_equals(md5($_POST['passw'].SALT_KEY), $U['passw'])) {
                $old_auth = $U['auth_key'];

                $U['auth_key'] = md5($U['id'].$U['email'].microtime(true));

                upd_user($old_auth, 'auth_key', $U['auth_key']);
                set_auth($U['auth_key']);

                header($ADMIN_PAGE_LOC);
                exit;
            }

            // Wrong password
            else {
                $MSG = 'Invalid username or password.'; unset($U);
            }
        }

        // User not found
        else {
            $MSG = 'Invalid username or password.'; unset($U);
        }

    }


    // Below only for authorized users
    if ($_REQUEST['task'] == 'events' && $_REQUEST['p'] == 'edit' && $U['auth_key']) {
        list($dd, $mm, $yy) = explode('.', $_POST['d']);
        list($hh, $ii) = explode(':', $_POST['t']);

        // Converting input values to integers to prevent from SQL injection
        $P = array_map('intval', $_POST);

        // Event date
        $dt = $yy.'-'.$mm.'-'.$dd.' '.$hh.':'.$ii;

        // Event id. "0" for new event, or "> 0" for existing event
        $event_id = $P['event_id'];

        if (!$P['org'])
            $MSG .= '<li>Company providing the event</li>';

        if (!$P['city'])
            $MSG .= '<li>City of the event</li>';

        if (!$P['lang'])
            $MSG .= '<li>Language of the event</li>';

        if (!($dd && $mm && $yy && checkdate($mm, $dd, $yy) && $hh >= '00' && $hh <= '23' && $ii >= '00' && $ii <= '59' && ($dt >= strftime('%Y-%m-%d %H:%M') || $event_id)))
            $MSG .= '<li>Date and time of the event. It could not be earlier of the current date and time</li>';

        if ($P['price'] <= 0)
            $MSG .= '<li>Ticket price</li>';

        if ($P['count_min'] <= 0)
            $MSG .= '<li>Minimum number of tickets to be sold to start the event</li>';

        if ($P['count_max'] <= 0 || $P['count_max'] < $P['count_min'])
            $MSG .= '<li>Maximum number of tickets in the event</li>';

        if ($P['count_free'] < 0 || $P['count_free'] > $P['count_max'])
            $MSG .= '<li>Number of free tickets in the event</li>';

        if (!$_POST['game'])
            $MSG .= '<li>Type of the event</li>';

        if (!$_POST['title'])
            $MSG .= '<li>Title of the event</li>';
        
        if (!$_POST['addr'])
            $MSG .= '<li>Address</li>';
        
        if (!$_POST['map'])
            $MSG .= '<li>Link to the map</li>';
        
        if (!$_POST['descr'])
            $MSG .= '<li>Short description of the event</li>';

        if (!$_POST['long_descr'])
            $MSG .= '<li>Long description of the event</li>';

        if ($_POST['link'] && !filter_var($_POST['link'], FILTER_VALIDATE_URL))
            $MSG .= '<li>Link to the photo album after the event is completed</li>';

        // Updating info if no error message has been occurred
        if (!$MSG) {
            $link   = $DB->escape_string($_POST['link']);
            $title  = $DB->escape_string($_POST['title']);
            $addr   = $DB->escape_string($_POST['addr']);
            $map    = $DB->escape_string($_POST['map']);
            $descr  = $DB->escape_string($_POST['descr']);
            $ldescr = $DB->escape_string($_POST['long_descr']);

            // Updating only certain fields
            if ($event_id) {
                $res = $DB->query("UPDATE `events` SET `title`='$title', `addr`='$addr', `map`='$map', `descr`='$descr', `descr`='$descr', `long_descr`='$ldescr', `admin_id`={$U['id']}, `game_id`={$P['game']} WHERE `id`=".$event_id);

                if (!$res)
                    $MSG .= '<li>A database error has occurred while updating the event</li>';
            }

            // Adding new event and getting new event id
            else {
                $res = $DB->query("
                    INSERT INTO `events` (
                        `title`,
                        `descr`,
                        `long_descr`,
                        `org_id`,
                        `lang_id`,
                        `dt`,
                        `status`,
                        `game_id`,
                        `city_id`,
                        `price`,
                        `count_min`,
                        `count_max`,
                        `count_free`,
                        `count_paid`,
                        `link`,
                        `admin_id`,
                        `addr`,
                        `map`
                    )
                    VALUES (
                        '$title',
                        '$descr',
                        '$ldescr',
                        {$P['org']},
                        {$P['lang']},
                        '$dt:00',
                        0,
                        {$P['game']},
                        {$P['city']},
                        {$P['price']},
                        {$P['count_min']},
                        {$P['count_max']},
                        {$P['count_free']},
                        0,
                        '',
                        {$U['id']},
                        '$addr',
                        '$map'
                    )"
                );

                if (!$res)
                    $MSG .= '<li>A database error has occurred while adding new event</li>';
                else
                    $event_id = $DB->insert_id;
            }
        }

        if ($event_id) {
            for ($i = 1; $i <= NUM_IMAGES; $i++) {
                $eimg = 'event_img_'.$i;

                // Image uploaded
                if ($_FILES[$eimg]['error'] == UPLOAD_ERR_OK) {

                    if (dirname($_FILES[$eimg]['type']) == 'image') {
                        $ext = pathinfo($_FILES[$eimg]['name'], PATHINFO_EXTENSION);
                        $dst = $_SERVER['DOCUMENT_ROOT'].UPLOAD_DIR.'/'.$event_id.'-'.$i.'.'.$ext;

                        if (move_uploaded_file($_FILES[$eimg]['tmp_name'], $dst)) {
                            if (!$DB->query("UPDATE `events` SET `img_ext_$i`='$ext' WHERE `id`=".$event_id))
                                $MSG .= '<li>An error occurred while updating image imformation on the server</li>';
                        }
                        else
                            $MSG .= '<li>An error occurred while image processing</li>';
                    }
                    else {
                        $MSG .= '<li>The uploaded file is not an image type</li>';
                    }
                }
            }
        }

        // Checking for errors one more time
        if ($MSG) {
            $MSG = 'Please, correct to the following data:<ul>'.$MSG.'</ul>';
        }
        else {
            header($ADMIN_PAGE_LOC);
            exit;
        }
    }

    // Telegram-users
    if ($_REQUEST['task'] == 'bot' && $_REQUEST['p'] == 'users' && $U) {
        $org_id = intval($_GET['i']);

        if (url_query_check() && $org_id) {

            foreach ($U['city_id'] ? array($U['city_id']) : array_keys($C_cities) as $c) {

                if (isset($_POST['city-'.$c])) {
                    $usr = array_map('trim', explode(',', $_POST['city-'.$c]));

                    $DB->query("DELETE FROM `{$_GET['b']}_admins` WHERE `org_id`=$org_id AND `city_id`=$c");

                    for ($u = 0; $u < count($usr); $u++) if($usr[$u] != '') if(!$DB->query("INSERT INTO `{$_GET['b']}_admins` (`uname`, `org_id`, `city_id`) VALUES ('{$usr[$u]}', $org_id, $c)"))
                        $MSG .= 'A database error occurred while updating';
                }
            }
        }
    }

    // Tickets
    if ($_REQUEST['task'] == 'tickets' && $_REQUEST['p'] == 'check' && $U) {
        $tid = intval($_GET['i']);
        $tcode = $DB->escape_string($_GET['code']);

        if ($tid && $tcode && url_query_check()) {
            $DB->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

            if ($DB->query("UPDATE `tickets` SET `status` = 1 WHERE `id` = $tid AND `t_code`='$tcode'"))
                $DB->commit();
            else
                $DB->rollback();
        }
    }

endif;

if ($_REQUEST['task'] == 'events' && $_REQUEST['p'] == 'status-up' && $U['auth_key']) {
    if (url_query_check()) {
        $DB->query("UPDATE `events` SET `status`=1 WHERE `id`=".intval($_REQUEST['i']));
    }

    header($ADMIN_PAGE_LOC);
    exit;
}

if ($_REQUEST['task'] == 'events' && $_REQUEST['p'] == 'status-dn' && $U['auth_key']) {
    if (url_query_check()) {
        $DB->query("UPDATE `events` SET `status`=-1 WHERE `id`=".intval($_REQUEST['i']));
    }

    header($ADMIN_PAGE_LOC);
    exit;
}

if ($_REQUEST['task'] == 'events' && $_REQUEST['p'] == 'remove' && $U['auth_key']) {
    if (url_query_check()) {
        $e = $DB->query("SELECT `id`, `img_ext` FROM `events` WHERE `id`=".intval($_REQUEST['i']))->fetch_assoc();

        unlink($_SERVER['DOCUMENT_ROOT'].UPLOAD_DIR.'/'.$e['id'].'.'.$e['img_ext']);

        $DB->query("DELETE FROM `events` WHERE `id`=".$e['id']);
        //$DB->query("DELETE FROM `tickets` WHERE `event_id`=".$e['id']);
    }

    header($ADMIN_PAGE_LOC);
    exit;
}

?>