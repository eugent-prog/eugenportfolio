<?php
declare(strict_types=1);

$root = dirname(__DIR__);

ob_start();
require $root . '/index.php';
$html = ob_get_clean();

$email = 'eugentaborada21@gmail.com';
$mailto = 'mailto:' . $email . '?subject=Portfolio%20Contact';
$staticContact = <<<HTML
            <div class="contact-static">
              <p>
                For opportunities, interviews, or project discussions, email me
                directly and I will respond as soon as I can.
              </p>
              <a class="button primary form-button" href="{$mailto}">
                Email Eugen
              </a>
            </div>
HTML;

$html = preg_replace(
    '/\s*<form\s+class="contact-form"[\s\S]*?<\/form>/',
    "\n" . $staticContact,
    $html,
    1
);

if ($html === null) {
    fwrite(STDERR, "Failed to build static HTML.\n");
    exit(1);
}

file_put_contents($root . '/index.html', $html);

echo "Built index.html\n";
