<?php

namespace Clonable\Controllers;

use Clonable\Models\ApiKey;
use Clonable\Services\SyncService;
use Clonable\Traits\Validation;
use Clonable\Views\OnboardingView;

class LandingPageController extends Controller {
    use Validation;
    //<editor-fold desc="Singleton pattern">
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new LandingPageController();
        }
        return self::$instance;
    }

    private function __construct() {
        // Keep constructor private
        $this->view = new OnboardingView();
    }

    //</editor-fold>

    public function validate($request) {
        $this->validate_fields($request, ApiKey::$fields);
        // if the api key gets successfully validated, then we can synchronise the site
        if (!empty(get_option('clonable_api_key'))) {
            $sync_service = new SyncService();
            $sync_service->sync_site();
        }
    }

    public function clonable_api_key_validate($input) {
        return $this->validate_api_key($input, 'clonable_api_key');
    }
}