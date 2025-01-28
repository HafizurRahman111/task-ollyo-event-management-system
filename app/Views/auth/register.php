<section class="form-section">
    <div class="form-box">
        <h2 class="form-title">Create Account</h2>

        <!-- Display error and success messages -->
        <div id="error-box" class="error-box" aria-live="polite" style="display: none;">
            <ul id="error-list" aria-labelledby="error-message"></ul>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form id="registerForm" method="POST" action="<?= BASE_URL . 'auth/register'; ?>" autocomplete="off">
            <div class="form-group">
                <label for="fullname">Fullname</label>
                <input type="text" id="fullname" name="fullname" class="form-control" required minlength="3"
                    maxlength="250" placeholder="Enter your fullname"
                    value="<?= htmlspecialchars($_POST['fullname'] ?? '', ENT_QUOTES); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required maxlength="250"
                    placeholder="Enter your email" value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES); ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control" required minlength="8"
                        maxlength="250" placeholder="Enter your password">
                    <button type="button" id="togglePassword" class="password-toggle-btn">👁️</button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required
                        minlength="8" maxlength="250" placeholder="Confirm your password">
                    <button type="button" id="toggleConfirmPassword" class="password-toggle-btn">👁️</button>
                </div>
            </div>

            <div class="form-group text-end">
                <a href="<?= BASE_URL . 'auth/login'; ?>">Already have an account? Login</a>
            </div>

            <div class="form-group">
                <button type="submit" id="submitButton" class="btn btn-custom">Register</button>
            </div>
        </form>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleVisibility = (inputId, button) => {
            const input = document.getElementById(inputId);
            if (input) {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                button.textContent = isPassword ? '🙈' : '👁️';
            }
        };

        const togglePasswordButton = document.getElementById('togglePassword');
        const toggleConfirmPasswordButton = document.getElementById('toggleConfirmPassword');

        togglePasswordButton?.addEventListener('click', () => {
            toggleVisibility('password', togglePasswordButton);
        });

        toggleConfirmPasswordButton?.addEventListener('click', () => {
            toggleVisibility('confirm_password', toggleConfirmPasswordButton);
        });

        const registerForm = document.getElementById('registerForm');
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const errors = validateForm();
            if (errors.length) {
                displayErrors(errors);
                return;
            }

            const submitButton = document.getElementById('submitButton');
            submitButton.disabled = true;

            try {
                const formData = new FormData(registerForm);
                const response = await fetch(registerForm.action, { method: 'POST', body: formData });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                if (data.success) {
                    window.location.href = '<?= BASE_URL . "auth/login"; ?>';
                } else {
                    displayErrors(data.errors || ['Registration failed.']);
                }
            } catch (err) {
                console.error('Error during registration:', err);
                displayErrors(['An unexpected error occurred. Please try again.']);
            } finally {
                submitButton.disabled = false;
            }
        });

        const validateForm = () => {
            const fullname = document.getElementById('fullname').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('confirm_password').value.trim();
            const errors = [];

            if (fullname.length < 3 || fullname.length > 250) {
                errors.push('Fullname must be between 3 and 250 characters.');
            }

            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/i;
            if (!emailRegex.test(email)) {
                errors.push('Enter a valid email address.');
            }

            if (password.length < 8) {
                errors.push('Password must be at least 8 characters long.');
            }

            if (password !== confirmPassword) {
                errors.push('Passwords do not match.');
            }

            return errors;
        };

        const displayErrors = (errors) => {
            const errorBox = document.getElementById('error-box');
            const errorList = document.getElementById('error-list');

            if (errorBox && errorList) {
                errorList.innerHTML = errors.map(error => `<li>${error}</li>`).join('');
                errorBox.style.display = errors.length > 0 ? 'block' : 'none';
            }
        };
    });

</script>