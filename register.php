<?php
require_once __DIR__ . '/config.php';

$next   = safe_next($_GET['next'] ?? $_POST['next'] ?? null);
$errors = [];
$old    = ['name' => '', 'email' => '', 'phone' => ''];

if (current_user()) {
    redirect($next);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name']  = trim((string)($_POST['name'] ?? ''));
    $old['email'] = trim((string)($_POST['email'] ?? ''));
    $old['phone'] = trim((string)($_POST['phone'] ?? ''));
    $password     = (string)($_POST['password'] ?? '');
    $confirm      = (string)($_POST['confirm'] ?? '');

    if (!csrf_valid($_POST['csrf'] ?? null)) {
        $errors[] = 'Your session expired. Please try again.';
    }
    if (mb_strlen($old['name']) < 2 || mb_strlen($old['name']) > 80) {
        $errors[] = 'Enter your name (2 to 80 characters).';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 150) {
        $errors[] = 'Enter a valid email address.';
    }
    if (!preg_match('/^\+?[0-9 ]{9,15}$/', $old['phone'])) {
        $errors[] = 'Enter a valid phone number, e.g. +256 700 000 000.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        try {
            $st = db()->prepare('INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)');
            $st->execute([$old['name'], $old['email'], $old['phone'], password_hash($password, PASSWORD_DEFAULT)]);
            login_user((int)db()->lastInsertId());
            redirect($next);
        } catch (PDOException $e) {
            $errors[] = ($e->getCode() === '23000')
                ? 'That email is already registered. Try logging in instead.'
                : 'We could not create your account right now. Please try again.';
        } catch (Throwable $e) {
            $errors[] = 'We could not reach the database. Please try again shortly.';
        }
    }
}

$page_title = 'Sign up | Blessed Snaks';
$active     = 'register';
$solid_nav  = true;
require __DIR__ . '/includes/header.php';
?>
<main class="page-top auth-page">
  <div class="auth-card">
    <h1>Create your account</h1>
    <p class="muted">It takes a minute, then you can place your order.</p>

    <?php if ($errors): ?>
      <div class="form-error" role="alert">
        <?php foreach ($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="register.php" class="form" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="next" value="<?= e($next) ?>">
      <label>Full name
        <input type="text" name="name" value="<?= e($old['name']) ?>" autocomplete="name" required autofocus>
      </label>
      <label>Email
        <input type="email" name="email" value="<?= e($old['email']) ?>" autocomplete="email" required>
      </label>
      <label>Phone number
        <input type="tel" name="phone" value="<?= e($old['phone']) ?>" placeholder="+256 700 000 000" autocomplete="tel" required>
      </label>
      <label>Password
        <input type="password" name="password" minlength="8" autocomplete="new-password" required>
      </label>
      <label>Confirm password
        <input type="password" name="confirm" minlength="8" autocomplete="new-password" required>
      </label>
      <button type="submit" class="btn btn-primary btn-block btn-lg">Create account</button>
    </form>

    <p class="auth-alt">Already have an account? <a href="login.php?next=<?= urlencode($next) ?>">Log in</a></p>
  </div>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
