<?php
require_once __DIR__ . '/config.php';

$next  = safe_next($_GET['next'] ?? $_POST['next'] ?? null);
$error = '';
$email = '';

if (current_user()) {
    redirect($next);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } elseif ($email === '' || $password === '') {
        $error = 'Enter your email and password.';
    } else {
        try {
            $st = db()->prepare('SELECT id, password_hash FROM users WHERE email = ?');
            $st->execute([$email]);
            $row = $st->fetch();
            if ($row && password_verify($password, $row['password_hash'])) {
                login_user((int)$row['id']);
                redirect($next);
            }
            $error = 'Incorrect email or password.';
        } catch (Throwable $e) {
            $error = 'We could not reach the database. Please try again shortly.';
        }
    }
}

$page_title = 'Log in | Blessed Snaks';
$active     = 'login';
$solid_nav  = true;
require __DIR__ . '/includes/header.php';
?>
<main class="page-top auth-page">
  <div class="auth-card">
    <h1>Welcome back</h1>
    <p class="muted">Log in to place your order.</p>

    <?php if ($error): ?><p class="form-error" role="alert"><?= e($error) ?></p><?php endif; ?>

    <form method="post" action="login.php" class="form" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="next" value="<?= e($next) ?>">
      <label>Email
        <input type="email" name="email" value="<?= e($email) ?>" autocomplete="email" required autofocus>
      </label>
      <label>Password
        <input type="password" name="password" autocomplete="current-password" required>
      </label>
      <button type="submit" class="btn btn-primary btn-block btn-lg">Log in</button>
    </form>

    <p class="auth-alt">New here? <a href="register.php?next=<?= urlencode($next) ?>">Create an account</a></p>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
