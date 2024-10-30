<?php

namespace Clonable\Services;

class ShortCodeService {
    public function __construct() {
        add_shortcode('not-clone', array( $this, 'not_clone'));
        add_shortcode('clone-only', array( $this, 'clone_only'));
    }

    public function clone_only($atts, $content = null) {
        $server_data = $_SERVER; // this fixes some linting rules
        $target_language = $server_data['HTTP_CLONABLE_TARGET_LANGUAGE'] ?? null;

        if ($target_language == null) {
            return "";
        }

        if (empty($atts['clone'])) {
            return do_shortcode($content);
        }

        if ($atts['clone'] === $target_language) {
            return do_shortcode($content);
        }

        return "";
    }

    public function not_clone($atts, $content = null) {
        $server_data = $_SERVER; // this fixes some linting rules
        $target_language = $server_data['HTTP_CLONABLE_TARGET_LANGUAGE'] ?? null;

        if ($target_language == null) {
            return do_shortcode($content);
        }
        return "";
    }
}