<?php

namespace Clonable\Views;

use Clonable\Helpers\Functions;
use Clonable\Helpers\Html;
use Clonable\Models\ClonedSite;
use Clonable\Models\Settings;
use Clonable\Models\Site;
use Clonable\Services\AllowedHostsService;
use Clonable\Services\ApiService;
use Clonable\Services\LanguageSwitcherService;
use Clonable\Services\LanguageTagService;
use Clonable\Traits\Forms;
use Clonable\Helpers\Locales;
use LocaleService;

class SettingsView implements ViewInterface {
    use Forms;

    public function render() {
        Html::include_alpine();
        $this->render_fields(Settings::PAGE, Settings::$fields, 'Miscellaneous settings');
        $this->render_form(Settings::PAGE);
    }

    public function description() {
        echo "These settings control more advanced features/settings in the plugin.";
    }

    public function max_proxy_timeout_field(){
        $option = get_option('clonable_max_proxy_timeout', 15);
        echo "<input name='clonable_max_proxy_timeout' type='number' value='$option'/>";
        echo " seconds";
    }

    public function allowed_hosts_enabled_field() {
        $option = get_option('clonable_allowed_hosts_enabled', 'on');
        $this->create_checkbox('clonable_allowed_hosts_enabled', $option);
        echo "(Add the Clonable clone domains the the allowed redirects hosts, so the WordPress redirect functions work correctly.)";
    }

    public function subfolder_service_enabled_field() {
        $option = get_option('clonable_subfolder_service_enabled', 'on');
        $this->create_checkbox('clonable_subfolder_service_enabled', $option);
        echo "(Sets up the proxy communication between the subfolder clones and WordPress.)";
    }

    public function locale_service_enabled_field() {
        $option = get_option('clonable_locale_service_enabled', 'on');
        $this->create_checkbox('clonable_locale_service_enabled', $option);
        echo "(Sets the WordPress locale to the locale of the active clone.)";
    }

    public function language_tag_service_enabled_field() {
        $option = get_option('clonable_language_tag_service_enabled', 'on');
        $this->create_checkbox('clonable_language_tag_service_enabled', $option);
        echo "(Add the language tags of the original website and the clones to the header of the page.)";
    }

    public function max_upstream_requests_field() {
        $option = max(2, intval(get_option('clonable_max_upstream_requests', 4)));
        echo "<input name='clonable_max_upstream_requests' min='2' max='200' type='number' value='$option'/>";
        echo "";
    }

    public function max_upstream_queued_field() {
        $option = max(0, intval(get_option('clonable_max_upstream_queued', 2)));
        echo "<input name='clonable_max_upstream_queued' min='0' max='200' type='number' value='$option'/>";
        echo "";
    }
}