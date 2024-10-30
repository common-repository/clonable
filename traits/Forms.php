<?php

namespace Clonable\Traits;

/**
 * Class with common render functions for forms inside views.
 */
trait Forms {
    /**
     * Renders the actual form for the Clonable plugin with the correct CSRF fields and submit button.
     * @param $page string name of the settings section that should be rendered, often called the page.
     * @return void echos out the form
     */
    public function render_form($page) {
        ?>
            <div>
                <form method="post" action="">
                    <?php
                        settings_fields('clonable_options');
                        do_settings_sections($page);
                        submit_button();
                    ?>
                </form>
            </div>
        <?php
    }

    /**
     * Renders the actual form for the Clonable plugin with the correct CSRF fields and submit button.
     * @param $page string name of the settings section that should be rendered, often called the page.
     * @param $id string the id of the form, used for JavaScript purposes
     * @return void echos out the form
     */
    public function render_javascript_form($page, $id, $button_text = 'register') {
        ?>
            <div>
                <form id="<?php echo esc_attr($id); ?>" action="">
                    <?php
                        settings_fields('clonable_options');
                        do_settings_sections($page);
                    ?>
                    <button type="submit" class="button button-primary"><?php echo esc_html($button_text); ?></button>
                </form>
            </div>
        <?php
    }

    /**
     * Renders the $fields variables from a Model object.
     * @param $page string name of the settings section that should be created, often called the page.
     * @param $fields array the fields that should be created for the page, given as an associative array. These fields should have the following format:
     * <pre>
     * "id_of_the_field" => [
     *      "render" => "render_function_in_the_view",
     *      "name" => "Visual name in the view",
     *      "setting" => "wordpress_option_name"
     * ]</pre>
     * @param $title string the title of the settings, this get displayed at the top of the page.
     * @return void registers the settings inside a setting_section.
     */
    public function render_fields($page, $fields, $title, $description = 'description') {
        $section = $page . '_section';
        add_settings_section($section, $title , array($this, $description), $page);
        foreach ($fields as $id => $field) {
            if (!empty($field['setting'])) {
                register_setting('clonable_options', $field["setting"]);
            }
            $callback = (is_callable([$this, $field["render"]]) ? $field["render"] : "render_method_not_found");
            $hide_field = (isset($field['hidden']) && $field['hidden']);
            $extra_arguments = array('class' => (($hide_field) ? 'hidden' : ''));
            $description = ($field['description'] ?? null);
            add_settings_field($id, $this->render_title($field["name"], $description), array($this, $callback), $page, $section, $extra_arguments);
        }
    }

    /**
     * Function for creating the title of a form field.
     * @param $name string the name of the fields.
     * @param $description string|null an optional description for the field
     * @return string returns the html of the title.
     */
    public function render_title($name, $description) {
        if ($description != null) {
            $info_mark = "<div class='clonable-info-wrapper'> 
                <span class='clonable-info-description'>$description</span>
                <span class='dashicons dashicons-info clonable-info-mark'></span>
            </div>";
            return "<div class='clonable-setting-title'>" . $name . $info_mark . "</div>";
        }
        return $name;
    }

    /**
     * Function used when a render function is not found.
     * @return void prints out a span tag
     */
    public function render_method_not_found() {
        echo "<span>Could not render field associated with this setting</span>";
    }

    /**
     * Renders a checkbox field. Also renders an error when it occurs.
     * @param $name string the name attribute of the checkbox
     * @param $description string|null an optional description.
     * @return void echos out the checkbox.
     */
    public function create_checkbox($name, $default = 'off') {
        $option = get_option($name, $default);
        echo "<label>";
        echo "<input type='checkbox' name='" . esc_attr($name) . "'" . esc_attr(($option == 'on') ? 'checked' : '') . " />";
        echo "</label>";
        $this->render_error($name);
    }

    /**
     * Renders a color input field. Also renders an error when it occurs.
     * @param $name string the name attribute of the color input
     * @param $default string the default color
     * @param $description string|null an optional description.
     * @return void echos out the color field
     */
    public function create_color_field($name, $default, $description = null) {
        $option = get_option($name, $default);
        echo "<input id='" . esc_attr($name) . "' name='" . esc_attr($name) . "' placeholder='color...' type='color' value='" . esc_attr($option) . "'>";
        $this->render_error($name);
    }

    /**
     * Renders a select input field. Also renders an error when it occurs.
     * Use this for general options fields
     * @param $name string the name attribute of the select input
     * @param $values string[] the options of the checkbox.
     * @param $description string|null an optional description.
     * @return void echos out the select field
     */
    public function create_select($name, $values, $description = null) {
        $this->create_select_element(true, $name, $values);
    }

    /**
     * Renders a select input field.
     * Use this when you want to work around the get_option field
     * @param $field_name string name and id of the select field
     * @param $default string the default value of the select
     * @param $values array the possible values of the select
     * @return void echos out the select field
     */
    public function create_default_select($field_name, $default, $values) {
        $this->create_select_element(false, $field_name, $values, $default);
    }

    private function create_select_element($use_option, $name, $values, $default = '') {
        if($use_option) {
            $option = get_option($name, $values[array_key_first($values)]);
        } else {
            $option = $default;
        }

        echo "<select id='" . esc_attr($name) . "' name='" . esc_attr($name) . "'>";
        foreach ($values as $value => $name) {
            $visual = ($name ?? $value);
            if ($option === $value) {
                echo "<option value='" . esc_attr($value) . "' selected>" . esc_html($visual) . "</option>";
            } else {
                echo "<option value='" . esc_attr($value) . "'>" . esc_html($visual) . "</option>";
            }
        }
        echo "</select>";
        if($use_option) {
            $this->render_error($name);
        }
    }

    /**
     * Renders all the errors for a field generated by the add_settings_error function.
     * Renders a <li> for each error inside a <ul> element.
     * @param $setting string the name of the setting
     * @return void echos out the error list.
     */
    public function render_error($setting) {
        $errors = get_settings_errors($setting);
        if ($errors != null) {
            echo "<br/><ul style='color: red'>";
            foreach ($errors as $id => $error) {
                echo "<li>" . esc_html($error['message']) . "</li>";
            }
            echo "</ul>";
        }
    }
}