# Changelog

## 1.0.0 (2026-10-21)

First release by the new maintainers.

- Updated: The SaferPay admin tab shows an icon (`Icon=bank` in
  `settings/menu.ini.append.php`), drawn by the admin4 design.
- Fixed: `settings/saferpay.ini.append.php` is UTF-8, as it declares.
- Fixed: `MerchantNotifyEmail` is empty by default; it used to send merchant
  notifications to the original developer's address.
- Fixed: `MenuFontColor` is empty by default; it used to send the value `=`.
- Added: `composer.json`; the package is published as `se7enxweb/nxc_saferpay`
  and requires PHP 8.0 or later with the curl and dom extensions.
- Updated: `ezinfo.php` (class `nxc_saferpayInfo`, as the about page expects)
  and the new `extension.xml` state the version, license status and website.
- Updated: Modern README with installation, configuration and examples.

## 1.0 (2010)

- Added: Saferpay Hosting payments (CreatePayInit, VerifyPayConfirm,
  PayComplete), result pages and the transaction list in the administration.
