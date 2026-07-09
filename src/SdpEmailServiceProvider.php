<?php

namespace Sdp\Email;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Sdp\Email\Transport\SdpTransport;

class SdpEmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/services.php', 'services');

        if ($this->app->make('config')->get('mail.mailers.sdp') === null) {
            $this->app->make('config')->set('mail.mailers.sdp', [
                'transport' => 'sdp',
            ]);
        }
    }

    public function boot(): void
    {
        Mail::extend('sdp', function (array $config = []): SdpTransport {
            $apiKey = (string) (
                $config['key']
                ?? $config['api_key']
                ?? config('services.sdp.key', '')
            );

            if ($apiKey === '') {
                throw new InvalidArgumentException(
                    'The SDP Email API key is missing. Set SDP_EMAIL_KEY in your environment.',
                );
            }

            return new SdpTransport(
                apiKey: $apiKey,
                endpoint: (string) (
                    $config['endpoint']
                    ?? config('services.sdp.endpoint', 'https://email.sdp-platform.com')
                ),
                timeout: (float) ($config['timeout'] ?? config('services.sdp.timeout', 10)),
            );
        });
    }
}
