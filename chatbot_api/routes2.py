# -*- coding: utf-8 -*-

'''
API endpoints. This module uses only SQL database engine
'''


from flask import request
from sqlalchemy.sql import text
from functions import *
from consts import *
from tgcalc import app, engine
from hashlib import md5


@app.route('/user', methods=['GET'])
def get_user():
    '''Get telegram-user information
    Params:
        token (str): Telegram chatbot digital signature
        uid (int): Telegram-user id
    Returns:
        tuple: (user information data structure, HTTP status)
    '''

    token = request.args.get('token', type=str)
    uid   = request.args.get('uid',   type=int)

    if token == TG_TOKEN:
        if uid:
            try:
                con = engine.connect()
                u = load_user(uid, con)

                return error_resp(ERR_OK, u) if u else error_resp(ERR_DB)

            except:
                return error_resp(ERR_CONNECT)

            finally:
                con.close()
        else:
            return error_resp(ERR_USER_ID)
    else:
        return error_resp(ERR_TOKEN)


@app.route('/user/register', methods=['GET'])
def user_register():
    '''Registration of new Telegram-user
    Params:
        token (str): Telegram chatbot digital signature
        uid (int): Telegram-user id
        uname (str): Telegram username
        fname (str): Telegram-user full name. Optional
        lang_id (int): Telegram chatbot interface language. Optional. Default 0
        src (str): Source of the user. Optional
    Returns:
        tuple: (User data structure, HTTP status)
    '''

    token = request.args.get('token',      type=str)
    uid   = request.args.get('uid',        type=int)
    uname = request.args.get('uname',  '', type=str)
    fname = request.args.get('fname',  '', type=str)
    lang  = request.args.get('lang_id', 0, type=int)
    src   = request.args.get('src',    '', type=str)

    if token == TG_TOKEN:
        if uid:
            try:
                con = engine.connect()

                try:
                    con.execute(text('INSERT INTO `tg_users` (`id`, `uname`, `fname`, `langs`, `lang_id`, `city_def`, `src`) VALUES (:id, :un, :fn, 0, :l, 1, :s)'), id=uid, un=uname, fn=fname, l=lang, s=src)

                    u = load_user(uid, con)

                    return error_resp(ERR_OK, u) if u else error_resp(ERR_DB)

                except:
                    return error_resp(ERR_DB)

            except:
                return error_resp(ERR_CONNECT)

            finally:
                con.close()

        else:
            return error_resp(ERR_USER_ID)
    else:
        return error_resp(ERR_TOKEN)


@app.route('/user/update', methods=['GET'])
def user_update():
    '''Update Telegram-user data
    Params:
        token (str): Telegram chatbot digital signature
        uid (int): Telegram-user id
        lang_id (int): Telegram chatbot interface language. Optional. Default 0
        langs (int): Event languages that user can attend to the event. Bit mask
        uname (str): Telegram username
        fname (str): Telegram-user full name. Optional
        src (str): Source of the user. Optional
        city_def (int): City of events to offer by default
    Returns:
        tuple: (User information data structure, HTTP status)
    '''

    fields = {
        'lang_id':int,
        'langs':int,
        'fname':str,
        'uname':str,
        'src':str,
        'city_def':int
    }

    token = request.args.get('token', type=str)
    uid   = request.args.get('uid',   type=int)

    if token == TG_TOKEN:
        if uid:
            try:
                con = engine.connect()

                for f in fields:
                    r = request.args.get(f, type=fields[f])

                    if r is not None:
                        trans = con.begin()

                        try:
                            con.execute(text("UPDATE `tg_users` SET `" + f + "` = :f WHERE `id` = :i"), f=r, i=uid)
                            trans.commit()
                        except:
                            trans.rollback()

                            return error_resp(ERR_DB)

                u = load_user(uid, con)

                return error_resp(ERR_OK, u) if u else error_resp(ERR_DB)

            except:
                return error_resp(ERR_CONNECT)

            finally:
                con.close()

        else:
            return error_resp(ERR_USER_ID)
    else:
        return error_resp(ERR_TOKEN)


@app.route('/user/state', methods=['GET'])
def user_state():
    '''Update user information about date & city where the user should be
    
    Params:
        token (str): Telegram chatbot digital signature
        uid (int): Telegram-user id. Optional
        now (str): current date in the city of user. It depends on timmezone. Format: YYYY-MM-DD. Optional
        dt (int): relative to now date shift. 0 (today), 1 (tomorrow), 2 (after tomorrow), etc. Optional. Default 0
        city (int): City id to select the events dependent to it
    Returns:
        tuple: (list of date/city pairs, HTTP status)
    '''

    token = request.args.get('token', type=str)
    uid   = request.args.get('uid',   type=int)
    now   = request.args.get('now',   type=str)
    dt    = request.args.get('dt', 0, type=int)
    city  = request.args.get('city',  type=int)

    if token == TG_TOKEN:
        # Date and city provided
        if uid and now:
            try:
                con = engine.connect()

                if dt is not None and city is not None:
                    trans = con.begin()

                    try:
                        con.execute(text("DELETE FROM `states` WHERE `tg_id` = :i AND `dt` = DATE_ADD(:ymd, INTERVAL :d DAY)"), i=uid, ymd=now, d=dt)
                        con.execute(text("INSERT INTO `states` (`tg_id`, `dt`, `city_id`) VALUES (:i, DATE_ADD(:ymd, INTERVAL :d DAY), :c)"), i=uid, ymd=now, d=dt, c=city)
                        con.execute(text("UPDATE `tg_users` SET `city_def` = :c WHERE `id` = :i"), c=city, i=uid)
                        trans.commit()

                    except:
                        trans.rollback()
                        return error_resp(ERR_DB)

                # List of date/city
                try:
                    res = con.execute(text("SELECT *, UNIX_TIMESTAMP(`dt`) AS `utime`, DATEDIFF(`dt`, :ymd) AS `diff` FROM `states` WHERE `tg_id` = :i AND `city_id` > 0 HAVING `diff` >= 0 AND `diff` <= 3 ORDER BY `dt` ASC LIMIT 25"), ymd=now, i=uid)

                    resp = []

                    for r in res:
                        resp.append({'d': r['dt'].strftime(DT_FORMAT), # дата по формату
                                     'dt': r['diff'],                  # дата относительно <now>
                                     'utime': r['utime'],              # unixtime даты
                                     'city_id': r['city_id']})         # id города проведения мероприятия

                    return error_resp(ERR_OK, resp)

                except:
                    return error_resp(ERR_DB)

            except:
                return error_resp(ERR_CONNECT)

            finally:
                con.close()

        else:
            return error_resp(ERR_USER_ID)
    else:
        return error_resp(ERR_TOKEN)


@app.route('/user/events', methods=['GET'])
def user_events():
    '''List of available events according to the user's place (city), specific date, and languages

    Params:
        token (str): Telegram chatbot digital signature
        uid (int): Telegram-user id. Optional
        now (str): current date in the city of user. It depends on timmezone. Format: YYYY-MM-DD. Optional
        pg (int): page number. Optional. Default 0 (first page)
        lim (int): events per page. Optional. Default 5
    Returns:
        tuple: (list of dicts, HTTP status)
    '''

    token = request.args.get('token',  type=str)
    uid   = request.args.get('uid',    type=int)
    now   = request.args.get('now',    type=str)
    pg    = request.args.get('pg',  0, type=int)
    lim   = request.args.get('lim', 5, type=int)

    if token == TG_TOKEN:
        if uid and now:
            try:
                con = engine.connect()

                try:
                    res = con.execute("SELECT `e`.*, UNIX_TIMESTAMP(`e`.`dt`) AS `utime`, `u`.`city_def`, `s`.`dt` AS `state_dt`, `s`.`city_id` AS `state_city_id` FROM `tg_users` AS `u`, `events` AS `e` LEFT JOIN `states` AS `s` ON `s`.`dt`=DATE(`e`.`dt`) WHERE `u`.`id`=" + str(uid) + " AND (`e`.`lang_id` & `u`.`langs`=`e`.`lang_id` OR `u`.`langs`=0) AND `e`.`status`>=0 AND `e`.`count_paid`<`e`.`count_max` AND DATE(`e`.`dt`) BETWEEN '" + now + "' AND DATE_ADD('" + now + "', INTERVAL " + str(DT_EVENTS_INTERVAL) + " DAY) AND IF(`s`.`city_id` IS NOT NULL, `s`.`city_id`, `u`.`city_def`)=`e`.`city_id` ORDER BY `e`.`dt` ASC LIMIT " + str(lim) + " OFFSET " + str(pg*lim))

                    resp = []

                    for r in res:
                        resp.append({
                            'id': r['id'],                     # Event id
                            'title': r['title'],               # Event title
                            'descr': r['descr'],               # Event description
                            'long_descr': r['long_descr'],     # Event long description
                            'org_id': r['org_id'],             # Organizer id
                            'utime': r['utime'],               # Unixtime of the event
                            'dt': str(r['dt']),                # Datetime of the event
                            'd': r['dt'].strftime(DT_FORMAT),  # Date of the event
                            't': r['dt'].strftime('%H:%M'),    # Time of the event
                            'lang_id': r['lang_id'],           # Event language id
                            'status': r['status'],             # Event status: -1 (cancelled), 0 (not approved yet), 1 (approved)
                            'city_id': r['city_id'],           # City id of the event
                            'addr': r['addr'],                 # Address of the event
                            'map': r['map'],                   # Event on Google Map via link
                            'price': r['price'],               # Cost of a ticket to the event
                            'count_min': r['count_min'],       # Minimal count of tickets to sold required to start event
                            'count_max': r['count_max'],       # Maximum count of tickets available to sold to the event
                            'count_free': r['count_free'],     # Count of free of charge tickets to the event
                            'count_paid': r['count_paid'],     # Count of already paid tickets
                            'report': r['link'],               # ссылка на отчет о мероприятии
                            'images': event_images(r),         # Images describing the event
                            'city_def': r['city_def'],         # City of events to offer by default
                            # Hereinafter if None - the event selected by default city
                            'state_d': r['state_dt'].strftime(DT_FORMAT) if r['state_dt'] else None,
                            'state_city_id': r['state_city_id'] if r['state_city_id'] else None
                        })

                    return error_resp(ERR_OK, resp)

                except:
                    return error_resp(ERR_DB)

            except:
                return error_resp(ERR_CONNECT)

            finally:
                con.close()

        else:
            return error_resp(ERR_USER_ID)
    else:
        return error_resp(ERR_TOKEN)


@app.route('/user/tickets', methods=['GET'])
def user_tickets():
    '''List of tickets of the user

    Params:
        token (str): Telegram chatbot digital signature
        uid (int): Telegram-user id. Optional
        now (str): current date in the city of user. It depends on timmezone. Format: YYYY-MM-DD. Optional
        lim (int): records per page. Optional. Default 25
    Returns:
        tuple: (list of dicts, HTTP status)
    '''

    token = request.args.get('token',   type=str)
    uid   = request.args.get('uid',     type=int)
    lim   = request.args.get('lim', 25, type=int)
    now   = request.args.get('now',     type=str)

    if token == TG_TOKEN:
        if uid and now:
            try:
                con = engine.connect()

                try:
                    res = con.execute(text("SELECT `e`.*, UNIX_TIMESTAMP(`e`.`dt`) AS `e_utime`, `t`.`id` AS `tid`, `t`.`tg_id`, `t`.`t_buy`, `t`.`t_refund`, `t`.`status` AS `t_status`, `t`.`t_code`, `t`.`ts`, UNIX_TIMESTAMP(`t`.`ts`) AS `t_utime` FROM `events` AS `e` , `tickets` AS `t` WHERE `t`.`event_id` = `e`.`id` AND `t`.`tg_id` = :i AND DATE(`e`.`dt`) BETWEEN DATE_SUB(:ymd, INTERVAL 1 DAY) AND DATE_ADD(:ymd, INTERVAL :t DAY) ORDER BY `e`.`dt` ASC, `t`.`status` DESC LIMIT :l"), i=uid, ymd=now, t=DT_TICKETS_INTERVAL, l=lim)

                    resp = []

                    for r in res:
                        resp.append(format_ticket(r))

                    return error_resp(ERR_OK, resp)

                except:
                    return error_resp(ERR_DB)

            except:
                return error_resp(ERR_CONNECT)

            finally:
                con.close()

        else:
            return error_resp(ERR_USER_ID)
    else:
        return error_resp(ERR_TOKEN)


@app.route('/event', methods=['GET'])
def get_event():
    '''Event information

    Params:
        token (str): Telegram chatbot digital signature
        eid (int): Event id
 
    Returns:
        tuple: (dict, HTTP status)
    '''

    token = request.args.get('token', type=str)
    eid   = request.args.get('eid',   type=int)

    if token == TG_TOKEN:
        if eid is not None:
            try:
                con = engine.connect()

                try:
                    r = con.execute(text("SELECT *, UNIX_TIMESTAMP(`dt`) AS `utime` FROM `events` WHERE `id` = :i"), i=eid).fetchone()

                    resp = {
                        'id': r['id'],                    # Event id
                        'title': r['title'],              # Event title
                        'descr': r['descr'],              # Event description
                        'long_descr': r['long_descr'],    # Event long description
                        'org_id': r['org_id'],            # Organizer id
                        'dt': str(r['dt']),               # Datetime of the event
                        'utime': r['utime'],              # Unixtime of the event
                        'd': r['dt'].strftime(DT_FORMAT), # Date of the event
                        't': r['dt'].strftime('%H:%M'),   # Time of the event
                        'lang_id': r['lang_id'],          # Event language id
                        'status': r['status'],            # Event status: -1 (cancelled), 0 (not approved yet), 1 (approved)
                        'city_id': r['city_id'],          # City id of the event
                        'addr': r['addr'],                # Address of the event
                        'map': r['map'],                  # Event on Google Map via link
                        'price': r['price'],              # Cost of a ticket to the event
                        'count_min': r['count_min'],      # Minimal count of tickets to sold required to start event
                        'count_max': r['count_max'],      # Maximum count of tickets available to sold to the event
                        'count_free': r['count_free'],    # Count of free of charge tickets to the event
                        'count_paid': r['count_paid'],    # Count of already paid tickets
                        'images': event_images(r),        # Link to the photos from the event
                        'report': r['link']               # Images describing the event
                    } if r else {}

                    return error_resp(ERR_OK, resp)

                except:
                    return error_resp(ERR_DB)

            except:
                return error_resp(ERR_CONNECT)

            finally:
                con.close()

        else:
            return error_resp(ERR_USER_ID)

    else:
        return error_resp(ERR_TOKEN)


@app.route('/ticket', methods=['GET'])
def get_ticket():
    '''Ticket information

    Params:
        token (str): Telegram chatbot digital signature
        tid (int): Ticket id
 
    Returns:
        tuple: (dict, HTTP status)
    '''

    token = request.args.get('token', type=str)
    tid   = request.args.get('tid',   type=int)

    if token == TG_TOKEN:
        if tid is not None:
            try:
                con = engine.connect()
                t = load_ticket(tid, con)

                if t is not None:
                    return error_resp(ERR_OK, t)
                else:
                    return error_resp(ERR_DB)

            except:
                return error_resp(ERR_CONNECT)

            finally:
                con.close()

        else:
            return error_resp(ERR_USER_ID)

    else:
        return error_resp(ERR_TOKEN)


@app.route('/ticket/add', methods=['GET'])
def add_ticket():
    '''Add ticket

    Params:
        token (str): Telegram chatbot digital signature
        uid (int): Telegram-user id. Optional
        eid (int): Event id
        trans (str): Buying transaction
    Returns:
        tuple: (ticket information dict, HTTP status)
    '''

    token = request.args.get('token', type=str)
    uid   = request.args.get('uid',   type=int)
    eid   = request.args.get('eid',   type=int)
    tr    = request.args.get('trans', type=str)

    if token == TG_TOKEN:
        if uid is not None and eid is not None and tr is not None:
            try:
                con = engine.connect()
                trans = con.begin()

                try:
                    tid = con.execute(text("INSERT INTO `tickets` (`event_id`, `tg_id`, `t_buy`, `t_code`, `status`) VALUES (:e, :i, :t, CONCAT(LPAD(ROUND(RAND()*1000000), 5, '0'), '-', LPAD(ROUND(RAND()*100000), 5, '0')), 0)"), e=eid, i=uid, t=tr).lastrowid

                    con.execute(text("UPDATE `events` SET `count_paid`=`count_paid`+1 WHERE `id` = :i"), i=eid)

                    trans.commit()

                    t = load_ticket(tid, con)

                    if t is not None:
                        return error_resp(ERR_OK, t)
                    else:
                        return error_resp(ERR_DB)

                except:
                    trans.rollback()

                    return error_resp(ERR_DB)

            except:
                return error_resp(ERR_CONNECT)

            finally:
                con.close()

        else:
            return error_resp(ERR_USER_ID)

    else:
        return error_resp(ERR_TOKEN)


@app.route('/ticket/refund', methods=['GET'])
def refund_ticket():
    '''Ticket refund

    Params:
        token (str): Telegram chatbot digital signature
        tid (int): Ticket id
        trans (str): Transaction of the refund
    Returns:
        tuple: (ticket information dict, HTTP status)
    '''

    token = request.args.get('token', type=str)
    tid   = request.args.get('tid',   type=int)
    tr    = request.args.get('trans', type=str)

    if token == TG_TOKEN:
        if tid is not None and tr is not None:
            try:
                con = engine.connect()
                trans = con.begin()

                try:
                    con.execute(text("UPDATE `events` AS `e`, `tickets` AS `t` SET `e`.`count_paid` = `e`.`count_paid` - 1, `t`.`t_refund` = :t, `t`.`status` = -1 WHERE `e`.`id` = `t`.`event_id` AND `t`.`status` = 0 AND `t`.`id` = :i"), t=tr, i=tid)

                    trans.commit()

                    t = load_ticket(tid, con)

                    if t is not None:
                        return error_resp(ERR_OK, t)
                    else:
                        return error_resp(ERR_DB)

                except:
                    trans.rollback()

                    return error_resp(ERR_DB)

            except:
                return error_resp(ERR_CONNECT)

            finally:
                con.close()

        else:
            return error_resp(ERR_USER_ID)

    else:
        return error_resp(ERR_TOKEN)
