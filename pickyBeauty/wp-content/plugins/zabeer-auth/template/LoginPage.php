<?php
// /var/www/wordpress/modern_plugin/wp-content/plugins/zabeer-auth/template/LoginPage.php
$acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
$locale = preg_match('/^de\b/i', $acceptLanguage) ? 'de-DE' : 'de-DE';
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($locale, ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anmeldung</title>
    <!-- Tailwind CSS CDN -->

</head>

<body class="!bg-gradient-to-br !from-rose-700 !to-gray-900 !min-h-screen !flex !justify-center !items-center">

    <div class="!bg-white !rounded-2xl !shadow-xl !p-8 !max-w-md !w-full !transform !transition !hover:-translate-y-1 !hover:shadow-2xl">
        <div class="!text-center !mb-6">
            <h3 class="!text-3xl !font-bold !text-gray-800">Willkommen zurueck</h3>
            <p class="!text-gray-500 !mt-2">Bitte melden Sie sich an, um fortzufahren</p>
        </div>

        <form id="zabeer-login-form" class="!space-y-5">
            <input
                type="text"
                id="username"
                class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 focus:!outline-none focus:!ring-2 focus:!ring-rose-500 focus:!border-rose-500"
                placeholder="Benutzername"
                required>
            <input
                type="password"
                id="password"
                class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 focus:!outline-none focus:!ring-2 focus:!ring-rose-500 focus:!border-rose-500"
                placeholder="Passwort"
                required>
            <button
                type="submit"
                class="!w-full !py-3 !rounded-lg !text-white !font-semibold !text-lg !bg-gradient-to-r !from-red-500 !to-rose-600 hover:!from-red-600 hover:!to-rose-700 !transition-all">
                🔐 Anmelden
            </button>
        </form>

        <p class="!text-center !text-gray-500 !mt-6">
            Passwort vergessen?
            <a href="/wp-login.php?action=lostpassword" class="!text-rose-600 hover:!underline hover:!text-rose-700">Jetzt zuruecksetzen</a>
        </p>
        <!-- Divider -->
        <div class="flex items-center my-6">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="mx-3 text-gray-400 text-sm">oder</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <!-- Register Section -->
        <p class="text-center text-gray-600 mt-4 text-sm">
            <span>Noch kein Konto?</span>
            <a href="<?php echo site_url('/register'); ?>"
                class="ml-2 text-rose-500 font-semibold hover:text-rose-600 hover:underline transition">
                Registrieren Sie sich
            </a>
        </p>
    </div>

    <script>
        const API_URL = '<?php echo site_url(); ?>/wp-json/jwt-auth/v1/token';
        const ME_URL = '<?php echo site_url(); ?>/wp-json/zabeer-auth/v1/me';
        const LOGIN_URL = '<?php echo site_url(); ?>/wp-login.php';

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('zabeer-login-form');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value.trim();

                try {
                    const response = await fetch(API_URL, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            username,
                            password
                        })
                    });

                    const data = await response.json();
                    console.log("JWT Response:", data);

                    if (data.token) {
                        const token = data.token;

                        const meResponse = await fetch(ME_URL, {
                            headers: {
                                "Authorization": `Bearer ${token}`
                            }
                        });

                        const meData = await meResponse.json();
                        console.log("Authenticated User Info:", meData);

                        localStorage.setItem('jwt_token', token);
                        localStorage.setItem('auth_user', JSON.stringify(meData));

                        const wpForm = document.createElement('form');
                        wpForm.method = 'POST';
                        wpForm.action = LOGIN_URL;

                        const userField = document.createElement('input');
                        userField.type = 'hidden';
                        userField.name = 'log';
                        userField.value = username;
                        wpForm.appendChild(userField);

                        const passField = document.createElement('input');
                        passField.type = 'hidden';
                        passField.name = 'pwd';
                        passField.value = password;
                        wpForm.appendChild(passField);

                        const redirectField = document.createElement('input');
                        redirectField.type = 'hidden';
                        redirectField.name = 'redirect_to';
                        redirectField.value = '<?php echo admin_url(); ?>';
                        wpForm.appendChild(redirectField);

                        document.body.appendChild(wpForm);
                        wpForm.submit();
                    } else {
                        alert('❌ Anmeldung fehlgeschlagen! Bitte pruefen Sie Benutzername und Passwort.');
                    }
                } catch (error) {
                    console.error("Error:", error);
                    alert("⚠️ Beim Anmelden ist ein Fehler aufgetreten. Bitte versuchen Sie es spaeter erneut.");
                }
            });
            const pageContent = document.querySelector('.page-content');
            if (pageContent) {
                pageContent.classList.add('flex', 'items-center', 'justify-center');
            }

        });
    </script>


</body>

</html>
