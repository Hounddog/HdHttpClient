<?php

return array(
    'service_manager' => array(
        'invokables' => array(
            'HdHttpClient\Response' => 'HdHttpClient\Message\Response',
            'HdHttpClient\Request' => 'HdHttpClient\Message\Request',
        ),
    ),
);