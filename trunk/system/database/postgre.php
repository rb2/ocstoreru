<?php
final class Postgre {
    private $connection;
    private $resource;
    private $affectedRows = 0;

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

		$sql = $newsql;
	    };

	    $sql = preg_replace('/"/', '\'', $sql);
	    $sql = preg_replace('/`/', '"', $sql);
	    $sql = preg_replace('/&&&quote&&&/', '"', $sql);

	    // Replace zero date '0000-00-00' to '0001-01-01'
	    $sql = preg_replace('/\'0000-00-00\'/', '\'0001-01-01\'', $sql);
	    $sql = preg_replace('/\'0000-00-00 ([0-9\:]+)\'/', '\'0001-01-01 \1\'', $sql);

	    $this->resource = pg_query($this->connection, $sql);

	    if ($this->resource) {
		$this->affectedRows = pg_affected_rows($this->resource);
		if (is_resource($this->resource)) {
		    $i = 0;

		    $data = array();

		    while ($result = pg_fetch_assoc($this->resource)) {
			$data[$i] = $result;

			$i++;
		    }

		    pg_free_result($this->resource);

		    $query = new stdClass();
		    $query->row = isset($data[0]) ? $data[0] : array();
		    $query->rows = $data;
		    $query->num_rows = $i;

		    unset($data);

		    return $query;
		} else {
		    return TRUE;
		};
	    } else {
		exit('Error: ' . pg_last_error($this->connection) . ': ' . $sql);
	    }
    }

    public function escape($value) {
	$s = pg_escape_string($value);
	$s = preg_replace('/"/', '&&&quote&&&', $s);

	return $s;
    }

    public function countAffected() {
	return $this->affectedRows;
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
