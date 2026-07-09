# Laravel SDP Email

Send Laravel mailables through the SDP Email HTTP API. The package adds an
`sdp` Laravel mail transport and is auto-discovered after installation.

## Install

```bash
composer require sharasolns/sdp-email
```

Configure the application:

```dotenv
MAIL_MAILER=sdp
SDP_EMAIL_KEY=sdp_your_api_key
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

Like Laravel's Mailgun integration, the mailer belongs in `config/mail.php`
and its credentials belong in `config/services.php`. Package discovery adds
both defaults automatically, so most applications only need these environment
variables:

```dotenv
SDP_EMAIL_KEY=
SDP_EMAIL_ENDPOINT=https://email.sdp-platform.com
SDP_EMAIL_TIMEOUT=10
```

If you keep explicit configuration in your application, use:

```php
// config/services.php
'sdp' => [
    'key' => env('SDP_EMAIL_KEY'),
    'endpoint' => env('SDP_EMAIL_ENDPOINT', 'https://email.sdp-platform.com'),
    'timeout' => (float) env('SDP_EMAIL_TIMEOUT', 10),
],
```

```php
// config/mail.php
'mailers' => [
    'sdp' => [
        'transport' => 'sdp',
    ],
],
```

Mailer-level `key`, `endpoint`, and `timeout` values remain supported when
separate credentials are needed for a specific mailer.

## Development

```bash
composer install
composer test
```
