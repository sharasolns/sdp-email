<?php

namespace Sdp\Email\Transport;

use JsonException;
use Stringable;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SdpTransport extends AbstractTransport implements Stringable
{
    private const RESERVED_HEADERS = [
        'from',
        'to',
        'cc',
        'bcc',
        'reply-to',
        'sender',
        'subject',
        'content-type',
        'mime-version',
        'content-transfer-encoding',
    ];

    private readonly HttpClientInterface $client;

    public function __construct(
        private readonly string $apiKey,
        private readonly string $endpoint = 'https://email.sdp-platform.com',
        float $timeout = 10,
        ?HttpClientInterface $client = null,
    ) {
        parent::__construct();

        $this->client = $client ?? HttpClient::create([
            'timeout' => $timeout,
        ]);
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        $envelope = $message->getEnvelope();

        try {
            $response = $this->client->request('POST', rtrim($this->endpoint, '/').'/emails', [
                'auth_bearer' => $this->apiKey,
                'json' => $this->payload($email, $envelope),
            ]);

            $status = $response->getStatusCode();
            $result = $response->toArray(false);

            if ($status < 200 || $status >= 300) {
                $reason = $result['message'] ?? $result['error'] ?? "HTTP {$status}";

                throw new TransportException("SDP Email API request failed: {$reason}", $status);
            }

            if (isset($result['id'])) {
                $email->getHeaders()->addTextHeader('X-SDP-Email-ID', (string) $result['id']);
            }
        } catch (TransportException $exception) {
            throw $exception;
        } catch (ExceptionInterface|JsonException $exception) {
            throw new TransportException(
                'SDP Email API request failed: '.$exception->getMessage(),
                (int) $exception->getCode(),
                $exception,
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Email $email, Envelope $envelope): array
    {
        $attachments = [];

        foreach ($email->getAttachments() as $attachment) {
            $headers = $attachment->getPreparedHeaders();

            $attachments[] = [
                'filename' => $attachment->getFilename() ?? 'attachment',
                'content' => str_replace(["\r", "\n"], '', $attachment->bodyToString()),
                'content_type' => $headers->get('Content-Type')?->getBodyAsString()
                    ?? 'application/octet-stream',
            ];
        }

        return array_filter([
            'from' => $envelope->getSender()->toString(),
            'to' => $this->stringifyAddresses($this->toRecipients($email, $envelope)),
            'cc' => $this->stringifyAddresses($email->getCc()),
            'bcc' => $this->stringifyAddresses($email->getBcc()),
            'reply_to' => $this->stringifyAddresses($email->getReplyTo()),
            'subject' => $email->getSubject() ?? '',
            'html' => $email->getHtmlBody(),
            'text' => $email->getTextBody(),
            'headers' => $this->customHeaders($email),
            'attachments' => $attachments,
        ], static fn (mixed $value): bool => $value !== null && $value !== []);
    }

    /**
     * Use the envelope recipients so Laravel's alwaysTo feature is respected,
     * while keeping CC and BCC in their dedicated API fields.
     *
     * @return array<int, Address>
     */
    private function toRecipients(Email $email, Envelope $envelope): array
    {
        $copied = array_merge($email->getCc(), $email->getBcc());

        return array_values(array_filter(
            $envelope->getRecipients(),
            static fn (Address $address): bool => ! in_array($address, $copied, true),
        ));
    }

    /**
     * @return array<string, string>
     */
    private function customHeaders(Email $email): array
    {
        $headers = [];

        foreach ($email->getHeaders()->all() as $header) {
            if (in_array(strtolower($header->getName()), self::RESERVED_HEADERS, true)) {
                continue;
            }

            $headers[$header->getName()] = $header->getBodyAsString();
        }

        return $headers;
    }

    public function __toString(): string
    {
        return 'sdp';
    }
}
