<?php
require __DIR__ . "/../../../vendor/autoload.php";
use Goutte\Client;
use PHPUnit\Framework\TestCase;
class GameControllerTest extends TestCase
{
    public function testIndex_HasUl()
    {
        $client = new Client();
        $response = $client->request('GET', 'http://localhost/phpunitdemo/three/web/');
        $this->assertCount(6, $response->filter('ul > li'));
        //$this->assertRegexp('/<ul>/',$response->getBody()->getContents());
        
    }

    public function testApiGames_WithUser_Returns6Items()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://localhost/phpunitdemo/three/web/api-games.php', [
            'json' => [
                'user' => '1',
            ],
        ]);

        $json = $response->getBody()->getContents();
        $this->assertJsonStringEqualsJsonString(
            file_get_contents(__DIR__.'/api-games-user.json'), $json);
    }

    public function testAddRating_WithGet_HasEmptyForm()
    {
        $client = new Client();
        $response = $client->request('GET',
            'http://localhost/phpunitdemo/three/web/add-rating.php?game=1');

        $this->assertCount(1, $response->filter('form'));
        $this->assertEquals('',
            $response->filter('form input[name=score]')->attr('value'));
    } 

    public function testAddRating_WithPost_IsRedirect()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST',
            'http://localhost/phpunitdemo/three/web/add-rating.php?game=1',
            [
                'allow_redirects' => false,
                'multipart' => [
                    [
                        'name' => 'score',
                        'contents' => '5',
                    ],
                    [
                        'name' => 'screenshot',
                        'contents' => fopen(__DIR__.'/screenshot.jpg', 'r'),
                   ],
                ]
            ]);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/', $response->getHeaderLine('Location'));

        $pdo = new PDO(
            'mysql:host=localhost;dbname=gamebook_test', 'root', null);
        $statement = $pdo->prepare('SELECT * FROM rating');
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $result);
        $this->assertEquals([
            'user_id' => '1',
            'game_id' => '1',
            'score' => '5',
        ], $result[0]);

        $this->assertFileExists(
            __DIR__.'/../../../web/screenshots/1-1.jpg');
    }
    
}