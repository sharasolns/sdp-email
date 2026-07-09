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
        $this->mergeConfigFrom(__DIR__.'/../config/sdp-email.php', 'sdp-email');

        if ($this->app->make('config')->get('mail.mailers.sdp') === null) {
            $this->app->make('config')->set('mail.mailers.sdp', [
                'transport' => 'sdp',
            ]);
        }
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/sdp-email.php' => config_path('sdp-email.php'),
        ], 'sdp-email-config');

        Mail::extend('sdp', function (array $config = []): SdpTransport {
            $apiKey = (string) ($config['api_key'] ?? config('sdp-email.api_key', ''));

            if ($apiKey === '') {
                throw new InvalidArgumentException(
                    'The SDP Email API key is missing. Set SDP_EMAIL_API_KEY in your environment.',
                );
            }

            return new SdpTransport(
                apiKey: $apiKey,
                endpoint: (string) ($config['endpoint'] ?? config('sdp-email.endpoint')),
                timeout: (float) ($config['timeout'] ?? config('sdp-email.timeout', 10)),
            );
        });
    }
}
