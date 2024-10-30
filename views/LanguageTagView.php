<?php

namespace Clonable\Views;

use Clonable\Helpers\Html;
use Clonable\Helpers\Session;
use Clonable\Models\LanguageTag;
use Clonable\Services\ApiService;
use Clonable\Traits\Forms;

class LanguageTagView implements ViewInterface {
    use Forms;

    public function render() {
        Html::include_fomantic_dropdown();
        Html::include_alpine();
        $this->render_fields(LanguageTag::PAGE, LanguageTag::$fields, 'Language Tag Settings');
        $this->render_form(LanguageTag::PAGE);
    }

    public function description() {
        echo '<p>Use the list below to manage the language tags on your clone. Language tags help search engines get a
                better view of the multiple versions of your site. You can find the data you need to enter in the
                Clonable Dashboard. For more information, click <a href="https://kb.clonable.net/">here</a>.</p>';
    }

    public function clonable_langtag_data_field() {
        $option = Session::old('clonable_langtag_data', 'off');
        ?>
            <p>In the table below you can add your language tags. There are a few rules for adding the language tags:</p>
            <ul>
                <li>1: Domain names should <span style="font-weight: bold">not</span> start with "https://".</li>
                <li>2: Hreflang should follow locale code format. For example: Belgium is "nl-be".</li>
                <li>3: Subfolder fields should always end with a "/".</li>
                <li>4: The first row in the table, is the language tag of your original site.</li>
                <li>5: The domain field should <span style="font-weight: bold">not</span> include the subfolder of the clone.</li>
            </ul>
            <div class="wrap">
                <div x-data="languageTags(<?php echo esc_textarea($option); ?>)">
                    <!-- hidden input field for submitting the json value -->
                    <label for="clonable_langtag_data"></label>
                    <input x-model="JSON.stringify(languageTagData)" name="clonable_langtag_data" id="clonable_langtag_data" style="display: none"/>
                    <!-- Display data -->
                    <table class="wp-list-table fat striped" style="width: 75%">
                        <thead>
                            <tr>
                                <th>Hreflang</th>
                                <th>Domain</th>
                                <th style="width: 25px">Original subfolder</th>
                                <th style="width: 25px">Clone subfolder</th>
                                <th style="width: 25px">Translate URLs</th>
                                <th style="width: 25px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="field">
                                        <div class="ui search selection dropdown" style="width: 75%">
                                            <input x-model="languageTagData.data.original.langcode" type="hidden">
                                            <i class="dropdown icon"></i>
                                            <div class="default text mt-0.5"></div>
                                            <div class="menu">
                                                <?php
                                                $added_locales = [];
                                                foreach (ApiService::get_locales() as $language) {
                                                    $formatted = str_replace('_', '-', strtolower($language['locale']));
                                                    $added_locales[] = $formatted;
                                                    echo "<div @click='languageTagData.data.original.langcode = \"" . esc_attr($formatted) . "\"' class='item' data-value='" . esc_attr($formatted) . "'>" . esc_html($formatted) . "</div>";

                                                    $lang_only = explode('_', $language['locale'])[0];
                                                    if (!in_array($lang_only, $added_locales)) {
                                                        $added_locales[] = $lang_only;
                                                        echo "<div @click='languageTagData.data.original.langcode = \"" . esc_attr($lang_only) . "\"' class='item' data-value='" . esc_attr($lang_only) . "'>" . esc_html($lang_only) . "</div>";
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><input x-model="languageTagData.data.original.domain" style="width: 100%" /></td>
                                <td style="width: 45px"></td>
                                <td style="width: 25px"></td>
                                <td style="width: 25px"></td>
                                <td style="width: 25px">
                                    <div style="display: flex; justify-content: space-around;">
                                        <input type="checkbox" style="display: none" :checked="languageTagData.data.original.include" x-model="languageTagData.data.original.include">
                                        <button type="button" style="display: flex; justify-content: center; align-items: center;" class="button" @click="languageTagData.data.original.include = !languageTagData.data.original.include"
                                                x-tooltip="'click to ' + (languageTagData.data.original.include ? 'exclude' : 'include') + ' the default language tag.'">
                                            <span class="dashicons" :class="languageTagData.data.original.include ? 'dashicons-lock' : 'dashicons-unlock'"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <template x-for="(clone, index) in languageTagData.data.clones" :key="index">
                                <tr>
                                    <td>
                                        <div class="field">
                                            <div class="ui search selection dropdown" style="width: 75%">
                                                <input x-model="languageTagData.data.clones[index].langcode" type="hidden">
                                                <i class="dropdown icon"></i>
                                                <div class="default text mt-0.5"></div>
                                                <div class="menu">
                                                    <?php
                                                    $added_locales = [];
                                                    foreach (ApiService::get_locales() as $language) {
                                                        $formatted = str_replace('_', '-', strtolower($language['locale']));
                                                        $added_locales[] = $formatted;
                                                        echo "<div @click='languageTagData.data.clones[index].langcode = \"" . esc_attr($formatted) . "\"' class='item' data-value='" . esc_attr($formatted) . "'>" . esc_html($formatted) . "</div>";

                                                        $lang_only = explode('_', $language['locale'])[0];
                                                        if (!in_array($lang_only, $added_locales)) {
                                                            $added_locales[] = $lang_only;
                                                            echo "<div @click='languageTagData.data.clones[index].langcode = \"" . esc_attr($lang_only) . "\"' class='item' data-value='" . esc_attr($lang_only) . "'>" . esc_html($lang_only) . "</div>";
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><input x-model="languageTagData.data.clones[index].domain" style="width: 100%" /></td>
                                    <td style="width: 25px"><input x-model="languageTagData.data.clones[index].original_subfolder" style="width: 100%" /></td>
                                    <td style="width: 25px"><input x-model="languageTagData.data.clones[index].clone_subfolder" style="width: 100%" /></td>
                                    <td style="width: 25px; text-align: center;">
                                        <input type="checkbox" x-bind:checked="languageTagData.data.clones[index].translate_urls"
                                               @change="languageTagData.data.clones[index].translate_urls = $event.target.checked"/>
                                    </td>
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

                    <button type="button" class="button action" @click="addRow">Add another language tag</button>
                </div>
            </div>
        <?php
        $this->render_error('clonable_langtag_data');
    }

    public function clonable_langtag_switch_field() {
        $option = get_option('clonable_langtag_switch', 'off');
        echo "<label>";
        echo "<input type='checkbox' name='clonable_langtag_switch'" . (($option === 'on') ? 'checked' : '') . " />";
        echo "<span>(May incur a small performance penalty when loading a page for the first time)</span>";
        echo "</label>";
        $this->render_error('clonable_langtag_switch');
    }
}