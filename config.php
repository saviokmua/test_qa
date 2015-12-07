<?php

use lib\Config;

// DB Config
Config::write('db.host', 'localhost');
Config::write('db.port', '');
Config::write('db.basename', 'testqa');
Config::write('db.user', 'testqa');
Config::write('db.password', 'testqa');

// Project Config
Config::write('path', 'http://'.$_SERVER['SERVER_NAME'].'/');