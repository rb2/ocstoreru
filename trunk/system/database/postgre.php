<?php
final class Postgre {
    private $connection;

    public function __construct($hostname, $username, $password, $database) {
	if (!$this->connection = pg_pconnect('host='.$hostname.' dbname='.$database.' user='.$username.' password='.$password)) {
	    exit('Error: Could not make a database connection using ' . $username . '@' . $hostname);

	}
    }

    public function query($sql) {

	    $newsql = $sql;
	    $isselect = 0;
	    $md5query = '';
	    $pos = stripos($sql, 'select ');
	    if ($pos == 0)
	    {
		$isselect = 1;

		// Replace "limit N,M" to "limit N offset M"
		$newsql = preg_replace('/^(.+) LIMIT ([0-9]+),([0-9]+)$/i', '\1 LIMIT \3 OFFSET \2', $sql);

		$newsql = preg_replace('/"/', '\'', $newsql);
		$newsql = preg_replace('/`/', '"', $newsql);

		// Replace zero date '0000-00-00' to '0001-01-01'
		$newsql = preg_replace('/\'0000-00-00\'/', '\'0001-01-01\'', $newsql);
		$newsql = preg_replace('/\'0000-00-00 ([0-9\:]+)\'/', '\'0001-01-01 \1\'', $newsql);

		$sql = $newsql;
	    };

	    $pos = stripos($sql, 'insert ');
	    if ($pos == 0)
	    {
		if (preg_match('/^INSERT[\s]+INTO ("|)([^"\s]+)("|)[\s]+SET\s+(.+)$/i', $sql, $matches))
		{
		    $tabname = $matches[2];

		    // Array name-value
		    $vals = array();
		    // Replace "INSERT INTO ... SET ..." syntax on "INSERT INTO (...) VALUES (...)"
		    $s = $matches[4];
		    // Replace '...' to `...`

		    //$d = 0;
		    while ($s != "")
		    {
			//	    $d++;
			//	    $s = preg_replace("/^([^']+)=\s*'(.*)'($|\s*,)/si", "\\1= `\\2`\\3", $s);

			// Если значение без кавычек - ставим кавычки
			if (preg_match("/^\s*(\"|)([^\s=\"]+)(\"|)\s*=\s*([^'`,]*)[\s]*,[\s]*(.*)$/", $s))
			{
			    $s = preg_replace("/^\s*(\"|)([^\s=\"]+)(\"|)\s*=\s*([^'`,]*)[\s]*,[\s]*(.*)$/si", "\\2 = '\\4', \\5`", $s);
			};

			// Меняем первую кавычку на `
			$s = preg_replace("/^([^']+)'/si", "\\1`", $s);
			// Пока находятся двойные кавычки - меняем их на ключевое слово
			while (preg_match("/^[^']+''/", $s))
			{
			    $s = preg_replace("/^([^']+)''/si", "\\1&&doublequite&&", $s);
			};
			$s = preg_replace("/^([^']+)'/si", "\\1`", $s);
			//echo '1: ', $s, "\n";
			//if ($d > 5) { exit; };
			if (preg_match('/^\s*(\"|)([^\s=\"]+)(\"|)\s*=\s*`([^`]*)`\s*(,|)\s*(.*)$/', $s, $m))
			{
			    //echo '2: ', $m[2] , ' = ', $m[4], ' --- ', $m[6], "\n";
			    $i = count($vals);
			    $vals[$i]['name'] = $m[2];
			    $vals[$i]['value'] = $m[4];
			    $s = $m[6];
			};
		};

		// Create new query
		$s = 'INSERT INTO "' . $tabname . '" (';
		$i = 0;
		$sep = '';
		while ($i < count($vals))
		{
		    $s .= $sep . '"' . $vals[$i]['name'] . '"';
		    $sep = ', ';
		    $i++;
		};

		$s .= ') VALUES (';
		$i = 0;
		$sep = '';
		while ($i < count($vals))
		{
		    $s .= $sep . "'" . $vals[$i]['value'] . "'";
		    $sep = ', ';
		    $i++;
		};
		$s .= ')';

		$s = preg_replace("/&&doublequite&&/", "''", $s);
		$s = preg_replace("/`/", "'", $s);

		$sql = $s;
		};
	    };

	    $resource = pg_query($this->connection, $sql);

	    if ($resource) {
		if (is_resource($resource)) {
		    $i = 0;

		    $data = array();

		    while ($result = pg_fetch_assoc($resource)) {
			$data[$i] = $result;

			$i++;
		    }

		    pg_free_result($resource);

		    $query = new stdClass();
		    $query->row = isset($data[0]) ? $data[0] : array();
		    $query->rows = $data;
		    $query->num_rows = $i;

		    unset($data);

		    return $query;
		} else {
		    return TRUE;
		}
	    } else {
		exit('Error: ' . pg_last_error($this->connection) . ': ' . $sql);
	    }
    }

    public function escape($value) {
	return pg_escape_string($this->connection, $value);
    }

    public function countAffected() {
	return pg_affected_rows($this->connection);
    }

    public function getLastId() {
	$lastval = $this->query('select lastval() as lastval');
	return $lastval->row['lastval'];
    }

    public function __destruct() {
	pg_close($this->connection);
    }

}
?>
