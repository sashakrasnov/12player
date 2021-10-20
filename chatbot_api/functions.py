# -*- coding: utf-8 -*-

'''
Helper functions
'''

import flask.json as json
from sqlalchemy.sql import text
from consts import *
from hashlib import md5


def error_resp(code, resp=None):
    '''Response from the Flask server

    Args:
        code (int): Error code number
        resp (dict): Response data. Optional, but HTTP 405 status is returned if not provided
    Returns:
        tuple: (JSON data, HTTP status code)
    '''

    if resp is not None:
        return json.jsonify({
            'error': code,
            'response': resp
        }), 200
    else:
        return json.jsonify({
            'error': code
        }), 405


def load_ticket(id, con):
    '''Loads ticket information by id

    Args:
        id (int): ticket id
        con: Database connection context variable
    Returns:
        dict: Ticket data structure
    '''

    q = text("SELECT `e`.*, UNIX_TIMESTAMP(`e`.`dt`) AS `e_utime`, `t`.`id` AS `tid`, `t`.`tg_id`, `t`.`t_buy`, `t`.`t_refund`, `t`.`status` AS `t_status`, `t`.`t_code`, `t`.`ts`, UNIX_TIMESTAMP(`t`.`ts`) AS `t_utime` FROM `events` AS `e` , `tickets` AS `t` WHERE `t`.`event_id` = `e`.`id` AND `t`.`id` = :i")

    try:
        return format_ticket(con.execute(q, i=id).fetchone())

    except:
        return None


def format_ticket(r):
    '''Formats raw event and ticket data after SQL-query execution

    Args:
        r: data after SQL-query execution
    Returns:
        dict: Event and ticket data structure
    '''

    if r:
        # Event data structure
        event = {
            'id': r['id'],                     # Event id
            'title': r['title'],               # Event title
            'descr': r['descr'],               # Event description
            'long_descr': r['long_descr'],     # Event long description
            'org_id': r['org_id'],             # Organizer id
            'lang_id': r['lang_id'],           # Event language id
            'dt': str(r['dt']),                # Event 'datetime'
            'd': r['dt'].strftime(DT_FORMAT),  # Date of the event
            't': r['dt'].strftime('%H:%M'),    # Time of the event
            'utime': r['e_utime'],             # Unixtime of the event
            'status': r['status'],             # Event status: -1 (cancelled), 0 (not approved yet), 1 (approved)
            'game_id': r['game_id'],           # Event type id
            'city_id': r['city_id'],           # City id of the event
            'addr': r['addr'],                 # Address of the event
            'map': r['map'],                   # Event on Google Map via link
            'price': r['price'],               # Cost of a ticket to the event
            'count_min': r['count_min'],       # Minimal count of tickets to sold required to start event
            'count_max': r['count_max'],       # Maximum count of tickets available to sold to the event
            'count_free': r['count_free'],     # Count of free of charge tickets to the event
            'count_paid': r['count_paid'],     # Count of already paid tickets
            'link': r['link'],                 # Link to the photos from the event
            'images': event_images(r)          # Images describing the event
        }

        t_no = '{}-{}'.format(r['t_code'], r['tid'])
        ticket = {
            'id': r['tid'],                    # Id of the ticket to the event
            'tg': r['tg_id'],                  # Id of the Telegram user
            'buy': r['t_buy'],                 # Ticket buying transaction
            'refund': r['t_refund'],           # Ticket refunding transaction
            'code': r['t_code'],               # Ticked code
            'number': t_no,                    # Full ticked number: <code>-<id>
            'status': r['t_status'],           # Ticked status: -1 (return), 0 (not used yet), 1 (used)
            'utime': r['t_utime'],             # Payment process unixtime
            'dt': str(r['ts']),                # Payment process timestamp
            'd': r['ts'].strftime(DT_FORMAT),  # Date of the event
            't': r['ts'].strftime('%H:%M'),    # Time of the event
            'image': URL_IMAGES + '/t/' + t_no + '.' + TYPE_IMG + '?key=' + md5('{}{}{}'.format(t_no, r['org_id'], SALT_KEY).encode('utf-8')).hexdigest()
        }

        return {
            'event': event,
            'ticket': ticket
        }

    else:
        return {}

    
# Загрузка юзера по его id
def load_user(id, con, cache=None):
    '''Loads user information by user id

    Args:
        id (int): user id
        con: database connection context variable
        cache: memcached database handler. Optional, if available
    Returns:
        dict: user data structure
    '''
    if cache is not None:
        try:
            val = cache.get('tg:' + str(id)).decode()
        except:
            val = ''
    else:
        val = None

    try:
        r = con.execute(text(
            "SELECT *, UNIX_TIMESTAMP(`ts`) AS `utime` \
                FROM `tg_users` \
                WHERE `id` = :i"), i=id).fetchone()

        resp = {
            'uid': r['id'],                    # Tekegram user id
            'uname': r['uname'],               # Telegram username
            'fname': r['fname'],               # Full username
            'langs': r['langs'],               # Event languages that user can attend to the event. Bit mask
            'lang_id': r['lang_id'],           # Telegram chatbot interface language
            'city_def': r['city_def'],         # City of events to offer by default
            'ts': str(r['ts']),                # Timestamp of user registration
            'utime': r['utime'],               # Unixtime of user registration
            'src': r['src'],                   # Source of the user 
            'd': r['ts'].strftime(DT_FORMAT),  # Date of registration
            't': r['ts'].strftime('%H:%M')     # Time of registration
        } if r else {}

    except:
        resp = None

    if resp:
        resp['cache'] = val
    else:
        resp = {'cache': val} if val else None

    return resp


def event_images(e, id='id'):
    '''Builds the list of URLs of images about the event

    Args:
        e (dict): raw event data after SQL-query execution
    Returns:
        list: list of URLs to the images
    '''
    imgs = list()

    for i in range(1, NUM_IMAGES + 1):
        img_ext = 'img_ext_' + str(i)

        if e[img_ext]:
            imgs.append(URL_IMAGES + '/e/' + str(e[id]) + '-' + str(i) + '.' + e[img_ext])

    return imgs
