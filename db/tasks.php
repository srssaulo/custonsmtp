<?php
$timetosend= intval(get_config('local_custonsmtp', 'timetosend'));
$tasks = array(
		array(
				'classname' => 'local_custonsmtp\task\sendmail',
				'blocking' => 0,
				'minute' => '0',
				'hour' => $timetosend,
				'dayofweek' => '*',
				'month' => '*'
		),
);