<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DefaulControllerTest extends WebTestCase
{
    public function testIndexRendersFizzBuzzConsole(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('title', 'Console FizzBuzz');
        self::assertSelectorTextContains('h1', 'Donnez du rythme à vos nombres.');
        self::assertSelectorExists('form#fizzbuzz-form');
        self::assertSelectorExists('input[name="int1"]');
        self::assertSelectorExists('input[name="int2"]');
        self::assertSelectorExists('input[name="limit"]');
        self::assertSelectorExists('input[name="str1"]');
        self::assertSelectorExists('input[name="str2"]');
        self::assertSelectorTextContains('button[type="submit"]', 'Générer la séquence');
        self::assertSelectorTextContains('#stats-button', 'Voir les stats');
        self::assertSame('/api/docs', $crawler->filter('a[href="/api/docs"]')->attr('href'));
        self::assertSame('/css/app.css', $crawler->filter('link[rel="stylesheet"]')->attr('href'));
        self::assertSame('/js/app.js', $crawler->filter('script[src="/js/app.js"]')->attr('src'));
        self::assertSelectorExists('#result-grid');
        self::assertSelectorExists('#stats-box');
    }

    public function testFormContainsDefaultValues(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertSame('3', $crawler->filter('input[name="int1"]')->attr('value'));
        self::assertSame('5', $crawler->filter('input[name="int2"]')->attr('value'));
        self::assertSame('30', $crawler->filter('input[name="limit"]')->attr('value'));
        self::assertSame('fizz', $crawler->filter('input[name="str1"]')->attr('value'));
        self::assertSame('buzz', $crawler->filter('input[name="str2"]')->attr('value'));
    }

    public function testNavigationLinksReachTheirApiEndpoints(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/docs', server: ['HTTP_ACCEPT' => 'text/html']);
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'text/html; charset=UTF-8');

        $client->request('GET', '/api/fizzbuzz', [
            'int1' => 3,
            'int2' => 5,
            'limit' => 15,
            'str1' => 'fizz',
            'str2' => 'buzz',
        ], server: ['HTTP_ACCEPT' => 'application/json']);
        self::assertResponseIsSuccessful();

        $client->request('GET', '/api/statistics/most-frequent', server: ['HTTP_ACCEPT' => 'application/json']);
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
    }

    public function testApiSubmitReturnsExpectedCustomSequence(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/fizzbuzz', [
            'int1' => 2,
            'int2' => 3,
            'limit' => 6,
            'str1' => 'pair',
            'str2' => 'triple',
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
        self::assertSame(
            ['1', 'pair', 'triple', 'pair', '5', 'pairtriple'],
            json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR),
        );
    }

    public function testApiSubmitRejectsMissingValues(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/fizzbuzz');

        self::assertResponseStatusCodeSame(400);
        self::assertResponseHeaderSame('content-type', 'text/plain; charset=UTF-8');
        self::assertSame(
            'Veuillez renseigner les paramètres suivants : int1, int2, limit, str1, str2.',
            $client->getResponse()->getContent(),
        );
    }
}
