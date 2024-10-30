<?php

namespace Clonable\Views;

use Clonable\Helpers\Html;
use Clonable\Helpers\Locales;
use Clonable\Helpers\Session;
use Clonable\Models\LanguageSwitcher;
use Clonable\Traits\Forms;

class LanguageSwitcherView implements ViewInterface {
    use Forms;

    public function render() {
        Html::include_fomantic_dropdown();
        Html::include_alpine();
        Html::include_css("clonable-thumbnails.css");

        $this->render_fields(LanguageSwitcher::PAGE, LanguageSwitcher::$fields, 'Language switcher Settings');
        $this->render_form(LanguageSwitcher::PAGE);
    }

    public function description() {
        echo '<p style="max-width: 75%">For your convenience we provide an easy-to-use language switcher,
            you can use the options below to configure the language switcher to your liking.
            If you save the config, the can view the result immediately on the page. This is
            a simple implementation of the Clonable language switcher, you can view a more
            detailed version in the <a href="https://app.clonable.net/sites" target="_blank">Clonable dashboard</a></p>';
    }

    public function show_flag_field() {
        $this->create_checkbox('clonable_show_flag');
    }

    public function rounded_flag_field() {
        $this->create_checkbox('clonable_rounded_flag');
    }

    public function show_text_field() {
        $this->create_checkbox('clonable_show_text');
    }

    public function background_color_field() {
        $this->create_color_field('clonable_background_color', '#ffffff', "test");
    }

    public function background_hover_color_field() {
        $this->create_color_field('clonable_hover_background_color', '#efefef');
    }

    public function size_field() {
        $this->create_select('clonable_size', array(
            "sm" => "small",
            "md" => "medium",
            "lg" => "large",
        ));
    }

    public function position_field() {
        $this->create_select('clonable_position', array(
            "bottom-left" => "Bottom left",
            "bottom-right" => "Bottom right",
        ));
    }

    public function languages_field() {
        $option = Session::old('clonable_langswitch_data');
        if (isset($_SERVER)) {
            $server_data = wp_unslash($_SERVER);
        }
        $domain = ($server_data['SERVER_NAME'] ?? '');
        ?>
        <p>In the table below you can add your languages for the language switcher. There are a few rules for adding the languages:</p>
        <ul>
            <li>1: Domain names should always start with "https://".</li>
            <li>2: We recommend adding the current site to the language switcher (https://<?php echo esc_html($domain); ?>)</li>
            <li>3: The flag is the flag of the country you want the languge switcher to show.</li>
            <li>4: The text is the text of the language you want to show. For example: "de" or "german".</li>
        </ul>
        <div class="wrap">
            <div x-data="languageSwitcher(<?php echo esc_js($option); ?>)">
                <!-- hidden input field for submitting the json value -->
                <label for="clonable_langtag_data"></label>
                <input x-model="JSON.stringify(languages)" name="clonable_langswitch_data" id="clonable_langswitch_data" style="display: none"/>
                <!-- Display data -->
                <table class="wp-list-table fat striped" style="width: 75%">
                    <thead>
                        <tr>
                            <th>Flag</th>
                            <th>Text</th>
                            <th>Domain</th>
                            <th style="width: 25px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <template x-for="(language, index) in languages" :key="index">
                        <tr>
                            <td>
                                <div class="field">
                                    <div class="ui search selection dropdown" style="width: 75%">
                                        <input x-model="languages[index].clonableLocaleCode" type="hidden">
                                        <i class="dropdown icon"></i>
                                        <div class="default text mt-0.5"></div>
                                        <div class="menu">
                                            <?php
                                            foreach (Locales::filter_regions() as $language) {
                                                ?>
                                                    <div @click="selectFlag(index, '<?php echo esc_attr($language['locale']); ?>', '<?php echo esc_attr($language['display_country']); ?>')"
                                                         class="item" data-value="<?php echo esc_attr($language['locale']); ?>">
                                                        <i class="fflag-<?php echo esc_attr($language['region']); ?> fflag ff-sm" style="margin-right: 0.5rem; margin-bottom: 0.125rem"></i>
                                                        <?php echo esc_html($language['display_country']); ?>
                                                    </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><input x-model="languages[index].clonableDisplayLanguage" style="width: 100%" /></td>
                            <td><input x-model="languages[index].clonableUrl" style="width: 100%" /></td>
                            <td style="width: 25px">
                                <div style="display: flex; justify-content: space-around">
                                    <button type="button" style="display: flex; justify-content: center; align-items: center;" class="button" @click="removeRow(index)">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>

                <button type="button" class="button action" @click="addRow">Add another language</button>
            </div>
        </div>
        <?php
        $this->render_error('clonable_langswitch_data');
    }

    public function enable_language_switcher_field() {
        $file = plugin_dir_url(__DIR__) . "images/language-switcher-thumbnail.png";
        ?>
            <div class="thumbnail-container">
                <a href="https://youtu.be/MteYDXdBkTw" target="_blank">
                    <img src="<?php echo $file ?>" class="thumbnail"
                         alt="Configure the language switcher thumbnail"/>
                </a>
                <p>Need help setting up the language switcher?</p>
                <p>Watch our instruction video above!</p>
            </div>
        <?php

        $this->create_checkbox('clonable_enable_language_switcher');
    }
}