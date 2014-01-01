<?php
    $_configuration = array(
            'mysql' => array(
                'prefix'    => 'cms_',
                'default' => array(
                    'host'      => 'localhost',
                    'user'      => 'shoutzor',
                    'pass'      => '',
                    'db'        => 'shoutzor'
                )
            ),
            
            'smtp' => array(
                    'default' => array(
                            'host' => 'smtp.gmail.com',
                            'port' => 465,
                            'auth' => true,
                            'user' => '',
                            'pass' => ''
                        )
                ),
                
            'settings' => array(
                'ssl'       => false,
                'smtp'      => true,
                'siteurl'   => '127.0.0.1'
            )
        );
        
    $_salt = array(
            'password'  => '',
            'token'     => '',
            'default'   => ''
        );

    define("SITEURL", ($_configuration['settings']['ssl'] ? "https://" : "http://") . $_configuration['settings']['siteurl'].'/');
    define("SITEADMINURL", SITEURL . "admin/");

    define("SQL_PREFIX", $_configuration['mysql']['prefix']);

    define("MUSIC_UPLOAD_DIR", "/home/shoutzor/music/");
    define("QUEUE_TRACK_DELAY", 40);
    define("QUEUE_ARTIST_DELAY", 30);
    define("QUEUE_ALBUM_DELAY", 20);
    define("USER_REQUEST_DELAY", 5);