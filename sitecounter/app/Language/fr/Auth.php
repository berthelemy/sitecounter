<?php

declare(strict_types=1);

$shieldAuthFile = ROOTPATH . 'vendor/codeigniter4/shield/src/Language/fr/Auth.php';
$messages = is_file($shieldAuthFile) ? require $shieldAuthFile : [];

return array_replace($messages, [
    'invalidEmail' => 'Impossible de verifier que l\'adresse e-mail "{0}" correspond a l\'adresse enregistree.',
]);
