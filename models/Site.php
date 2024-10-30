<?php

namespace Clonable\Models;

class Site {
    const PAGE = "site";

    public static $fields = [
        "clonable_site_domain" => [
            "render" => "site_domain_field",
            "name" => "Site domain",
        ],
        "clonable_site_locale" => [
            "render" => "site_locale_field",
            "name" => "Site locale",
        ],
        "clonable_site_preferred_domain" => [
            "render" => "site_preferred_domain_field",
            "name" => "Site www or non-www",
            "hidden" => true,
        ],
        "clonable_site_origin" => [
            "render" => "site_origin_field",
            "name" => "Site origin",
            "hidden" => true,
        ],
    ];

    /* @var array $site */
    private $site;

    public function __construct($site) {
        $this->site = $site;
    }

    public function get_id() {
        return $this->site["id"];
    }

    public function get_domain() {
        return $this->site["domain"];
    }

    public function get_locale() {
        return $this->site["lang_code"];
    }

    public function get_all_props() {
        return $this->site;
    }

    /**
     * Gets the clones from a site, in the form of an associative array, in which the keys are
     * the fields of the clone.
     * @return array
     */
    public function get_clones() {
        return $this->site["clones"];
    }

    public function is_validated() {
        return $this->site["validated"] === 1;
    }

    public function get_validation_hash() {
        return $this->site["validation_hash"];
    }

    public function get_subfolders() {
        $clones = $this->site["clones"];
        $domain = $this->get_domain();
        $mapped_subdomains = array_map(function ($clone) use ($domain) {
            if ($clone["domain"] == $domain) {
                return $clone["subfolder_clone"];
            }
            return null;
        }, $clones);
        return array_filter($mapped_subdomains, function ($mapped_domain) {
            return $mapped_domain != null;
        });
    }

    public function get_url() {
        return 'https://' . $this->get_domain();
    }

    public function get_clone_url($index) {
        $clone = $this->site["clones"][$index];

        $prefix = '';
        if ($clone['prefer_www'] == 'www' || ($clone['prefer_www'] == 'inherit' && $this->site['prefer_www'] === 1)) {
            $prefix = 'www.';
        }

        return 'https://' . $prefix . $clone['domain'] . $clone['subfolder_clone'];
    }
}