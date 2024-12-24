<?php

return [
	'mode'                  => 'utf-16',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
	'tempDir'               => base_path('storage/app/mpdf'),
	'pdf_a'                 => false,
	'pdf_a_auto'            => false,
	'icc_profile_path'      => '',
	'font_path' => base_path('storage/fonts/'),
	'font_data' => [
		'dejafont' => [
			'R'  => 'DejaVuSans.ttf',    // regular font
			'B'  => 'DejaVuSans-Bold.ttf',       // optional: bold font
			'I'  => 'DejaVuSans-Italic.ttf',     // optional: italic font
			'BI' => 'DejaVuSans-BoldItalic.ttf', // optional: bold-italic font
			'useOTL' => 0xFF,    // required for complicated langs like Persian, Arabic and Chinese
			'useKashida' => 75,  // required for complicated langs like Persian, Arabic and Chinese
		]
		// ...add as many as you want.
	]

];
