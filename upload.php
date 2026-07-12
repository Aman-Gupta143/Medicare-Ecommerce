<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Upload Prescription';
$currentPage = 'upload';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prescriptionText = trim($_POST['prescription_text'] ?? '');
    $file = $_FILES['prescription_file'] ?? null;
    $hasFile = $file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;

    if ($prescriptionText === '' && !$hasFile) {
        setFlash('error', 'Please write prescription details or upload a file first.');
    } else {
        $originalFileName = null;
        $storedFileName = null;
        $filePath = null;

        if ($hasFile) {
            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                setFlash('error', 'File upload failed. Please try again.');
                redirect('upload.php');
            }

            if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
                setFlash('error', 'File size must be 5 MB or less.');
                redirect('upload.php');
            }

            $originalFileName = basename((string) $file['name']);
            $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
            $storedFileName = uniqid('rx_', true) . ($extension !== '' ? '.' . $extension : '');
            $targetPath = __DIR__ . '/uploads/' . $storedFileName;

            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                setFlash('error', 'Could not save uploaded file.');
                redirect('upload.php');
            }

            $filePath = 'uploads/' . $storedFileName;
        }

        $uploadUser = currentUser();

        $insertStatement = $pdo->prepare(
            'INSERT INTO prescriptions (user_id, prescription_text, original_file_name, stored_file_name, file_path) VALUES (?, ?, ?, ?, ?)'
        );
        $insertStatement->execute([
            $uploadUser['id'] ?? null,
            $prescriptionText !== '' ? $prescriptionText : null,
            $originalFileName,
            $storedFileName,
            $filePath,
        ]);

        setFlash('success', 'Prescription submitted successfully.');
        redirect('upload.php');
    }
}

$recentStatement = $pdo->query(
    'SELECT prescriptions.*, users.name AS user_name
     FROM prescriptions
     LEFT JOIN users ON users.id = prescriptions.user_id
     ORDER BY prescriptions.id DESC
     LIMIT 5'
);
$recentUploads = $recentStatement->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<main class="page-shell">
    <section class="section-head">
        <div>
            <h1>Upload Prescription</h1>
            <p class="muted">Write prescription notes or upload any prescription file. The database stores the text, file name, and upload time.</p>
        </div>
    </section>

    <section class="two-column">
        <div class="panel upload-box">
            <form method="post" enctype="multipart/form-data" class="stack">
                <div class="field-group">
                    <label for="prescription_text">Prescription Details</label>
                    <textarea id="prescription_text" name="prescription_text" placeholder="Write your prescription details here"><?= e($_POST['prescription_text'] ?? ''); ?></textarea>
                </div>

                <div class="file-box">
                    <div class="field-group">
                        <label for="prescription_file">Prescription File</label>
                        <input type="file" id="prescription_file" name="prescription_file">
                    </div>
                    <p class="price-note">You can upload an image, PDF, or any supporting prescription file up to 5 MB.</p>
                </div>

                <button class="btn" type="submit">Submit</button>
            </form>
        </div>

        <aside class="panel">
            <h3>Recent Prescription Records</h3>
            <p class="muted">These entries are pulled from MySQL to confirm storage is working.</p>

            <div class="recent-list">
                <?php if ($recentUploads === []): ?>
                    <div class="empty-state">No prescription records yet.</div>
                <?php else: ?>
                    <?php foreach ($recentUploads as $upload): ?>
                        <div class="summary-line">
                            <div>
                                <strong><?= e($upload['user_name'] ?: 'Guest User'); ?></strong>
                                <div class="meta"><?= e((string) ($upload['prescription_text'] ?: 'File-only prescription submission')); ?></div>
                                <div class="meta"><?= e($upload['created_at']); ?></div>
                            </div>
                            <div>
                                <?php if ($upload['file_path']): ?>
                                    <a class="btn secondary small-btn" href="<?= e($upload['file_path']); ?>" target="_blank">Open File</a>
                                <?php else: ?>
                                    <span class="muted">No file</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </aside>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>