=== Clonable - Translate Woocommerce / WordPress website. Multilingual in 5 minutes. ===
Contributors: clonable
Tags: translations, translate, multilingual, clonable, seo
Requires at least: 5.0
Tested up to: 6.6.2
Requires PHP: 7.2
Stable tag: 2.2.6
License: GPL v2 or later

Seamlessly translate and maintain your multilingual websites. Speed up and simplify your internationalisation with Clonable.

== Description ==
Online internationalisation without the hassle:  Speed up & simplify your translation processes. Your multilingual website updates automatically.

= The challenge =
Translating a WordPress / Woocommerce site can prove to be both costly and time-consuming. Beyond the initial translation effort, ongoing maintenance is essential to ensure the translated version remains current with new content. Consequently, translations are often overlooked or neglected following their initial creation, despite the substantial potential in foreign markets.

= Our solution =
Introducing Clonable, a groundbreaking solution for WordPress website owners. Clonable enables the effortless creation of "clones" of your WordPress websites, ensuring they are consistently synchronised with the original version. Any modifications made to the original site are instantly reflected in the clone, significantly reducing maintenance overhead. With Clonable, you can generate a website clone in just 5 minutes, drastically reducing time-to-market.

= Key features =
1. SEO Enhancement: Our plugin seamlessly adds language tags to the head section of all your pages. This ensures that your clones and the original site are appropriately linked for improved SEO performance. This functionality extends to both translated and non-translated clones and supports clones of subfolders as well.
2. Effortless Subfolder Integration: Clonable empowers you to effortlessly incorporate a clone into a subfolder of your website, eliminating the need for complex technical configurations within WordPress.
3. User-Friendly Language Switcher: Clonable also offers an intuitive language switcher, allowing users to effortlessly navigate between the different languages available on your website.
4. Support for Woocommerce for easier conversion tracking in different languages.

== Installation ==
You will need a [Clonable account](https://app.clonable.net/register) to make use of the plugin. When you have successfully connected the plugin to your Clonable account the settings will
automatically synchronise with your WordPress installation.

When settings have not synchronised correctly, you can do this manually by hitting the 'Sync with Clonable' button on the general settings page of the plugin.

== Changelog ==
v2.2.6
Bug fix: timeouts

v2.2.5
Improve circuit breaker logic

v2.2.4
Fix bug with incorrect saving location in language tags.

v2.2.3
Fixed bug with dropdown in language tag settings.

v2.2.2
Embedded videos into the plugin.
Added language-only options to the language tags

v2.2.1
Circuit breaker sensitivity improvements.

v2.2.0
Added support for WordPress 6.6 and bumped the minimum version for PHP.
Added circuit breaker for subfolder communication with Clonable.
Added settings window for enabling and disabling specific services.

v2.1.16
Better default values for Clonable options.
Added option to get the locale of the clones using the WordPress get_locale() function.
Bugfix: Disabled proxy loop for sites with multiple subfolders.

v2.1.15
Performance improvements for API communication

v2.1.14
Bugfix: Debounce algorithm fix
Bumped tested up to version

v2.1.13
Bugfix: Solved problem with Norwegian flag that could not be selected for the language switcher.

v2.1.12
Bug fix

v2.1.11
Support for site_url and home_url with a subdirectory.

v2.1.10
Performance improvements for the admin interface.

v2.1.9
Bugfix: Fixed mismatch in 'sync with Clonable' button for domains that use www. Added a notification system for better insights into background tasks. Improved stability for internal hooks.

v2.1.8
Bugfix: Read user data correctly when the content-type is multipart/form-data

v2.1.7
Bugfix: Fixed redirect behaviour for subfolder clones.

v2.1.6
Translation improvements for subfolder clones.

v2.1.5
Bugfixes and performance improvements.

v2.1.4
Bugfix: handle HTTP methods differently and fixed invalid content length header.

v2.1.3
Bugfix: edge case with the Mollie payment provider and domain-based clones causing indirect redirects

v2.1.2
Better support for error tracing with subfolder config errors.
Fixed formatting error.

v2.1.1
Bug fix

v2.1.0
Several bug fixes
Improved support for WooCommerce:
- Added product exclusions for WooCommerce products.
- Improved conversion tracking for subfolder clones.
- Clarified existing Analytics/WooCommerce modules.

v2.0.7
Bug fix in language tags setting screens in combination with some performance plug-ins

v2.0.6
Bug fix in setup
Updated to WordPress 6.4 compatibilty level

v2.0.5
Performance improvements.

v2.0.4
Fixed a bug for the input of the original site, where the input was not correctly sanitized.

v2.0.3
Fixed a bug where some function names could conflict with other plugins.

v2.0.2
Fixed several bugs in the language tag translations

v2.0.1
Fixed a bug where api keys error were not handled correctly during the site connection.
Added a button for disconnecting the Clonable plugin.

v2.0.0
New major version of the plugin.
- Added language switcher settings.
- Added connection to the control panel.
- Improved usability of the language tag settings.
- Added registration process for new users.
- Added automatic subfolder configuration for subfolder clones.

v1.3.0
Added option to turn off url translation for language tags.

v1.2.4:
Fixed a bug where an empty domain field would be seen as invalid input.

v1.2.3:
Fixed visitors not returning to the cart page when payment was cancelled while using Mollie.

v1.2.2:
Fix crash on some installations when trying to detect whether woocommerce is installed or not.

v1.2.1:
Add compatibility with Mollie Payment Gateway.

v1.2.0:
Add integration with Woocommerce for easier conversion tracking on cloned sites.

v1.1.2:
Improved cache hit rate and tweaked backoff algorithm.

v1.1.1:
Fix a crash when saving the settings

v1.1.0:
Use translated versions of url's in language tags

v1.0.2:
Fix compatibility with Wordpress 6.0
Fix incorrect language tags in some cases

v1.0.1:
Fix compatibility with Wordpress < 5.9

v1.0.0:
Initial release
