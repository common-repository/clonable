<?php

/*
Plugin Name: Clonable
Description: Official plugin for improving your clones made with Clonable.
Plugin URI: https://kb.clonable.net/en/introduction/getting-started/wordpress#de-clonable-plug-in-downloaden
Version: 2.2.6
Author: Clonable BV
Author URI: https://www.clonable.net
License: GPL v2 or later
Requires PHP: 7.0
Tested up to: 6.5.4
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

use Clonable\Bootstrap;
use Clonable\Services\AllowedHostsService;
use Clonable\Services\ClonableWooCommerceService;
use Clonable\Services\LanguageSwitcherService;
use Clonable\Services\LanguageTagService;
use Clonable\Services\ShortCodeService;
use Clonable\Services\SubfolderService;

// include traits
include_once "traits/Validation.php";
include_once "traits/Forms.php";
include_once "traits/WooCommerceCheck.php";

// include models
include_once "models/ClonableWooCommerce.php";
include_once "models/LanguageSwitcher.php";
include_once "models/LanguageTag.php";
include_once "models/Site.php";
include_once "models/ApiKey.php";
include_once "models/ClonedSite.php";
include_once "models/Settings.php";

// custom objects
include_once "objects/CurlBuilder.php";
include_once "objects/ClonableResponse.php";
include_once "objects/ApiResponse.php";
include_once "objects/ExcludedProductTerm.php";
include_once "objects/ClonableConfig.php";
include_once "objects/Notification.php";
include_once "objects/CircuitBreaker.php";

// include views
include_once "views/Layout.php";
include_once "views/ViewInterface.php";
include_once "views/DashboardView.php";
include_once "views/LanguageSwitcherView.php";
include_once "views/LanguageTagView.php";
include_once "views/WoocommerceView.php";
include_once "views/SettingsView.php";
include_once "views/OnboardingView.php";

// include controllers
include_once "controllers/Controller.php";
include_once "controllers/DashboardController.php";
include_once "controllers/WoocommerceController.php";
include_once "controllers/LanguageTagController.php";
include_once "controllers/LanguageSwitcherController.php";
include_once "controllers/SettingController.php";
include_once "controllers/LandingPageController.php";

// include helper classes
include_once "helpers/Locales.php";
include_once "helpers/Json.php";
include_once "helpers/Session.php";
include_once "helpers/Html.php";
include_once "helpers/Functions.php";

// middleware
include_once "middleware/MiddlewareHandler.php";
include_once "middleware/MiddlewareInterface.php";
include_once "middleware/ClonedSite.php";
include_once "middleware/WooCommerce.php";
include_once "middleware/Auth.php";

// include application classes
include_once "Bootstrap.php";
include_once "routes/Router.php";

// include services
include_once "services/LanguageSwitcherService.php";
include_once "services/ApiService.php";
include_once "services/SubfolderService.php";
include_once "services/SyncService.php";
include_once "services/LanguageTagService.php";
include_once "services/ClonableWooCommerceService.php";
include_once "services/AllowedHostsService.php";
include_once "services/LocaleService.php";
include_once "services/ShortCodeService.php";

// include service modules
include_once "services/modules/ExclusionModule.php";
include_once "services/modules/TaxonomyModule.php";
include_once "services/modules/DataPanelModule.php";
include_once "services/modules/ProductImporterModule.php";

define('CLONABLE_NAME', 'Clonable');
define('CLONABLE_VERSION', '2.2.6');

try {
    $clonable_plugin = new Bootstrap(CLONABLE_NAME, CLONABLE_VERSION);
    add_action('wp_head', array($clonable_plugin, 'verification'), 1);
} catch (Exception $exception) {
    error_log("[Clonable] Error while initializing plug-in: {$exception->getMessage()}");
    return;
}

try {
    $allowed_hosts_service = new AllowedHostsService();
} catch (Exception $exception) {
    error_log("[Clonable] Error setting allowed hosts: {$exception->getMessage()}");
}

try {
    $language_tag_service = new LanguageTagService();
    add_action('wp_head', array($language_tag_service, 'clonable_echo_language_tags'), 5);
    add_action('clonable_public_key_cron_hook', array($language_tag_service, 'clonable_get_public_key'), 10, 1);
    // Schedule cronjob for public key if it doesn't exist yet
    if(wp_next_scheduled("clonable_public_key_cron_hook", array(true)) === false) {
        wp_schedule_event(time(), 'daily', 'clonable_public_key_cron_hook', array(true));
    }
} catch (Exception $exception) {
    error_log("[Clonable] Error while setting up language tags: {$exception->getMessage()}");
}

try {
    // Add language switcher hook
    $language_switcher_service = new LanguageSwitcherService();
    add_action("wp_head", array($language_switcher_service, 'print_language_switcher'));
} catch (Exception $exception) {
    error_log("[Clonable] Error while setting up language switcher: {$exception->getMessage()}");
}


try {
    // Constructor automatically fixes the initialisation of the subfolders
    $subfolder_service = new SubfolderService();
} catch (Exception $exception) {
    error_log("[Clonable] Error while setting up Clonable subfolders: {$exception->getMessage()}");
}

try {
    // Instantiate all the services that are used for different kind of WooCommerce functionalities.
    $woocommerce_service = new ClonableWooCommerceService();
} catch (Exception $exception) {
    error_log("[Clonable] Error while setting up WooCommerce modules: {$exception->getMessage()}");
}

try {
    $locale_service = new LocaleService();
} catch (Exception $exception) {
    error_log("[Clonable] Error setting allowed hosts: {$exception->getMessage()}");
}