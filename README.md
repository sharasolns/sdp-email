# Laravel SDP Email

Send Laravel mailables through the SDP Email HTTP API. The package adds an
`sdp` Laravel mail transport and is auto-discovered after installation.

## Install

```bash
composer require iankibet/laravel-sdp-email
```

Configure the application:

```dotenv
MAIL_MAILER=sdp
SDP_EMAIL_API_KEY=sdp_your_api_key
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

The sending domain must be registered in SDP Email, enabled for outbound
sending, and allowed by the API key.

Use Laravel Mail normally:

```php
use Illuminate\Support\Facades\Mail;

Mail::raw('Sent through SDP Email.', function ($message) {
    $message->to('person@example.net')->subject('Hello');
});
```

Mailables, queued mail, HTML and text bodies, CC, BCC, reply-to addresses,
custom headers, and file attachments are supported.

## Configuration

The production API endpoint is configured automatically. To publish the
optional configuration file:

```bash
php artisan vendor:publish --tag=sdp-email-config
```

Available environment variables:

```dotenv
SDP_EMAIL_API_KEY=
SDP_EMAIL_ENDPOINT=https://email.sdp-platform.com
SDP_EMAIL_TIMEOUT=10
```

Each mailer may override package settings in `config/mail.php`:

```php
'mailers' => [
    'sdp' => [
        'transport' => 'sdp',
        'api_key' => env('SDP_EMAIL_API_KEY'),
        'endpoint' => env('SDP_EMAIL_ENDPOINT', 'https://email.sdp-platform.com'),
        'timeout' => 10,
    ],
],
```

## Development

```bash
composer install
composer test
```
