<?php

namespace SECUADEM;

class AdminNotice
{
    const NOTICE_FIELD = 'my_admin_notice_message';

    public static function displayError($message)
    {
        self::updateOption($message, 'notice-error');
    }

    protected static function updateOption($message, $noticeLevel)
    {
        update_option('secuadem_admin_notice_message', ['message' => sanitize_text_field($message), 'notice-level' => sanitize_text_field($noticeLevel)]);
    }

    public static function displayWarning($message)
    {
        self::updateOption($message, 'notice-warning');
    }

    public static function displayInfo($message)
    {
        self::updateOption($message, 'notice-info');
    }

    public static function displaySuccess($message)
    {
        self::updateOption($message, 'notice-success');
    }

    public function secuadem_displayAdminNotice()
    {
        $option = get_option(self::NOTICE_FIELD);
        $message = isset($option['message']) ? esc_html($option['message']) : false;
        $noticeLevel = !empty($option['notice-level']) ? esc_attr($option['notice-level']) : 'notice-error';

        if ($message) {
            echo "<div class='notice " . esc_attr($noticeLevel) . " is-dismissible'><p>" . esc_html($message) . '</p></div>';
            delete_option(self::NOTICE_FIELD);
        }
    }
}
