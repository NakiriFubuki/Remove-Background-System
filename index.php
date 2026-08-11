<?php
/**
 * Remove Background System - Main Application
 * Copyright (c) 2026 Remove Background System. All rights reserved.
 */
require_once __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars(APP_NAME); ?></title>
  <meta name="description" content="Remove image backgrounds instantly with Remove Background System.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <!-- Top bar: User Manual trigger -->
  <header class="topbar">
    <div class="topbar__inner">
      <a class="brand" href="index.php" aria-label="<?php echo htmlspecialchars(APP_NAME); ?> home">
        <span class="brand__mark" aria-hidden="true"></span>
        <span class="brand__text"><?php echo htmlspecialchars(APP_NAME); ?></span>
      </a>
      <nav class="topbar__nav" aria-label="Primary">
        <button type="button" class="btn btn--ghost" id="openManualBtn" aria-haspopup="dialog" aria-controls="manualModal">
          <svg class="btn__icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
            <path fill="currentColor" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm1 7V3.5L19.5 9H15zM8 12h8v2H8v-2zm0 4h8v2H8v-2zm0-8h5v2H8V8z"/>
          </svg>
          User Manual
        </button>
      </nav>
    </div>
  </header>

  <main>
    <section class="hero">
      <div class="hero__glow" aria-hidden="true"></div>
      <div class="hero__content">
        <p class="hero__eyebrow">AI-assisted background removal</p>
        <h1 class="hero__title"><?php echo htmlspecialchars(APP_NAME); ?></h1>
        <p class="hero__lead">Upload a photo, remove the background in one click, and download a clean transparent PNG.</p>
        <div class="hero__actions">
          <label class="btn btn--primary" for="imageInput">
            Choose Image
          </label>
          <button type="button" class="btn btn--secondary" id="openManualHeroBtn">How to Use</button>
        </div>
      </div>
    </section>

    <section class="workspace" id="workspace">
      <div class="workspace__grid">
        <div class="panel">
          <div class="panel__head">
            <h2>Original</h2>
            <span class="badge" id="fileBadge">No file</span>
          </div>
          <div class="preview" id="originalPreview">
            <div class="preview__empty">
              <svg viewBox="0 0 48 48" width="48" height="48" aria-hidden="true">
                <path fill="currentColor" d="M38 10H10a4 4 0 0 0-4 4v20a4 4 0 0 0 4 4h28a4 4 0 0 0 4-4V14a4 4 0 0 0-4-4zm-2 22H12l7-9 5 6 3.5-4.5L36 32zM16 20a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
              </svg>
              <p>Drop an image here or click Choose Image</p>
              <p class="preview__hint">JPG, PNG, or WEBP · Max 10 MB</p>
            </div>
          </div>
        </div>

        <div class="panel">
          <div class="panel__head">
            <h2>Result</h2>
            <span class="badge badge--accent" id="statusBadge">Waiting</span>
          </div>
          <div class="preview preview--checkered" id="resultPreview">
            <div class="preview__empty">
              <p>Your transparent image will appear here</p>
            </div>
          </div>
        </div>
      </div>

      <div class="toolbar">
        <input type="file" id="imageInput" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp" hidden>
        <button type="button" class="btn btn--primary" id="removeBgBtn" disabled>Remove Background</button>
        <a class="btn btn--secondary is-disabled" id="downloadBtn" href="#" download aria-disabled="true">Download PNG</a>
        <button type="button" class="btn btn--ghost" id="resetBtn" disabled>Reset</button>
      </div>

      <div class="progress is-hidden" id="progressBar" role="status" aria-live="polite">
        <div class="progress__track">
          <div class="progress__fill" id="progressFill"></div>
        </div>
        <p class="progress__text" id="progressText">Preparing…</p>
      </div>
    </section>

    <section class="history" id="historySection">
      <div class="section-head">
        <h2>Recent Results</h2>
        <p>Completed removals saved on this device session.</p>
      </div>
      <div class="history__grid" id="historyGrid">
        <p class="history__empty" id="historyEmpty">No processed images yet.</p>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="footer__inner">
      <p class="footer__copy">
        &copy; <?php echo COPYRIGHT_YEAR; ?> <?php echo htmlspecialchars(COPYRIGHT_HOLDER); ?>. All rights reserved.
      </p>
      <p class="footer__meta">Version <?php echo htmlspecialchars(APP_VERSION); ?></p>
    </div>
  </footer>

  <!-- User Manual Modal -->
  <div class="modal" id="manualModal" role="dialog" aria-modal="true" aria-labelledby="manualTitle" hidden>
    <div class="modal__backdrop" data-close-manual></div>
    <div class="modal__panel" role="document">
      <div class="modal__header">
        <div>
          <p class="modal__eyebrow">Documentation</p>
          <h2 id="manualTitle">User Manual</h2>
        </div>
        <button type="button" class="modal__close" data-close-manual aria-label="Close user manual">&times;</button>
      </div>
      <div class="modal__body">
        <p class="manual-intro">
          Follow these steps to remove the background from any photo using
          <strong><?php echo htmlspecialchars(APP_NAME); ?></strong>.
        </p>

        <ol class="manual-steps">
          <li>
            <h3>Step 1 — Open the application</h3>
            <p>Start Apache (and MySQL if you want history) in XAMPP, then open
              <code>http://localhost/Remove%20Background%20System/</code> in your browser.</p>
          </li>
          <li>
            <h3>Step 2 — Upload an image</h3>
            <p>Click <strong>Choose Image</strong> at the top, or drop a file onto the Original panel.
              Supported formats: JPG, PNG, and WEBP. Maximum file size: 10 MB.</p>
          </li>
          <li>
            <h3>Step 3 — Remove the background</h3>
            <p>Click <strong>Remove Background</strong>. The system processes your image and shows a
              transparent result on the checkered preview. Wait until the status shows Completed.</p>
          </li>
          <li>
            <h3>Step 4 — Download the result</h3>
            <p>Click <strong>Download PNG</strong> to save the transparent image to your computer.
              The file is saved as a PNG so transparency is preserved.</p>
          </li>
          <li>
            <h3>Step 5 — Reset or process another image</h3>
            <p>Click <strong>Reset</strong> to clear the current images, then upload a new photo and repeat the steps.</p>
          </li>
          <li>
            <h3>Step 6 — View recent results (optional)</h3>
            <p>If MySQL is set up with <code>database/schema.sql</code>, completed images appear under
              <strong>Recent Results</strong> for quick re-download.</p>
          </li>
        </ol>

        <div class="manual-tips">
          <h3>Tips for best results</h3>
          <ul>
            <li>Use clear photos where the subject stands out from the background.</li>
            <li>Avoid heavily compressed or blurry images.</li>
            <li>Product shots and portrait photos usually work best.</li>
            <li>First-time processing may take longer while models load.</li>
          </ul>
        </div>

        <div class="manual-setup">
          <h3>One-time database setup (for history)</h3>
          <ol>
            <li>Open phpMyAdmin and import <code>database/schema.sql</code>.</li>
            <li>Confirm MySQL is running in XAMPP.</li>
            <li>Default DB credentials are in <code>includes/config.php</code> (root / empty password).</li>
          </ol>
        </div>
      </div>
      <div class="modal__footer">
        <p class="modal__copyright">
          &copy; <?php echo COPYRIGHT_YEAR; ?> <?php echo htmlspecialchars(COPYRIGHT_HOLDER); ?>. All rights reserved.
        </p>
        <button type="button" class="btn btn--primary" data-close-manual>Got it</button>
      </div>
    </div>
  </div>

  <div class="toast" id="toast" role="status" aria-live="polite" hidden></div>

  <script type="module" src="assets/js/app.js"></script>
</body>
</html>
