<?php
class ModelInstall extends Model {
	public function mysql($data) {
		$connection = mysql_connect($data['db_host'], $data['db_user'], $data['db_password']);

		mysql_select_db($data['db_name'], $connection);

		mysql_query("SET NAMES 'utf8'", $connection);
		mysql_query("SET CHARACTER SET utf8", $connection);

		// Если флаг есть, очистить БД
		if(isset($this->request->post['flushdbflag']) AND $this->request->post['flushdbflag'] == 1) {
			$this->flushDatabase($connection);
		}


		$file = DIR_APPLICATION . 'opencart.sql';

		if ($sql = file($file)) {
			$query = '';

			foreach($sql as $line) {
				$tsl = trim($line);

				if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
					$query .= $line;

					if (preg_match('/;\s*$/', $line)) {
						$query = str_replace("DROP TABLE IF EXISTS `oc_", "DROP TABLE IF EXISTS `" . $data['db_prefix'], $query);
						$query = str_replace("CREATE TABLE `oc_", "CREATE TABLE `" . $data['db_prefix'], $query);
						$query = str_replace("INSERT INTO `oc_", "INSERT INTO `" . $data['db_prefix'], $query);

						$result = mysql_query($query, $connection);

						if (!$result) {
							die(mysql_error());
						}

						$query = '';
					}
				}
			}

			mysql_query("SET CHARACTER SET utf8", $connection);

			mysql_query("SET @@session.sql_mode = 'MYSQL40'", $connection);

			mysql_query("DELETE FROM `" . $data['db_prefix'] . "user` WHERE user_id = '1'");

			mysql_query("INSERT INTO `" . $data['db_prefix'] . "user` SET user_id = '1', user_group_id = '1', username = '" . mysql_real_escape_string($data['username']) . "', password = '" . mysql_real_escape_string(md5($data['password'])) . "', status = '1', date_added = NOW()", $connection);

			mysql_query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_email'", $connection);
			mysql_query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `group` = 'config', `key` = 'config_email', value = '" . mysql_real_escape_string($data['email']) . "'", $connection);

			mysql_query("DELETE FROM `" . $data['db_prefix'] . "setting` WHERE `key` = 'config_url'", $connection);
			mysql_query("INSERT INTO `" . $data['db_prefix'] . "setting` SET `group` = 'config', `key` = 'config_url', value = '" . mysql_real_escape_string(HTTP_OPENCART) . "'", $connection);

			mysql_query("UPDATE `" . $data['db_prefix'] . "product` SET `viewed` = '0'", $connection);

			mysql_close($connection);
		}
	}

	public function postgre($data) {
	    $connection = pg_pconnect('host='.$data['db_host'].' dbname='.$data['db_name'].' user='.$data['db_user'].' password='.$data['db_password']);

	    // Если флаг есть, очистить БД
	    if(isset($this->request->post['flushdbflag']) AND $this->request->post['flushdbflag'] == 1) {
		$this->flushDatabase($connection, 'postgre');
	    }


	    $file = DIR_APPLICATION . 'opencart.postgre.sql';

	    if ($sql = file($file)) {
		$query = '';

		$isfunc = 0;

		foreach($sql as $line) {
		    $tsl = trim($line);

		    if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
			$query .= $line;

			if (preg_match('/^CREATE .*FUNCTION /', $line))
			{
			    $isfunc = 1;
			};

			if (preg_match('/;\s*$/', $line)) {
			    if (($isfunc == 0) || preg_match('/\$\$ LANGUAGE sql;$/', $line))
			    {
				$query = str_replace("DROP TABLE IF EXISTS \"oc_", "DROP TABLE IF EXISTS \"" . $data['db_prefix'], $query);
				$query = str_replace("CREATE TABLE \"oc_", "CREATE TABLE \"" . $data['db_prefix'], $query);
				$query = str_replace("INSERT INTO \"oc_", "INSERT INTO \"" . $data['db_prefix'], $query);

				$result = pg_query($connection, $query);

				if (!$result) {
				    die(pg_last_error());
				}

				$query = '';
				$isfunc = 0;
			    };
			}
		}
	    }

	    pg_query($connection, "DELETE FROM \"" . $data['db_prefix'] . "user\" WHERE user_id = 1");

	    pg_query($connection, "INSERT INTO \"" . $data['db_prefix'] . "user\" (user_id, user_group_id, username, password, status, date_added) VALUES (1, 1, '" . pg_escape_string($data['username']) . "', '" . pg_escape_string(md5($data['password'])) . "', 1, NOW())");

	    pg_query($connection, "DELETE FROM \"" . $data['db_prefix'] . "setting\" WHERE \"key\" = 'config_email'");
	    pg_query($connection, "INSERT INTO \"" . $data['db_prefix'] . "setting\" (\"group\", \"key\", \"value\") VALUES ('config', 'config_email', '" . pg_escape_string($data['email']) . "')");

	    pg_query($connection, "DELETE FROM \"" . $data['db_prefix'] . "setting\" WHERE \"key\" = 'config_url'");
	    pg_query($connection, "INSERT INTO \"" . $data['db_prefix'] . "setting\" (\"group\", \"key\", \"value\") VALUES ('config', 'config_url', '" . pg_escape_string(HTTP_OPENCART) . "')");

	    pg_query($connection, "UPDATE \"" . $data['db_prefix'] . "product\" SET \"viewed\" = 0");

	    pg_close($connection);
	    }
	}

	private function flushDatabase($connection, $db_driver = 'mysql') {

		if ($db_driver == 'mysql')
		{
		    $resource = mysql_query('SHOW TABLES', $connection);

		    $data = array();
		    $i = 0;
		    while ($result = mysql_fetch_row($resource)) {
			$data[$i] = $result[0];
			$i++;
		    }

		    foreach ($data as $table) {
			mysql_query('DROP table ' . $table , $connection);
		    }
		}
		elseif ($db_driver == 'postgre')
		{
		    $resource = pg_query($connection, "select table_name from information_schema.tables where table_schema = 'public'");

		    $data = array();
		    $i = 0;
		    while ($result = pg_fetch_row($resource)) {
			$data[$i] = $result[0];
			$i++;
		    }

		    foreach ($data as $table) {
			pg_query($connection, 'DROP table ' . $table);
		    }
		};


	}
}
?>