# PHP-WebSocket-Test
PHP Test Project for Motius

## Setup
- Set host and port you want your socket to run on in config.php
- If you want to change the value of the default threshold for acceleration in config.php

## Run
1. Run socket.php as deamon
2. Start a webserver in the root folder of the project
3. Navigate with your webbrowser to the address of the server you just started.
4. Everything should work now. Enjoy ;)

## Troubleshooting
- This project uses short open tags. So if the JavaScript variables host and port are not set or PHP is throwing an error regarding short_open_tags you have to activate the option in your php.ini
