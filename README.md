# NXC SaferPay Payment Gateway (nxc_saferpay)

An Exponential extension that takes card payments through the **Saferpay
Hosting interface** of SIX Payment Services. It is a building block for
developers who connect a shop to Saferpay:

- it creates a Saferpay payment link for an amount and sends the customer to
  Saferpay's payment page;
- when Saferpay notifies the site, it verifies the payment, captures the amount
  and approves the shop order's payment, so the order's workflow continues;
- it shows the customer a result page when they come back (or redirects to the
  order);
- it records every transaction, its status changes and every request in the
  database and in `var/log/saferpay.log`, and lists them in a **SaferPay** tab
  in the administration interface (drawn with a bank icon in the admin4 design).

The extension does **not** include a shop payment workflow event: you start a
payment from your own PHP code, as shown under [Usage](#usage).

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Permissions](#permissions)
- [Logging](#logging)
- [Upgrading](#upgrading)
- [Known limits](#known-limits)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits and history](#credits-and-history)

## Requirements

- Exponential 6.x (the legacy kernel) on PHP 8.0 or later
- The PHP extensions `curl` and `dom`
- MySQL or MariaDB (the table definitions are shipped for MySQL only)
- A Saferpay merchant account that offers the Saferpay Hosting interface
  (`CreatePayInit`, `VerifyPayConfirm`, `PayComplete`). The interface
  description from SIX is in `docs/Saferpay Hosting V1.5.9(EN).pdf`; check
  with SIX Payment Services that your contract still offers it.
- Optional: the NXC MooTools extension (`nxc_mootools`). When MooTools is
  loaded, the transaction list gets page navigation; without it the list shows
  all transactions on one page.

## Installation

### With Composer

From the root of your Exponential installation:

```bash
composer require se7enxweb/nxc_saferpay
```

The Exponential legacy installer places the extension in
`extension/nxc_saferpay`.

### By hand

1. Download the release archive from
   <https://github.com/se7enxweb/nxc_saferpay/releases>, or clone it:

   ```bash
   git clone https://github.com/se7enxweb/nxc_saferpay.git extension/nxc_saferpay
   ```

   The directory must be called `extension/nxc_saferpay`.

2. Activate the extension in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=nxc_saferpay
   ```

### After either way

1. Create the two tables. **The script drops the tables
   `nxc_saferpay_transactions` and `nxc_saferpay_log_messages` first**, so run
   it only once, on a database that has no transactions you want to keep:

   ```bash
   mysql -u <user> -p <database> < extension/nxc_saferpay/docs/mysql/install.sql
   ```

2. Regenerate the autoload array and clear the caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

3. Configure your account (next section). Then reload the administration
   interface: the **SaferPay** tab appears in the top menu.

## Configuration

The extension reads `saferpay.ini`. Do not edit the copy in
`extension/nxc_saferpay/settings/`; put your values in
`settings/override/saferpay.ini.append.php`. The shipped defaults point at
Saferpay's public **test account** (`99867-94913159`), so a fresh installation
takes test payments only.

```ini
<?php /* #?ini charset="utf-8"?

[Gateway]
URL=https://www.saferpay.com/hosting/

[LocalShopSettings]
GatewayType=curl
AccountId=12345-67890123
Currency=CHF
RequestCardVerificationNumber=yes
RequestCardholderName=yes
Delivery=no
MerchantNotifyEmail=shop@example.com
UserNotifyEmail=yes
Autoclose=no
ProviderSet=no
Language=de
SuccessUrl=/saferpay/success
BackUrl=/saferpay/back
FailUrl=/saferpay/fail
NotifyUrl=/saferpay/notify

[VirtualTerminalStyling]
ShowLanguages=yes
HeadColor=#003366
HeadFontColor=#FFFFFF

*/ ?>
```

### [Gateway]

| Setting | Default | What it does |
|---|---|---|
| `URL` | `https://www.saferpay.com/hosting/` | Base URL of the Saferpay Hosting interface; the method name and `.asp` are appended |

### [LocalShopSettings]

These are the defaults of every new transaction. Your code can override each
of them per transaction (see [Usage](#usage)).

| Setting | Default | What it does |
|---|---|---|
| `GatewayType` | `curl` | `curl` calls Saferpay over HTTPS. `file` only writes the request to the log and contacts nobody (a dry run) |
| `AccountId` | `99867-94913159` | Your Saferpay account number. For accounts starting with `99867-` (the test accounts) the test password is sent with the capture request |
| `Currency` | `EUR` | One of `EUR`, `USD`, `GBP`, `CHF`, `JPY`; any other value is replaced by `EUR` |
| `RequestCardVerificationNumber` | `yes` | Ask the customer for the card verification code (CVC) |
| `RequestCardholderName` | `yes` | Ask for the cardholder's name |
| `Delivery` | `no` | `yes` shows a delivery address form on the payment page |
| `MerchantNotifyEmail` | empty | Saferpay mails this address after each successful purchase; empty sends nothing |
| `UserNotifyEmail` | `yes` | `yes` sends Saferpay's confirmation to the email address of the signed-in user |
| `Autoclose` | `no` | Seconds after which the payment page returns to the success URL by itself; `no` keeps Saferpay's default |
| `ProviderSet` | `no` | Comma-separated Saferpay provider IDs to limit the payment methods; `no` offers all |
| `Language` | `en` | `en`, `de`, `fr` or `it`; any other value is replaced by `en` |
| `SuccessUrl` | `/saferpay/success` | Where the customer lands after a successful payment |
| `BackUrl` | `/saferpay/back` | Where the customer lands after canceling |
| `FailUrl` | `/saferpay/fail` | Where the customer lands when the payment failed |
| `NotifyUrl` | `/saferpay/notify` | The address Saferpay calls, server to server, with the payment result |

The extension appends `/<transaction ID>` to the four URLs and puts the site's
address in front of them.

### [VirtualTerminalStyling]

Colors and the font of Saferpay's payment page. Empty values are not sent.

| Setting | What it does |
|---|---|
| `ShowLanguages` | `no` hides the language selector on the payment page (default `yes`) |
| `BodyColor`, `HeadColor`, `HeadlineColor`, `MenuColor` | Background colors |
| `BodyFontColor`, `HeadFontColor`, `MenuFontColor` | Font colors |
| `Font` | Font face |

### Settings the extension adds for you

- `menu.ini`: the **SaferPay** top tab (`Icon=bank`), opening `saferpay/transactions`
- `module.ini`: the `saferpay` module
- `site.ini`: `saferpay/success`, `saferpay/back`, `saferpay/fail` and
  `saferpay/notify` in `[RoleSettings] PolicyOmitList[]`, so customers and
  Saferpay's server can reach them without a policy

## Usage

### 1. Start a payment from your code

Create a transaction, store it, and send the customer to its payment URL. Every
field you leave out is taken from `saferpay.ini`.

```php
<?php
$order = eZOrder::fetch( $orderID );

$transaction = new nxcSaferPayTransaction( array(
    // In the currency's smallest unit, as Saferpay expects: 2450 = 24.50
    'amount'            => (int) round( $order->attribute( 'total_inc_vat' ) * 100 ),
    'order_id'          => $order->attribute( 'id' ),
    'order_description' => 'Order ' . $order->attribute( 'id' ),
    // Optional overrides of the saferpay.ini defaults:
    'currency'          => 'CHF',
    'language'          => 'de',
    // Optional: any data of your own, stored with the transaction
    'extra_data'        => serialize( array( 'basket' => 17 ) ),
) );
$transaction->store();

$paymentURL = $transaction->attribute( 'pay_url' );
if ( $paymentURL )
{
    eZHTTPTool::redirect( $paymentURL );
    eZExecution::cleanExit();
}
// false: Saferpay refused the request; the reason is in the transaction's log.
```

Reading `pay_url` calls Saferpay's `CreatePayInit` once and remembers the
link. To keep a second account (for example a second shop), copy the settings
into another INI file and pass `'settings_file' => 'myshop.ini'`.

### 2. What happens next

1. The customer pays on Saferpay's page.
2. Saferpay calls `saferpay/notify/<transaction ID>`. The extension verifies
   the signed result (`VerifyPayConfirm`), checks that account, amount and
   currency match the transaction, captures the amount (`PayComplete`), and
   then, when a shop payment object exists for the order
   (`eZPaymentObject::fetchByOrderID()`), approves it and continues the
   order's workflow.
3. The customer returns to:
   - `saferpay/success/<ID>`: redirected to `shop/orderview/<order ID>` when the
     order exists, else the `saferpay/success.tpl` page. A transaction that
     was not captured gives a "not found" page.
   - `saferpay/back/<ID>`: canceled; the page offers the payment link again.
   - `saferpay/fail/<ID>`: failed; the page offers the payment link again.

The pages use the templates in `design/standard/templates/saferpay/`; override
them in your site design to match it.

### 3. Follow transactions in the administration

Click the **SaferPay** tab (or open `saferpay/transactions`). The list shows each
transaction's ID, date, order, customer, customer IP and status. The details
page (`saferpay/details/<ID>`) shows all fields sent to and received from
Saferpay, a link to the order, and the transaction's log messages.

A transaction passes these statuses: Object created, Object stored, Redirected
to payment URL, then Canceled by user, Authorization denied/failed, Payment
confirmed, Payment confirmation failed, Amount captured or Capture failed.

### Trying it out

`saferpay/test` creates a test transaction of 10.00 for order ID 1 and sends you
to the payment page. The default settings use Saferpay's test account; SIX's
notes on test logins are in `docs/Saferpay Testlogin+Passwort (en) SIX.pdf`.
The notify call
has to reach your site from the internet, so test on a public address.

## Permissions

The `saferpay` module has two functions:

| Function | Views | Give it to |
|---|---|---|
| `admin` | `transactions`, `details` | Staff who follow payments |
| `pay` | `test` (and `success`, `back`, `fail`, `notify`, which are also open to everybody through `PolicyOmitList`) | Developers who try the setup; do not give `saferpay/pay` to anonymous users on a live shop, since `saferpay/test` creates transactions |

Add the policies in Roles and policies, module **saferpay**.

## Logging

Every status change and every request to Saferpay is written to
`var/log/saferpay.log` and, once the transaction has an ID, stored in
`nxc_saferpay_log_messages`, shown on the transaction's details page.

## Upgrading

This is the first release by the new maintainers. If you used the extension
before, replace the directory (or require the package), keep your tables, then
regenerate the autoloads and clear the caches. Check your `MerchantNotifyEmail`:
the old default sent merchant mail to the original developer's address, and it
is now empty.

## Known limits

- No shop workflow event or payment gateway class is included; your code
  starts the payment (see [Usage](#usage)).
- The tables are MyISAM and the SQL is for MySQL/MariaDB only.
- The customer's IP is stored as an IPv4 number; IPv6 addresses are not stored.
- The callback URLs are built from the site's address without a siteaccess
  in the path. With URL-based siteaccesses, include it in the four URL
  settings (for example `SuccessUrl=/shop/saferpay/success`).
- The curl gateway does not verify Saferpay's TLS certificate.
- The admin pages use the box layout of the classic admin design.
- No translations are included; texts are in English.
- `settings.php` and `autoload/autoload.php` are used only by NXC's own
  extension manager; Exponential ignores them.

## Troubleshooting

- **No SaferPay tab**: check that the extension is in `ActiveExtensions[]`,
  clear the caches, and make sure your role has the `saferpay/admin` policy.
- **`pay_url` is false**: Saferpay answered with an error. Open the transaction
  in the SaferPay tab or read `var/log/saferpay.log`; the line
  `CreatePayInit failed: ...` has Saferpay's message. A wrong `AccountId` or a
  currency your account does not accept are the usual reasons.
- **The order is never approved**: Saferpay could not reach
  `saferpay/notify/<ID>` (the site must be reachable from the internet), or no
  shop payment object exists for the order. The log shows
  `Notification is skipped` when the transaction was not waiting for a payment.
- **Database error on the first payment**: the tables are missing; run
  `docs/mysql/install.sql`.

## License

The original authors published this extension without a license statement,
and none of its files states one. Until the copyright holders publish one, ask
them before you redistribute changed copies. The maintainers will add a license
file as soon as one is stated.

## Credits and history

- 2010: written by Serhey Dolgushev at NXC for eZ Publish 4 and published as
  `nxc/nxc_saferpay`.
- Since 2026: maintained by 7x for Exponential as `se7enxweb/nxc_saferpay`.

eZ Publish, the system this extension was first written for, was developed by
eZ Systems and was discontinued by its creator after about 18 years; Exponential
continues its legacy kernel.
