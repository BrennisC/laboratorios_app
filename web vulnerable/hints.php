<?php include __DIR__ . '/includes/header.php'; ?>
<h1>Progressive Hints</h1>
<section class="panel">
    <h2>Recon</h2>
    <details><summary>Hint 1</summary><p>Start like a normal user. Review the navigation and public pages.</p></details>
    <details><summary>Hint 2</summary><p>Debug information should never be exposed in production.</p></details>
</section>
<section class="panel">
    <h2>User</h2>
    <details><summary>Hint 1</summary><p>Authenticated users should only access their own objects.</p></details>
    <details><summary>Hint 2</summary><p>Look for numeric identifiers in URLs.</p></details>
</section>
<section class="panel">
    <h2>Admin</h2>
    <details><summary>Hint 1</summary><p>Authorization must be enforced server-side, not trusted from request input.</p></details>
    <details><summary>Hint 2</summary><p>Compare the admin page message with its URL parameters.</p></details>
</section>
<section class="panel">
    <h2>Root</h2>
    <details><summary>Hint 1</summary><p>XML parsers can be dangerous when external entities are enabled.</p></details>
    <details><summary>Hint 2</summary><p>The final flag is stored in the application's data directory.</p></details>
</section>
<section class="panel">
    <h2>RCE</h2>
    <details><summary>Hint 1</summary><p>Look for functionality that sends user input to the operating system.</p></details>
    <details><summary>Hint 2</summary><p>The command execution lab is intentionally restricted to localhost.</p></details>
</section>
<section class="panel">
    <h2>Upload</h2>
    <details><summary>Hint 1</summary><p>File uploads need extension, MIME type, content and storage-location controls.</p></details>
    <details><summary>Hint 2</summary><p>Files stored inside the webroot can become directly reachable from the browser.</p></details>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
