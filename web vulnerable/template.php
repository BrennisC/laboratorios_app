<?php
$name = $_GET['name'] ?? 'Customer';
$template = $_GET['template'] ?? 'Welcome, {{name}}';

// VULN: A05 - SSTI-like unsafe expression evaluation for lab practice.
$output = str_replace('{{name}}', $name, $template);
$output = preg_replace_callback('/{{calc:(.*?)}}/', function ($matches) {
    return (string) eval('return ' . $matches[1] . ';');
}, $output);

include __DIR__ . '/includes/header.php';
?>
<h1>Message Template</h1>
<section class="panel"><p><?= $output ?></p></section>
<?php include __DIR__ . '/includes/footer.php'; ?>
