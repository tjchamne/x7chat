CREATE TABLE `{$prefix}message_fonts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `font` varchar(255) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=12 ;

INSERT INTO `{$preifx}message_fonts` (`id`, `name`, `font`) VALUES
(1, 'Arial', 'Arial, Helvetica, sans-serif'),
(2, 'Comic Sans', 'Comic Sans, Comic Sans MS, cursive, serif'),
(3, 'Courier New', 'Courier New, Courier, monospace'),
(4, 'Georgia', 'Georgia, serif'),
(5, 'Impact', 'Impact, Charcoal, sans-serif'),
(7, 'Lucida Sans', 'Lucida Sans Unicode, Lucida Grande, sans-serif'),
(8, 'Tahoma', 'Tahoma, Geneva, sans-serif'),
(9, 'Times New Roman', 'Times New Roman, Times, serif'),
(10, 'Trebuchet', 'Trebuchet, Trebuchet MS, sans-serif'),
(11, 'Verdana', 'Verdana, Geneva, sans-serif');