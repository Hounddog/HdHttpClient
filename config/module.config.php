<?php

return array(
    'service_manager' => array(
        'invokables' => array(
            'HdHttpClient' => 'HdHttpClient\HttpClient',
            'HdHttpClient\Response' => 'HdHttpClient\Message\Response',
        ),
    );
);