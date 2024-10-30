<?php

namespace Clonable\Views;

use Clonable\Helpers\Html;
use Clonable\Models\ClonableWooCommerce;
use Clonable\Traits\Forms;

class WoocommerceView implements ViewInterface {
    use Forms;

    public function render() {
        Html::include_alpine();
        $module_enabled = get_option('clonable_woocommerce_module_enabled', 'on') === 'on';
        if (!$module_enabled) {
            $this->render_disabled_banner();
        }
        $this->render_fields(ClonableWooCommerce::PAGE, ClonableWooCommerce::$fields, 'Clonable WooCommerce Settings');
        $this->render_form(ClonableWooCommerce::PAGE);
    }

    public function description() {
        echo '<p style="max-width: 75%">When using Clonable, Clonable can keep track of which clone an order did originate from. When a user finishes their payment,
                 they will be redirected back to the clone, so you can properly record conversions. Clonable will also add a field to the WooCommerce order, so
                 you can from which clone the order has been made.</p>';
    }

	public function clonable_woocommerce_analytics_enabled_field() {
        $this->create_checkbox('clonable_woocommerce_analytics_enabled', 'on');
	}

    public function clonable_woocommerce_exclusions_enabled_field() {
        $this->create_checkbox('clonable_product_exclusions_enabled', 'on');
    }

    public function clonable_woocommerce_module_enabled_field() {
        $this->create_checkbox('clonable_woocommerce_module_enabled', 'on');
    }

    public function clonable_woocommerce_analytics_field() {
        $option = get_option('clonable_woocommerce_allowed_origins', '');
        if (gettype($option) === 'string' && !str_contains($option, '[')) {
            $parsed_origins = array_map(function ($origin) {
                return "'" . trim($origin) . "'";
            }, explode(",", $option));
            $output = esc_attr("[" . implode(",", $parsed_origins) . "]");
        } else {
            $output = esc_js($option);
        }

        // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
        ?>
        <p style="max-width: 75%">These locations will fix the problem clones have with redirecting from payment providers. It can happen that
            a payment provider will redirect the user to the original shop page instead of the clone. This often happens after a customer has ordered a product
            and lands on the so called 'Thank you' page. Enter all the domains on which you want a translated 'Thank you' page to exist. If a domain for a
            clone is not entered, the default 'Thank you' page will be used. Read
            <a target="_blank" href="https://kb.clonable.net/en/introduction/setup/platforms/woocommerce#conversie-metingen">our docs</a> for a detailed description.
            <br/>
            <strong>Only the domain name is required, order received page will automatically be prefixed to the domain.</strong>
        </p>
        <div class="wrap">
            <div x-data="wooCommerceAllowedOrigins(<?php echo $output; ?>)">
                <!-- hidden input field for submitting the json value -->
                <label for="clonable_langtag_data"></label>
                <input x-model="JSON.stringify(origins)" name="clonable_woocommerce_allowed_origins" id="clonable_woocommerce_allowed_origins" style="display: none"/>
                <!-- Display data -->
                <table class="wp-list-table fat striped" style="width: 75%">
                    <thead>
                    <tr>
                        <th>Domain</th>
                        <th style="width: 25px; text-align: center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="(origin, index) in origins" :key="index">
                        <tr>
                            <td><input x-model="origins[index]" style="width: 100%" /></td>
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

                <button type="button" class="button action" @click="addRow">Add another domain</button>
            </div>
        </div>
        <?php
        // phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
        $this->render_error('clonable_woocommerce_allowed_origins');
    }

    public function render_disabled_banner() {
        ?>
            <div class="clonable-banner">
                <p><strong>The Clonable WooCommerce module is disabled.</strong></p>
                <p><i>Enable it by using the setting below.</i></p>
            </div>
        <?php
    }
}