<?php

$link = url_to('verify-magic-link') . '?token=' . urlencode($token);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= esc(lang('Auth.magicLinkSubject')) ?></title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #212529; margin: 0; padding: 16px;">
	<p><strong><?= esc(lang('Auth.magicLinkSubject')) ?></strong></p>

	<p><?= esc(lang('Auth.emailInfo')) ?></p>

	<p><?= esc(lang('Auth.username')) ?>: <?= esc($user->username) ?></p>
	<p><?= esc(lang('Auth.emailIpAddress')) ?> <?= esc($ipAddress) ?></p>
	<p><?= esc(lang('Auth.emailDevice')) ?> <?= esc($userAgent) ?></p>
	<p><?= esc(lang('Auth.emailDate')) ?> <?= esc($date) ?></p>

	<p>
		<a href="<?= esc($link) ?>" style="display: inline-block; padding: 8px 12px; background-color: #0d6efd; color: #ffffff; text-decoration: none; border-radius: 4px;">
			<?= esc(lang('Auth.login')) ?>
		</a>
	</p>

	<p><?= esc(lang('Auth.login')) ?>: <a href="<?= esc($link) ?>"><?= esc($link) ?></a></p>
	<p>If the link is not clickable, copy and paste it into your browser.</p>
</body>
</html>
