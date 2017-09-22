<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require __DIR__ . "/../../../vendor/autoload.php";
 //echo '<pre>';
$client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://localhost/phpunitdemo/three/web/api-games.php', [
            'json' => [
                'user' => '1',
            ],
        ]);

        $json = $response->getBody()->getContents();
       
        print_r($json); echo '<br>';
        
        print_r(file_get_contents(__DIR__.'/api-games-user.json'));