<?php

namespace HdHttpClient

class Module
{
    public function getServiceConfig()
    {
        return array(
            'factories' =>  array(
                'HdHttpClient' => function($sm) {
                    $client = new HttpClient();
                    $client->setResponse($sm->get('HdHttpClient\Response'));
                    return $client;
                }
            ),
        );
    }
}