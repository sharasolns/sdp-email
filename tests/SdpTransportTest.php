<?php

namespace Sdp\Email\Tests;

use Sdp\Email\Transport\SdpTransport;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SdpTransportTest extends TestCase
{
    public function test_it_converts_a_symfony_email_to_the_sdp_api_payload(): void
    {
        $requests = [];
        $client = new MockHttpClient(function (string $method, string $url, array $options) use (&$requests) {
            $requests[] = compact('method', 'url', 'options');

            return new MockResponse('{"id":"email-id"}', [
                'http_code' => 200,
                'response_headers' => ['content-type: application/json'],
            ]);
        });

        $email = (new Email)
            ->from(new Address('hello@example.com', 'Example'))
            ->to('person@example.net')
            ->cc('copy@example.net')
            ->replyTo('support@example.com')
            ->subject('Hello')
            ->html('<p>Hello</p>')
            ->text('Hello')
            ->attach('invoice contents', 'invoice.txt', 'text/plain');
        $email->getHeaders()->addTextHeader('X-Campaign', 'welcome');

        $transport = new SdpTransport('sdp_secret', 'https://email.example.test/', client: $client);
        $transport->send($email, Envelope::create($email));

        $this->assertSame('POST', $requests[0]['method']);
        $this->assertSame('https://email.example.test/emails', $requests[0]['url']);
        $this->assertSame(
            'Authorization: Bearer sdp_secret',
            $requests[0]['options']['normalized_headers']['authorization'][0],
        );

        $payload = json_decode($requests[0]['options']['body'], true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('"Example" <hello@example.com>', $payload['from']);
        $this->assertSame(['person@example.net'], $payload['to']);
        $this->assertSame(['copy@example.net'], $payload['cc']);
        $this->assertSame(['support@example.com'], $payload['reply_to']);
        $this->assertSame('welcome', $payload['headers']['X-Campaign']);
        $this->assertSame('invoice.txt', $payload['attachments'][0]['filename']);
        $this->assertSame('invoice contents', base64_decode($payload['attachments'][0]['content']));
    }

    public function test_service_provider_registers_the_sdp_mailer(): void
    {
        $this->assertSame(['transport' => 'sdp'], config('mail.mailers.sdp'));
    }
}
