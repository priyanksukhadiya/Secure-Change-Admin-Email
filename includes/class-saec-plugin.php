<?php

namespace SECUADEM;

class SECUADEMPlugin
{
    public function secuadem_run()
    {
        add_action('init', [$this, 'secuadem_removePendingEmail']);
        add_action('admin_notices', [new AdminNotice(), 'secuadem_displayAdminNotice']);
        remove_action('add_option_new_admin_email', 'update_option_new_admin_email');
        remove_action('update_option_new_admin_email', 'update_option_new_admin_email');

        add_filter('send_site_admin_email_change_email', function () {
            return false;
        }, 10, 3);

        // Ensure nonce verification happens inside a proper hook
        add_action('admin_init', [$this, 'secuadem_verifyNonce']);
        add_action('admin_init', [$this, 'secuadem_testEmail']);

        add_action('init', function () {
            if (current_user_can('manage_options')) {
                add_action('add_option_new_admin_email', [$this, 'secuadem_updateOptionAdminEmail'], 10, 2);
                add_action('update_option_new_admin_email', [$this, 'secuadem_updateOptionAdminEmail'], 10, 2);
            }
        });

        add_action('admin_enqueue_scripts', [$this, 'secuadem_enqueue_scripts']);
    }

    public function secuadem_verifyNonce()
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saec-plugin-nonce'])) {
            if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['saec-plugin-nonce'])), 'saec-plugin-action')) {
                wp_die(esc_html__('Nonce failed. Something is wrong here.', 'secure-admin-email-change'));
            }
        }
    }

    public function secuadem_removePendingEmail()
    {
        delete_option('adminhash');
        delete_option('new_admin_email');
    }

    public function secuadem_testEmail()
    {
        
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saec-plugin-nonce'])) {
            if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['saec-plugin-nonce'])), 'saec-plugin-action')) {
                wp_die(esc_html__('Nonce verification failed. Something is wrong here.', 'secure-admin-email-change'));
            }

            if (!isset($_POST['new_admin_email'])) {
                wp_die(esc_html__('Email field is missing.', 'secure-admin-email-change'));
            }

            $email = sanitize_email(wp_unslash($_POST['new_admin_email']));

            if (!is_email($email)) {
                wp_die(esc_html__('Invalid email address provided.', 'secure-admin-email-change'));
            }
            
            $current_year = gmdate('Y');
            $key = "PRIMALDEVS{$current_year}SAEC";
            $domain = site_url();
            $data_to_encode = $email . '|' . $domain;
            $iv = openssl_random_pseudo_bytes(16);
            $encrypted_value = openssl_encrypt($data_to_encode, 'AES-256-CBC', $key, 0, $iv);
            $encrypted_payload = base64_encode($iv . $encrypted_value);

            $url = "https://blog.primaldevs.com/wp-json/secure-admin-email-change/v1/test-email";
            $response = wp_remote_post($url, [
                'method' => 'POST',
                'body' => [
                    'email' => $email,
                    'domain' => $domain,
                    'encoded' => $encrypted_payload,
                ],
            ]);

            AdminNotice::displaySuccess(esc_html__('Check your email inbox. A test message has been sent to you.', 'secure-admin-email-change'));
        }
    }

    public function secuadem_updateOptionAdminEmail($old_value, $value)
    {
        update_option('admin_email', sanitize_email($value));
    }

    public function secuadem_enqueue_scripts($hook)
    {
        if ($hook !== 'options-general.php') {
            return;
        }
    
        wp_enqueue_script('saec-plugin', plugin_dir_url(__FILE__) . 'js/saec-admin-email.js', ['jquery'], '1.0', true);
    
        wp_localize_script('saec-plugin', 'change_admin_email_data', [
            'nonce' => wp_create_nonce('saec-plugin-action'),
        ]);
    }
}
