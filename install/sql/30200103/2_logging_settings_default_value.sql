UPDATE `{$PREFIX}groups` SET
	view_logs = 1,
	view_unrestricted_logs = 1,
	view_private_logs = 1
WHERE
	id = 1;