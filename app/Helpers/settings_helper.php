<?php

/**
 * Get a site setting value by key
 *
 * @param string $key     The setting key
 * @param string $default Default value if key not found
 * @return string
 */
function get_setting(string $key, string $default = ''): string
{
    static $settings = null;

    if ($settings === null) {
        try {
            $db = \Config\Database::connect();
            $results = $db->table('site_settings')->get()->getResultArray();
            $settings = [];
            foreach ($results as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (\Exception $e) {
            $settings = [];
        }
    }

    return $settings[$key] ?? $default;
}
