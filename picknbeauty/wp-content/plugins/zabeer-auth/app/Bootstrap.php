<?php
if (!defined('ABSPATH')) {
    exit;
}

class ZabeerAuthBootstrap
{
    private static ?ZabeerAuthRestController $rest_controller = null;

    public static function init(): void
    {
        add_filter('get_avatar_url', 'zabeer_auth_filter_avatar_url', 10, 3);

        add_action('init', ['PageCreationController', 'zabeer_auth_create_page']);
        add_action('wp_logout', [self::class, 'handle_logout_redirect']);
        add_action('wp_footer', [self::class, 'render_logout_cleanup']);

        self::$rest_controller = new ZabeerAuthRestController();
        add_action('rest_api_init', [self::$rest_controller, 'register_routes']);
    }

    public static function handle_logout_redirect(): void
    {
        $rootUrl = site_url('/?logged_out=true');
        wp_redirect($rootUrl);
        exit;
    }

    public static function render_logout_cleanup(): void
    {
        if (!isset($_GET['logged_out']) || $_GET['logged_out'] !== 'true') {
            return;
        }
        ?>
        <script>
            (function() {
                try {
                    localStorage.clear();
                    sessionStorage.clear();

                    document.cookie.split(";").forEach(c => {
                        document.cookie = c
                            .replace(/^ +/, "")
                            .replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
                    });

                    if ('caches' in window) {
                        caches.keys().then(names => {
                            for (let name of names) caches.delete(name);
                        });
                    }

                    console.log("✅ Cleared all session data after logout.");
                } catch (e) {
                    console.error("Logout cleanup failed:", e);
                }
            })();
        </script>
        <?php
    }
}
