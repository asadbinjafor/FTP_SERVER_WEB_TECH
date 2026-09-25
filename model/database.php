<?php
// PDO-backed adapter retains the result methods used throughout the views.
class DbResult {
    private $rows;
    private $offset = 0;
    public $num_rows;
    public function __construct($statement) {
        $this->rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $this->num_rows = count($this->rows);
    }
    public function fetch_assoc() { return $this->rows[$this->offset++] ?? null; }
}

class DbStatement {
    private $statement;
    private $values = array();
    private $types = '';
    public function __construct($pdo, $sql) { $this->statement = $pdo->prepare($sql); }
    public function bind_param($types, &...$values) {
        $this->types = $types;
        $this->values = $values;
    }
    public function execute() {
        foreach ($this->values as $index => $value) {
            $type = ($this->types[$index] ?? 's') === 'i' ? PDO::PARAM_INT : PDO::PARAM_STR;
            $this->statement->bindValue($index + 1, $value, $type);
        }
        return $this->statement->execute();
    }
    public function get_result() { return new DbResult($this->statement); }
}

class DbConnection {
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }
    public function prepare($sql) { return new DbStatement($this->pdo, $sql); }
    public function query($sql) { return new DbResult($this->pdo->query($sql)); }
    public function lastInsertId($sequence) { return $this->pdo->lastInsertId($sequence); }
}

function databaseConnection() {
    $url = getenv('DATABASE_URL');
    if ($url) {
        $parts = parse_url($url);
        if ($parts === false || !isset($parts['host'], $parts['path'])) {
            throw new RuntimeException('Invalid DATABASE_URL');
        }
        $host = $parts['host'];
        $port = $parts['port'] ?? 5432;
        $name = ltrim($parts['path'], '/');
        $user = rawurldecode($parts['user'] ?? '');
        $password = rawurldecode($parts['pass'] ?? '');
        parse_str($parts['query'] ?? '', $options);
        $sslmode = $options['sslmode'] ?? getenv('DB_SSLMODE') ?: 'require';
    } else {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT') ?: 5432;
        $name = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');
        $sslmode = getenv('DB_SSLMODE') ?: 'require';
    }
    if (!$host || !$name || !$user || !$password || !in_array($sslmode, array('disable', 'allow', 'prefer', 'require', 'verify-ca', 'verify-full'), true)) {
        throw new RuntimeException('Database configuration is incomplete');
    }
    $dsn = "pgsql:host={$host};port={$port};dbname={$name};sslmode={$sslmode}";
    $pdo = new PDO($dsn, $user, $password, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ));
    return new DbConnection($pdo);
}
