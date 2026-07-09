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
```

Set Laravel's sender address separately:

```dotenv
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Laravel discovers the package automatically, so no configuration files need
to be changed. `SDP_EMAIL_ENDPOINT` and `SDP_EMAIL_TIMEOUT` are optional.

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

## Development

```bash
composer install
composer test
```
