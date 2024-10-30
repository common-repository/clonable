<?php

namespace Clonable\Views;

use Clonable\Helpers\Html;
use Clonable\Models\ApiKey;
use Clonable\Traits\Forms;

class OnboardingView implements ViewInterface {
    use Forms;

    public function render() {
        Html::include_css("clonable.css");
        Html::include_css("clonable-thumbnails.css");
        Html::include_alpine();
        $file = plugin_dir_url(__DIR__) . "images/clonable-full-logo.png";
        $thumbnail = plugin_dir_url(__DIR__) . "images/configure-wordpress-thumbnail.png";

        ?>
        <div class="landing">
            <img src="<?php echo esc_url($file); ?>" class="logo" alt="Clonable logo with slogan"/>
            <div class="col-center">
                <h2 class="title">Welcome to Clonable!</h2>
                <p style="text-align: center; margin-bottom: 35px; font-size: 1rem">
                    Before you can use the plugin, it is necessary to connect the Clonable dashboard with your WordPress installation.
                    <br/>
                    You need to take the following 2 step before you can use the plugin.
                </p>
            </div>
            <div class="row-center">
                <div class="card">
                    <h2>Step 1</h2>
                    <h3>Create a Clonable account</h3>
                    <p>
                        To make use of the Clonable plugin, you will need to create a Clonable account. Creating an account
                        is totally free, and does not require any credit card information.
                    </p>
                    <a href="https://app.clonable.net/register" target="_blank" class="button button-clonable">Create your account</a>

                    <div class="thumbnail-container-block">
                        <a href="https://youtu.be/7FftYudccf4" target="_blank">
                            <img src="<?php echo $thumbnail ?>" class="thumbnail small"
                                 alt="Configure the language switcher thumbnail"/>
                        </a>
                    </div>
                </div>
                <div class="card" style="margin-left: 50px">
                    <div class="custom-arrow">
                        <div class="arrow-text">
                            <span>Already got an account?</span>
                            <small>Skip step 1</small>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 800 800">
                            <g stroke-width="15" stroke="#ff541e" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="47.5 33" transform="matrix(0.30901699437494745,0.9510565162951535,-0.9510565162951535,0.30901699437494745,566.8158087680824,-182.02940426804037)">
                                <path d="M181.11764526367188 184Q669.1176452636719 184 613.1176452636719 616 " marker-end="url(#SvgjsMarker4438)"></path>
                            </g>
                            <defs>
                                <marker markerWidth="5" markerHeight="5" refX="2.5" refY="2.5" viewBox="0 0 5 5" orient="auto" id="SvgjsMarker4438">
                                    <polygon points="0,5 0,0 5,2.5" fill="#ff541e"></polygon>
                                </marker>
                            </defs>
                        </svg>
                    </div>
                    <?php
                        $this->render_fields(ApiKey::PAGE, ApiKey::$fields, 'Step 2');
                        $this->render_form(ApiKey::PAGE);
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Step 2 description
     * @return void
     */
    public function description() {
        ?>
            <h3>Connect your Clonable account</h3>
            <p>If already own a Clonable account, then you can find or create your api key in the
                <a href="https://app.clonable.net/my-account#keys" target="_blank" class="link">Clonable dashboard.</a>
            </p>
        <?php
    }

    public function api_key_field() {
        $option = get_option("clonable_api_key");
        echo "<input type='text' name='clonable_api_key' id='clonable_api_key' value='" . esc_attr($option) . "'/>";
        $this->render_error("clonable_api_key");
    }
}