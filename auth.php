<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Sign In / Sign Up';
$currentPage = 'auth';
$activeTab = 'signin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';
    $activeTab = $formType === 'signup' ? 'signup' : 'signin';

    /* =========================
       SIGN UP
    ========================= */
    if ($formType === 'signup') {

        $name = trim($_POST['signup_name'] ?? '');
        $email = strtolower(trim($_POST['signup_email'] ?? ''));
        $phone = trim($_POST['signup_phone'] ?? '');
        $password = $_POST['signup_password'] ?? '';
        $confirmPassword = $_POST['signup_confirm_password'] ?? '';

        if (
            $name === '' ||
            $email === '' ||
            $phone === '' ||
            $password === '' ||
            $confirmPassword === ''
        ) {
            setFlash('error', 'Please fill all sign up fields.');

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');

        } elseif (!preg_match('/^\d{10}$/', $phone)) {
            setFlash('error', 'Please enter a valid 10 digit phone number.');

        } elseif (strlen($password) < 6) {
            setFlash('error', 'Password must be at least 6 characters long.');

        } elseif ($password !== $confirmPassword) {
            setFlash('error', 'Password and confirm password do not match.');

        } else {

            $checkStatement = $pdo->prepare(
                'SELECT id FROM users WHERE email = ? LIMIT 1'
            );
            $checkStatement->execute([$email]);

            if ($checkStatement->fetch()) {

                setFlash(
                    'error',
                    'An account with this email already exists. Please sign in.'
                );

                $activeTab = 'signin';

            } else {

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $insertStatement = $pdo->prepare(
                    'INSERT INTO users (name, email, phone, password)
                     VALUES (?, ?, ?, ?)'
                );

                $insertStatement->execute([
                    $name,
                    $email,
                    $phone,
                    $passwordHash
                ]);

                /* Clear guest cart */
                saveCart([]);

                $_SESSION['user'] = [
                    'id'    => (int)$pdo->lastInsertId(),
                    'name'  => $name,
                    'email' => $email,
                    'phone' => $phone,
                ];

                setFlash(
                    'success',
                    'Account created successfully. You are now signed in.'
                );

                redirect('index.php');
            }
        }
    }

    /* =========================
       SIGN IN
    ========================= */
    if ($formType === 'signin') {

        $email = strtolower(trim($_POST['signin_email'] ?? ''));
        $password = $_POST['signin_password'] ?? '';

        if ($email === '' || $password === '') {

            setFlash('error', 'Please enter email and password.');

        } else {

            $statement = $pdo->prepare(
                'SELECT * FROM users WHERE email = ? LIMIT 1'
            );

            $statement->execute([$email]);
            $user = $statement->fetch();

            if (!$user || !password_verify($password, $user['password'])) {

                setFlash('error', 'Invalid email or password.');

            } else {

                /* Clear previous guest cart */
                saveCart([]);

                $_SESSION['user'] = [
                    'id'    => (int)$user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                ];

                setFlash('success', 'Signed in successfully.');

                redirect('index.php');
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<main class="page-shell">

    <section class="auth-layout">

        <!-- Left Info -->
        <div class="auth-info panel">
            <h1>Secure access for faster ordering.</h1>

            <p>
                Create an account to keep your details ready for future orders,
                then sign in anytime from the same browser.
            </p>

            <div class="auth-points">

                <div class="auth-point">
                    <strong>Sign Up</strong>
                    <p>Create account with name, email, phone, password.</p>
                </div>

                <div class="auth-point">
                    <strong>Sign In</strong>
                    <p>Login anytime using saved email and password.</p>
                </div>

                <div class="auth-point">
                    <strong>Fresh Cart</strong>
                    <p>
                        Guest cart automatically clears when new user logs in.
                    </p>
                </div>

            </div>
        </div>

        <!-- Right Form -->
        <div class="auth-card">

            <div class="tab-buttons">
                <button
                    type="button"
                    class="<?= $activeTab === 'signin' ? 'active' : ''; ?>"
                    onclick="showAuthTab('signin')"
                >
                    Sign In
                </button>

                <button
                    type="button"
                    class="<?= $activeTab === 'signup' ? 'active' : ''; ?>"
                    onclick="showAuthTab('signup')"
                >
                    Sign Up
                </button>
            </div>

            <!-- SIGN IN -->
            <div
                id="signin-panel"
                class="tab-panel <?= $activeTab === 'signin' ? 'active' : ''; ?>"
            >
                <form method="post" class="stack">

                    <input type="hidden" name="form_type" value="signin">

                    <div class="field-group">
                        <label for="signin_email">Email Address</label>
                        <input
                            type="email"
                            id="signin_email"
                            name="signin_email"
                            placeholder="Enter your email"
                            value="<?= e($_POST['signin_email'] ?? ''); ?>"
                        >
                    </div>

                    <div class="field-group">
                        <label for="signin_password">Password</label>
                        <input
                            type="password"
                            id="signin_password"
                            name="signin_password"
                            placeholder="Enter your password"
                        >
                    </div>

                    <button class="btn" type="submit">Sign In</button>

                </form>
            </div>

            <!-- SIGN UP -->
            <div
                id="signup-panel"
                class="tab-panel <?= $activeTab === 'signup' ? 'active' : ''; ?>"
            >
                <form method="post" class="stack">

                    <input type="hidden" name="form_type" value="signup">

                    <div class="field-group">
                        <label for="signup_name">Full Name</label>
                        <input
                            type="text"
                            id="signup_name"
                            name="signup_name"
                            placeholder="Enter your full name"
                            value="<?= e($_POST['signup_name'] ?? ''); ?>"
                        >
                    </div>

                    <div class="field-group">
                        <label for="signup_email">Email Address</label>
                        <input
                            type="email"
                            id="signup_email"
                            name="signup_email"
                            placeholder="Enter your email"
                            value="<?= e($_POST['signup_email'] ?? ''); ?>"
                        >
                    </div>

                    <div class="field-group">
                        <label for="signup_phone">Phone Number</label>
                        <input
                            type="tel"
                            id="signup_phone"
                            name="signup_phone"
                            placeholder="Enter 10 digit phone number"
                            value="<?= e($_POST['signup_phone'] ?? ''); ?>"
                        >
                    </div>

                    <div class="field-group">
                        <label for="signup_password">Password</label>
                        <input
                            type="password"
                            id="signup_password"
                            name="signup_password"
                            placeholder="Create password"
                        >
                    </div>

                    <div class="field-group">
                        <label for="signup_confirm_password">
                            Confirm Password
                        </label>
                        <input
                            type="password"
                            id="signup_confirm_password"
                            name="signup_confirm_password"
                            placeholder="Confirm password"
                        >
                    </div>

                    <button class="btn" type="submit">
                        Create Account
                    </button>

                </form>
            </div>

            <!-- Status -->
            <div class="summary-line" style="margin-top:18px;">
                <strong>Status</strong>

                <span class="muted">
                    <?= isLoggedIn()
                        ? 'Signed in as ' . e((string)currentUser()['name']) . '.'
                        : 'No user signed in yet.'; ?>
                </span>
            </div>

        </div>
    </section>
</main>

<script>
function showAuthTab(tab) {
    const buttons = document.querySelectorAll('.tab-buttons button');
    const panels = document.querySelectorAll('.tab-panel');

    buttons.forEach((button, index) => {
        const isSignIn = index === 0;

        button.classList.toggle(
            'active',
            (tab === 'signin' && isSignIn) ||
            (tab === 'signup' && !isSignIn)
        );
    });

    panels.forEach((panel) => {
        panel.classList.toggle('active', panel.id === `${tab}-panel`);
    });
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>