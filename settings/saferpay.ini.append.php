<?php /* #?ini charset="utf-8"?

[Gateway]
# Saferpay gateway URL
URL=https://www.saferpay.com/hosting/

[LocalShopSettings]
# Gateway type, possbile values are: "curl" or "file"
GatewayType=curl
# The merchant’s Saferpay account number. Example: 99867-94913159
AccountId=99867-94913159
# 3-digit currency code. Example: EUR, USD, GBP, CHF, JPY, etc
Currency=EUR
# Request the card verification number (CVV/CVC2). Possible values: "yes", "no"
RequestCardVerificationNumber=yes
# Request the cardholder or account holder. Possible values: "yes", "no"
RequestCardholderName=yes
# Must be set to "yes" or "no". If set to "yes" an input form for the customer
# delivery address appears during the Virtual Terminal session.
Delivery=no
# Email address of the merchant. MSaferpay sends a notification message after a
# successful purchase. Yo can leave this option empty to don`t send emails
MerchantNotifyEmail=
# Must be set to "yes" or "no". If set to "yes" saferpay sends a notification
# to the customer message after a successful purchase.
UserNotifyEmail=yes
# Number of seconds (0 to n), after which the counter is to be routed
# automatically to SUCCESSLINK. Set it "no" for use defaults
Autoclose=no
# Use this parameter to show the customer specific payment methods. It must
# contain a comma delimited list of provider id’s. A current list of provider
# id’s can be found here: http://www.saferpay.com/help/ProviderTable.asp. Set
# it "no" for use defaults
ProviderSet=no
# Specifies the language for the Virtual Terminal session. Possible values are
# "en" (English), "de" (German), "fr" (French) and "it" (Italian). Per default
# the Virtual Terminal uses the browsers language setting to determine the
# dialog language. A recent list of language codes is available at
# https://www.saferpay.com/vt/xml/language.xml.
Language=en
# View URL, which is to be called up upon successful authorization.
# Transaction`s ID will be transferred as first ordered param
SuccessUrl=/saferpay/success
# View URL, which is to be called up upon abort by the customer.
# Transaction`s ID will be transferred as first ordered param
BackUrl=/saferpay/back
# View URL that is to be called up if the payment cannot be carried out
# Transaction`s ID will be transferred as first ordered param
FailUrl=/saferpay/fail
# Saferpay sends the result of the a successful authorization or payment
# directly to this View (URL).
# Transaction`s ID will be transferred as first ordered param
NotifyUrl=/saferpay/notify

[VirtualTerminalStyling]
# If set to "no" this option disables the language selector in VT.
ShowLanguages=yes
# The color of the VT body.
BodyColor=
# The color of the VT head.
HeadColor=
# The color of the VT head-line.
HeadlineColor=
# The color of the menu bar background.
MenuColor=
# The font color of the body area.
BodyFontColor=
# The font color of the head.
HeadFontColor=
# The font color of the menu.
MenuFontColor=
# Defines the font-face used in the VT.
Font=
*/ ?>