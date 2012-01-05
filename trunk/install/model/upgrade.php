<?php
class ModelUpgrade extends Model {
	public function mysql($data, $sqlfile) {
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		
		$connection = mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);

		mysql_select_db(DB_DATABASE, $connection);

		mysql_query("SET NAMES 'utf8'", $connection);
		mysql_query("SET CHARACTER SET utf8", $connection);

		$file = DIR_APPLICATION . $sqlfile;

		if (!file_exists($file)) { die('Could not load sql file: ' . $file); }

		if ($sql = file($file)) {
			$query = '';

			foreach($sql as $line) {
				$tsl = trim($line);

				if (($sql != '') && $tsl && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
					$line = str_replace("oc_", DB_PREFIX, $line);

					// Check existing conditions for specific commands
					// For example, ALTER TABLE will error if the table has since been removed,
					// So validate the table exists first, etc.
					if (preg_match('/^ALTER TABLE (.+?) ADD PRIMARY KEY/', $line, $matches)) {
						$info = mysql_fetch_assoc(mysql_query(sprintf("SHOW KEYS FROM %s",$matches[1]), $connection));
						if ($info['Key_name'] == 'PRIMARY') { continue; }
					}
					if (preg_match('/^ALTER TABLE (.+?) ADD INDEX (.+?) /', $line, $matches)) {
						if (mysql_num_rows(mysql_query(sprintf("SHOW KEYS FROM %s WHERE Key_name != 'PRIMARY' AND Column_name = '%s'",$matches[1],str_replace('`', '', $matches[2])), $connection)) > 0) { continue; }
					}
					if (preg_match('/^ALTER TABLE (.+?) ADD (.+?) /', $line, $matches)) {
						if (mysql_num_rows(mysql_query(sprintf("SHOW COLUMNS FROM %s LIKE '%s'", $matches[1],str_replace('`', '', $matches[2])), $connection)) > 0) { continue; }
					}
					if (preg_match('/^ALTER TABLE (.+?) DROP (.+?) /', $line, $matches)) {
						if (mysql_num_rows(mysql_query(sprintf("SHOW COLUMNS FROM %s LIKE '%s'", $matches[1],str_replace('`', '', $matches[2])), $connection)) <= 0) { continue; }
					}
					if (preg_match('/^ALTER TABLE ([^\s]+) DEFAULT (.+?) /', $line, $matches)) {
						if (mysql_num_rows(mysql_query(sprintf("SHOW TABLES LIKE '%s'", str_replace('`', '', $matches[1])), $connection)) <= 0) { continue; }
					}
					if (preg_match('/^ALTER TABLE (.+?) MODIFY (.+?) /', $line, $matches)) {
						if (mysql_num_rows(mysql_query(sprintf("SHOW COLUMNS FROM %s LIKE '%s'", $matches[1],str_replace('`', '', $matches[2])), $connection)) <= 0) { continue; }
					}

					$query .= $line;

					// If the line has a semicolon, consider it a complete query
					if (preg_match('/;\s*$/', $line)) {
						$result = mysql_query($query, $connection);

						if (!$result) {
							die("Could not Execute: $query <br />" . mysql_error());
						}

						$query = '';
					}
				}
			}

			mysql_query("SET CHARACTER SET utf8", $connection);
			mysql_query("SET @@session.sql_mode = 'MYSQL40'", $connection);

			mysql_close($connection);
		}
	}
}
?>