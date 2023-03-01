<?php
/*class log_mysqli extends mysqli {
    public $qcounter = 0;
	public $deb = false;
	public $name = "";
	private $patch = "";

	public function debug($debug = false, $name = NULL) {
		$this->deb = $debug;
		$this->name = $name;
		$this->patch = "/var/www/log/SQLog.".$this->name."_".date("ymd").".cache";
		if ($this->deb)
			file_put_contents($this->patch, "\n", FILE_APPEND);
	}

    public function query($sql) {
		$this->qcounter++;

		if ($this->deb)
			file_put_contents($this->patch, date("H:i:s")."\t".$sql."\n", FILE_APPEND);

        return parent::query($sql);
    }

	public function getCounter() {
		return $this->qcounter;
	}
}
*/

class log_mysqli extends mysqli {
    public $qcounter = 0;
    public $deb = false;
    public $name = "";
    private $patch = "";

    public function debug($debug = false, $name = null) {
        $this->deb = $debug;
        $this->name = $name;
        $this->patch = "/var/www/log/SQLog.".$this->name."_".date("ymd").".cache";
        if ($this->deb) {
            file_put_contents($this->patch, "\n", FILE_APPEND);
        }
    }

    public function query($sql) {
        $this->qcounter++;
		$start_time = microtime(true);
        $result = parent::query($sql);
        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;
        
        $log_entry = date('Y-m-d H:i:s') . " | Execution time: {$execution_time} seconds\n";
        $log_entry .= "Query: {$sql}\n\n";

		if ($this->deb) {
            file_put_contents($this->patch, $log_entry, FILE_APPEND | LOCK_EX);
        }
		/*
        if ($this->deb) {
            file_put_contents($this->patch, date("H:i:s")."\t".$sql."\n", FILE_APPEND);
        }
		*/
        /*if ($this->log_file !== null) {
            $log_entry = date('Y-m-d H:i:s') . "\nQuery: {$sql}\n";
            file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);
        }*/

        return $result;
    }

    public function prepare($query) {
        return new LogMySQLiStatement($this, $query, $this->patch, $this->deb);
    }

    public function getCounter() {
        return $this->qcounter;
    }
}

class LogMySQLiStatement extends mysqli_stmt {
    private $log_file; // nazwa pliku, do którego będą zapisywane zapytania
	private $deb = false;
	private $query;

    public function __construct($link, $query, $log_file, $deb) {
        parent::__construct($link, $query);
        $this->log_file = $log_file;
		$this->deb = $deb;
		$this->query = $query;
    }

    public function execute() {
        $start_time = microtime(true);
        $result = parent::execute();
        $end_time = microtime(true);
        $execution_time = $end_time - $start_time;
        
        $log_entry = date('Y-m-d H:i:s') . " | Execution time: {$execution_time} seconds\n";
        $log_entry .= "Query: {$this->query}\n\n";

        if ($this->deb) {
            file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);
        }

        return $result;
    }
}




?>