<?php

namespace Clonable\Views;

use Clonable\Helpers\Functions;
use Clonable\Helpers\Html;
use Clonable\Models\ClonedSite;
use Clonable\Models\Site;
use Clonable\Services\ApiService;
use Clonable\Traits\Forms;
use Clonable\Helpers\Locales;

class DashboardView implements ViewInterface {
    use Forms;

    public function render() {
        Html::include_fomantic_dropdown();
        Html::include_alpine();
        Html::include_css("clonable-thumbnails.css");
        Html::include_css("clonable-extra-button.css");

        $cached_site = get_option("clonable_site");
        // if the user has not entered an api key, then show the registration form
        if (empty($cached_site)) {
            Html::include_cdn("DoH.js", "https://cdn.jsdelivr.net/npm/dohjs@latest/dist/doh.min.js");
            Html::include_js('dohjs.js');

            $this->render_no_site_section();
        } else {
            $site = new Site($cached_site);
            $this->render_site_cards($site);
        }
    }

    public function description() {
        //ignore
    }

    public function site_description() {
        echo "<p>We have detected the following settings on your website, please confirm these settings and create your site inside Clonable.</p>";
    }

    public function site_domain_field() {
        if (isset($_SERVER)) {
            $server_data = wp_unslash($_SERVER);
        }
        $domain = ($server_data['SERVER_NAME'] ?? '');
        echo "<input name='clonable_site_domain' value='" . esc_attr($domain) . "' id='clonable_site_domain'/>";
    }

    public function site_locale_field() {
        $locale = get_language_attributes();
        if (preg_match('~^lang="([a-zA-Z-_]*)"$~', $locale, $matches)) {
            $value = str_replace('-', '_', $matches[1]); // get the first match group
        } else {
            $value = 'en_gb';
        }
        ?>
        <div class="field">
            <div class="ui search selection dropdown">
                <input type="hidden" name="clonable_site_locale" value="<?php echo esc_attr($value); ?>">
                <i class="dropdown icon"></i>
                <div class="default text mt-0.5"></div>
                <div class="menu">
                    <?php
                    foreach (ApiService::get_locales() as $language) {
                        echo "<div class='item' data-value='" . esc_attr($language['locale']) . "'><i class='fflag-" . esc_attr($language['region']) . " fflag ff-sm' style='margin-right: 0.5rem; margin-bottom: 0.125rem'></i>" . esc_html($language['display_name']) . "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        $this->render_error('clonable_site_locale');
    }

    public function site_preferred_domain_field() {
        if (isset($_SERVER)) {
            $server_data = wp_unslash($_SERVER);
        }
        $domain = ($server_data['SERVER_NAME'] ?? '');
        $prefers_www = Functions::str_starts_with($domain, 'www.') ? 'checked' : '';
        echo "<input name='clonable_site_preferred_domain' id='clonable_site_preferred_domain' type='checkbox' " . esc_attr($prefers_www) . "/>";
    }

    public function site_origin_field() {
        echo "<input name='clonable_site_origin' id='clonable_site_origin'/>";
        $this->render_error('clonable_site_origin');
    }

    private function render_no_site_section() {
        ?>
            <div style="display: flex">
                <div class="card" style="margin-right: 4px;">
                    <?php
                        $this->render_fields(Site::PAGE, Site::$fields, 'Connect to clonable', 'site_description');
                        $this->render_form(Site::PAGE);
                        $this->render_api_key_button();
                    ?>
                </div>
            </div>
        <?php
    }

    public function render_site_cards($site) {
        $last_synced = get_option('clonable_last_sync');
        $file = plugin_dir_url(__DIR__) . "images/configure-wordpress-thumbnail.png";
        ?>
            <div class="row">
                <div class="card">
                    <h2 class="">Your site is successfully connected to clonable!</h2>
                    <p>Last checked: <?php echo esc_html($last_synced); ?></p>
                    <div style="display: flex;">
                        <a href="https://app.clonable.net/sites/<?php echo esc_attr($site->get_id()); ?>/settings" target="_blank" class="button button-secondary">
                            View your settings in Clonable
                        </a>
                        <form style="margin-left: 4px" method="POST">
                            <?php settings_fields('clonable_options'); ?>
                            <input type="hidden" name="clonable-sync" value="1">
                            <button type="submit" class="button button-secondary">
                                <span class="dashicons dashicons-update" style="line-height: 1.5"></span>
                                Sync with Clonable
                            </button>
                        </form>
                    </div>
                    <?php $this->render_api_key_button(); ?>
                </div>
                <div class="thumbnail-container-inline">
                    <a href="https://youtu.be/7FftYudccf4" target="_blank">
                        <img src="<?php echo $file ?>" class="thumbnail smaller"
                             alt="Configure the language switcher thumbnail"/>
                    </a>
                </div>
            </div>

            <h2>Your clones</h2>
            <div style="display: grid;	grid-template-columns: repeat(4, minmax(0, 1fr));">
                <?php
                foreach ($site->get_clones() as $index => $clone) {
                    $region = Locales::get_region($clone["lang_code"]);
                    ?>
                        <div class="card" style="margin-right: 4px; margin-top: 0; margin-bottom: 4px" x-data>
                            <a href="<?php echo esc_attr($site->get_clone_url($index)); ?>" target="_blank" style="text-decoration: none; box-shadow: none">
                                <span style="display: flex; justify-content: space-between">
                                    <?php echo esc_html($site->get_clone_url($index)); ?>
                                    <div class="dashicons dashicons-external" aria-hidden="true" x-tooltip="'View clone in new tab'"></div>
                                </span>
                            </a>
                            <i class='fflag-<?php echo esc_attr($region); ?> fflag ff-lg' style='margin-top: 16px'></i>
                            <h3><?php echo esc_html(Locales::get_display_name($clone["lang_code"])); ?></h3>
                            <div style="display: flex; justify-content: space-between; align-items: center">
                                <a href="https://app.clonable.net/sites/<?php echo esc_attr($site->get_id()); ?>/clones/<?php echo esc_attr($clone["id"]); ?>/editor/choice"
                                    target="_blank" class="button button-primary">
                                    <span class="dashicons dashicons-edit-large" style="line-height: 1.3"></span>
                                    Edit in Clonable
                                </a>
                                <?php
                                if ($clone['plugin_connected']) {
                                    ?>
                                        <div x-tooltip="'This clone has been connected to Clonable'">
                                            <span class="dashicons dashicons-admin-links"></span>
                                        </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php
                }
                echo "</div>";

        // new clone card
        echo "<div class='card' style='max-width: 600px'>";
        $this->render_fields(ClonedSite::PAGE, ClonedSite::$fields, 'Create a new clone');
        $this->render_form(ClonedSite::PAGE);
        echo "</div>";
        // clone cards

    }

    public function clone_locale_field() {
        ?>
        <div class="field">
            <div class="ui search selection dropdown">
                <input type="hidden" name="clonable_clone_locale" value="de_DE">
                <i class="dropdown icon"></i>
                <div class="default text mt-0.5"></div>
                <div class="menu">
                    <?php
                    foreach (ApiService::get_locales() as $language) {
                        echo "<div class='item' data-value='" . esc_attr($language['locale']) . "'><i class='fflag-" . esc_attr($language['region']) . " fflag ff-sm' style='margin-right: 0.5rem; margin-bottom: 0.125rem'></i>" . esc_html($language['display_name']) . "</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
        $this->render_error('clonable_clone_locale');
    }

    public function clone_subfolder_field() {
        ?>
            <div style="display: flex; align-items: center">
                <span style="background-color: #e7e7e7; border: 1px solid #8c8f94; border-radius: 4px 0 0 4px; padding: 0 8px; line-height: 2"><?php echo esc_html(Functions::get_root_domain() . '/'); ?></span>
                <input placeholder="..." type="text" name="clonable_clone_subfolder"
                    style="max-width: 75px; margin-left: 0; border-left: none; border-radius: 0 4px 4px 0"/>
            </div>
        <?php
    }

    public function render_api_key_button() {
        ?>
            <form method="POST" style="margin-top: 4px;" onsubmit="return confirm('Are your sure you want to disconnect Clonable? This will remove all the WordPress settings related to the Clonable plugin.');">
                <?php settings_fields('clonable_options'); ?>
                <input type="hidden" name="clonable_logout" value="1">
                <button type="submit" class="button button-danger">
                    Disconnect from Clonable
                </button>
            </form>
        <?php
    }
}