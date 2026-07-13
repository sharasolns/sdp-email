# Laravel SDP Email

Send Laravel mailables through the SDP Email HTTP API. The package adds an
`sdp` Laravel mail transport and is auto-discovered after installation.

## Install

```bash
composer require sharasolns/sdp-email
```

In the application's `.env` file, change the existing `MAIL_MAILER` value to
`sdp`:

```dotenv
MAIL_MAILER=sdp
```

Then add your SDP Email API key:

```dotenv
SDP_EMAIL_KEY=sdp_your_api_key
```

Check the existing `MAIL_FROM_ADDRESS` value and make sure it uses a domain
that is enabled for outbound sending in SDP Email. You do not need to add
another sender setting if it is already correct.

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
