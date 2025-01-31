<section class="form-section">
    <div class="form-box">
        <h2 class="form-title">Login</h2>

        <div id="error-box" class="alert alert-danger" style="display: none;"></div>

        <?php if (!empty($successMessage)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please enter a valid email address."
                    placeholder="Enter your email" autofocus
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control" required minlength="8"
                        title="Password must be at least 8 characters long." placeholder="Enter your password">
                    <button type="button" id="togglePassword" class="password-toggle-btn">👁️</button>
                </div>
            </div>

            <!-- <div class="form-group text-end">
                <a href="" class="forgot-password-link">Forgot Password?</a>
            </div> -->

            <div class="form-group">
                <button type="submit" class="btn btn-custom">Login</button>
            </div>
        </form>
    </div>
</section>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
        this.textContent = passwordField.type === 'password' ? '👁️' : '🙈';
    });

    $('#loginForm').on('submit', function (e) {
        e.preventDefault();

        const formData = $(this).serialize();

        $.ajax({
            url: '<?= BASE_URL . "login"; ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    window.location.href = response.redirect_url;
                } else {
                    let errorHtml = '<ul>';
                    response.errors.forEach(function (error) {
                        errorHtml += `<li>${error}</li>`;
                    });
                    errorHtml += '</ul>';
                    $('#error-box').html(errorHtml).show();
                }
            },
            error: function () {
                $('#error-box').html('<ul><li>An error occurred. Please try again later.</li></ul>').show();
            }
        });
    });
</script>