ALTER TABLE `{$PREFIX}groups`
ADD `view_logs` TINYINT(1) NOT NULL ,
ADD `view_unrestricted_logs` TINYINT(1) NOT NULL ,
ADD `view_private_logs` TINYINT(1) NOT NULL;