<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Contact Us';
$currentPage = 'contact';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        setFlash('error', 'Please fill all contact fields.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Please enter a valid email address.');
    } else {
        $statement = $pdo->prepare(
            'INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)'
        );
        $statement->execute([$name, $email, $message]);

        setFlash('success', 'Your message was submitted successfully.');
        redirect('contact.php');
    }
}

include __DIR__ . '/includes/header.php';
?>

<main class="page-shell">
    <section class="panel form-panel">
        <h1>Contact Us</h1>
        <p>Email: ag9785634@gmail.com</p>
        <p>Phone: 9056207052</p>
        <br>

        <form method="post" class="stack">
            <div class="field-group">
                <label for="name">Your Name</label>
                <input id="name" name="name" placeholder="Your Name" value="<?= e($_POST['name'] ?? ''); ?>">
            </div>
            <div class="field-group">
                <label for="email">Your Email</label>
                <input id="email" name="email" placeholder="Your Email" value="<?= e($_POST['email'] ?? ''); ?>">
            </div>
            <div class="field-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" placeholder="Your Message"><?= e($_POST['message'] ?? ''); ?></textarea>
            </div>
            <button class="btn" type="submit">Send</button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>