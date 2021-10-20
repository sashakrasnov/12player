# -*- coding: utf-8 -*-

'''
Constants & settings
'''

TG_TOKEN = '<tg_token>'                   # Chatbot digital signature
NUM_IMAGES = 2                            # Number of images per event
URL_IMAGES = 'https://domain.tld/images'  # Base url for images
SALT_KEY = '<salt_key>'                   # Security salt key
TYPE_IMG = 'png'                          # Default image type

ERR_OK      = 0         # Error code 0: No error
ERR_CONNECT = 1         # Error code 1: Database connection error
ERR_TOKEN   = 2         # Error code 2: Digital signature error
ERR_USER_ID = 3         # Error code 3: User's id is missing error
ERR_DB      = 4         # Error code 4: Database error

DT_FORMAT = '%d.%m.%Y'
DT_EVENTS_INTERVAL  = 5 # Search period for available events 
DT_TICKETS_INTERVAL = 3 # Displaying period for tickets
