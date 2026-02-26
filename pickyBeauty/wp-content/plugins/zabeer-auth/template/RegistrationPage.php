<?php
// /var/www/wordpress/modern_plugin/wp-content/plugins/zabeer-auth/template/RegistrationPage.php

ob_start();

$zipcodes_json_url = wp_make_link_relative(plugins_url('germanZipcodes.json', __FILE__));

// ✅ Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zabeer_register'])) {

    $username  = sanitize_user($_POST['username']);
    $email     = sanitize_email($_POST['email']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm_password'];
    $role      = isset($_POST['role']) ? sanitize_text_field($_POST['role']) : 'user';

    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name  = sanitize_text_field($_POST['last_name']);
    $address    = sanitize_text_field($_POST['address']);
    $postal     = sanitize_text_field($_POST['postal_code'] ?? ($_POST['postal_code_display'] ?? ''));
    $city       = sanitize_text_field($_POST['city']);
    $phone      = sanitize_text_field($_POST['phone']);
    $targets    = isset($_POST['targets']) ? implode(', ', array_map('sanitize_text_field', $_POST['targets'])) : '';
    $description = sanitize_textarea_field($_POST['description']);

    // ✅ Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $error = "⚠️ Please fill in all required fields.";
    } elseif ($password !== $confirm) {
        $error = "❌ Passwords do not match.";
    } elseif (username_exists($username)) {
        $error = "❌ Username already exists.";
    } elseif (email_exists($email)) {
        $error = "❌ Email already registered.";
    } elseif (!in_array($role, ['user', 'vendor'], true)) {
        $error = "❌ Invalid role selected.";
    } else {
        // ✅ Create new user
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            $error = "⚠️ " . $user_id->get_error_message();
        } else {
            // ✅ Assign role and save meta
            $user = new WP_User($user_id);
            if ($role === 'vendor') {
                $user->set_role('vendor');
                update_user_meta($user_id, 'is_vendor', true);
            } else {
                // Regular user
                $user->set_role('subscriber');
                update_user_meta($user_id, 'is_vendor', false);
            }

            // Common meta for both user and vendor
            update_user_meta($user_id, 'first_name', $first_name);
            update_user_meta($user_id, 'last_name', $last_name);
            update_user_meta($user_id, 'vendor_address', $address);
            update_user_meta($user_id, 'zipcode', $postal);
            update_user_meta($user_id, 'vendor_city', $city);
            update_user_meta($user_id, 'vendor_phone', $phone);
            update_user_meta($user_id, 'vendor_description', $description);
            if (!empty($_FILES['vendor_logo']['name'])) {
                $avatar_id = zabeer_auth_store_user_avatar_from_upload($_FILES['vendor_logo'], $user_id);
                if (!is_wp_error($avatar_id)) {
                    $logo_url = wp_get_attachment_url($avatar_id);
                    if ($logo_url) {
                        update_user_meta($user_id, 'vendor_logo_url', esc_url_raw($logo_url));
                    }
                }
            }

            zabeer_auth_send_welcome_email($user_id, $role);

            // ✅ Success message to trigger toast in JS
            $success = true;
        }
    }
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
 
    <style>
        .toast {
            position: fixed;
            top: 25px;
            right: 25px;
            background-color: #16a34a;
            color: white;
            padding: 14px 22px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.4s ease;
            z-index: 9999;
            font-weight: 500;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="!bg-gradient-to-br !from-rose-700 !to-gray-900 !min-h-screen !flex !justify-center !items-center">



    <div class="!bg-white !rounded-2xl !shadow-xl !p-8 !max-w-lg !w-full !transform !transition !hover:-translate-y-1 !hover:shadow-2xl">
        <div class="!text-center !mb-6">
            <h3 class="!text-3xl !font-bold !text-gray-800">Create Account</h3>
            <p class="!text-gray-500 !mt-2">Choose your role and fill out your details below</p>
        </div>

        <?php if (!empty($error)): ?>
            <p class="!text-center !text-red-500 !mb-4 !font-semibold"><?php echo esc_html($error); ?></p>
        <?php endif; ?>

        <!-- Registration Form -->
        <form method="POST" enctype="multipart/form-data" class="!space-y-5">
            <div>
                <label class="!block !text-gray-700 !font-medium !mb-2">Register as*</label>
                <select name="role" required class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">
                    <option value="user" <?php echo (!isset($_POST['role']) || (isset($_POST['role']) && $_POST['role'] === 'user')) ? 'selected' : ''; ?>>User</option>
                    <option value="vendor" <?php echo (isset($_POST['role']) && $_POST['role'] === 'vendor') ? 'selected' : ''; ?>>Vendor</option>
                </select>
            </div>
            <div class="!grid !grid-cols-2 !gap-4">
                <input type="text" name="first_name" placeholder="Vorname*" required class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">
                <input type="text" name="last_name" placeholder="Nachname*" required class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">
            </div>

            <input type="text" name="username" placeholder="Benutzername*" required class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">

            <div class="!grid !grid-cols-2 !gap-4">
                <input type="password" name="password" placeholder="Passwort*" required class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">
                <input type="password" name="confirm_password" placeholder="Passwort bestätigen*" required class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">
            </div>

            <input type="email" name="email" placeholder="Email*" required class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">
            <input type="text" name="address" placeholder="Adresse*" required class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">
            <div class="!relative">
                <input type="hidden" id="postal_code_value" name="postal_code" value="">
                <input type="text" id="postal_code" name="postal_code_display" placeholder="Postleitzahl*" required autocomplete="off" class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">
                <ul id="postal_suggestions" class="absolute left-0 right-0 bg-white border border-gray-200 rounded-xl mt-1 max-h-56 overflow-y-auto shadow-md z-50 hidden"></ul>
            </div>
            <input type="text" id="city" name="city" placeholder="Stadt*" required autocomplete="off" class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">
            <input type="text" name="phone" placeholder="Telefonnummer*" required class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">



            <textarea name="description" placeholder="Beschreibung*" required rows="3" class="!w-full !px-4 !py-3 !border !border-gray-300 !rounded-lg !text-gray-700 !focus:outline-none !focus:ring-2 !focus:ring-rose-500"></textarea>

            <div>
                <label class="!block !text-gray-700 !font-medium !mb-2">Logo / Bild (optional)</label>
                <input type="file" name="vendor_logo" accept="image/*" class="!w-full !text-gray-700 !border !border-gray-300 !rounded-lg !px-4 !py-2 !focus:outline-none !focus:ring-2 !focus:ring-rose-500">
            </div>

            <button type="submit" name="zabeer_register" class="!w-full !py-3 !rounded-lg !text-white !font-semibold !text-lg !bg-gradient-to-r !from-red-500 !to-rose-600 !hover:from-red-600 !hover:to-rose-700 !transition-all">
                ✨ Register
            </button>
        </form>

        <div class="!flex !items-center !my-6">
            <div class="!flex-grow !border-t !border-gray-300"></div>
            <span class="!mx-3 !text-gray-400 !text-sm">or</span>
            <div class="!flex-grow !border-t !border-gray-300"></div>
        </div>

        <p class="!text-center !text-gray-600 !mt-4 !text-sm">
            Already have an account?
            <a href="/sign-in" class="!ml-2 !text-rose-500 !font-semibold !hover:text-rose-600 !hover:underline !transition">Log in</a>
        </p>
    </div>
    <script>
        const setupPostalDropdown = () => {
            const pageContent = document.querySelector('.page-content');
            if (pageContent) {
                pageContent.classList.add('flex', 'items-center', 'justify-center');
            }

            const postalInput = document.getElementById('postal_code');
            const postalValue = document.getElementById('postal_code_value');
            const postalSuggestions = document.getElementById('postal_suggestions');

            if (!postalInput || !postalSuggestions) {
                return false;
            }

            if (postalInput.dataset.zipSetup) {
                return true;
            }
            postalInput.dataset.zipSetup = '1';

            let zipcodes = [];
            const zipcodesUrl = "<?php echo esc_url($zipcodes_json_url); ?>";

            const clearSuggestions = () => {
                postalSuggestions.innerHTML = '';
                postalSuggestions.classList.add('hidden');
                postalSuggestions.style.display = 'none';
            };

            const renderMatches = (query) => {
                clearSuggestions();
                if (!query) {
                    return;
                }
                const normalized = query.toLowerCase();
                const zipQuery = query.replace(/\D/g, '');
                const fragment = document.createDocumentFragment();
                let count = 0;

                for (const entry of zipcodes) {
                    if (!entry || !entry.zipcode) {
                        continue;
                    }
                    const zip = entry.zipcode.toString();
                    const place = (entry.place || '').toLowerCase();
                    const zipMatch = zipQuery ? zip.startsWith(zipQuery) : false;
                    const placeMatch = normalized ? place.includes(normalized) : false;
                    if (!zipMatch && !placeMatch) {
                        continue;
                    }

                    const item = document.createElement('li');
                    item.className = 'p-3 cursor-pointer hover:bg-rose-100 transition-colors';
                    item.innerHTML = `<span class="!font-semibold">${zip}</span> — <span>${entry.place || ''}</span>`;
                    item.addEventListener('click', () => {
                        const fullValue = entry.place ? `${zip} ${entry.place}` : zip;
                        postalInput.value = fullValue;
                        if (postalValue) {
                            postalValue.value = fullValue;
                        }
                        console.log('[zip] selected', zip);
                        clearSuggestions();
                    });
                    fragment.appendChild(item);
                    count += 1;
                    if (count >= 10) {
                        break;
                    }
                }

                if (count > 0) {
                    postalSuggestions.appendChild(fragment);
                    postalSuggestions.classList.remove('hidden');
                    postalSuggestions.style.display = 'block';
                }
            };

            const loadZipcodes = () => {
                if (!zipcodesUrl) {
                    return;
                }
                fetch(zipcodesUrl, { cache: 'no-cache' })
                    .then((response) => response.json())
                    .then((data) => {
                        zipcodes = Array.isArray(data) ? data : [];
                        renderMatches(postalInput.value.trim());
                    })
                    .catch((error) => {
                        // Silent fail: keep input usable even if JSON fails to load.
                    });
            };

            loadZipcodes();

            postalInput.addEventListener('input', (event) => {
                if (postalValue) {
                    postalValue.value = '';
                }
                renderMatches(event.target.value.trim());
            });

            return true;
        };

        document.addEventListener('DOMContentLoaded', () => {
            setupPostalDropdown();
            let attempts = 0;
            const timer = setInterval(() => {
                attempts += 1;
                if (setupPostalDropdown() || attempts >= 10) {
                    clearInterval(timer);
                }
            }, 500);
        });
    </script>


    <?php if (!empty($success)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {


                const toast = document.getElementById('toast');
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                    window.location.href = "<?php echo site_url('/sign-in?registered=1'); ?>";
                }, 2000);


            });
        </script>
    <?php endif; ?>

</body>

</html>
