<?php

namespace App\Helpers\Classes;

use App\Helpers\Classes\CPanel;
use Exception;

class MysqlDB {

  private $host = 'localhost';
  private $database = null;
  private $username = null;
  private $password = '';

  private $connection;

  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#
  
  # Connection

  public function __construct($database = false, $username = false, $password = false)
  {
    $this->database = $database !== false ? $database : (is_null($database) ? null : config('database.connections.mysql.database'));
    $this->username = $username !== false ? $username : (is_null($username) ? null : config('database.connections.mysql.username'));
    $this->password = $password !== false ? $password : (is_null($password) ? null : config('database.connections.mysql.password'));
  }

  public function connect()
  {
    $this->connection = new \mysqli($this->host, $this->username, $this->password, $this->database);

    if ($this->connection->connect_error)
      die("Connection failed: " . $this->connection->connect_error);

    return $this->connection;
  }

  public function close()
  {
    return $this->connection->close();
  }

  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#

  # Getters

  public function getDatabase()
  {
    return $this->database;
  }

  public function getUsername()
  {
    return $this->username;
  }

  public function getPassword()
  {
    return $this->password;
  }

  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#
  
  # Queries / Prepared Statemnts

  public function query($sql)
  {
    $mysqli = $this->connect();
    $result = $mysqli->query($sql);
    $result = is_bool($result) ? $result : mysqli_fetch_all($result, MYSQLI_ASSOC);

    $this->close();
    return $result;
  }

  public function prepared_stmt($sql, $params = [])
  {
    foreach ($params as $key => $param) {
      if (is_array($param))
      {
        $params[$key] = addslashes(json_encode($param));
        $types[] = 's';
      }
      else
        $types[] = is_integer($param) ? 'i' : (is_double($param) ? 'd' : (is_string($param) ? 's' : 'b'));
    }

    $mysqli = $this->connect();
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param(isset($types) ? implode('', $types) : '', ...$params);
    $stmt->execute();

    $result = $stmt->get_result();
    $result = is_bool($result) ? $result : mysqli_fetch_all($result, MYSQLI_ASSOC);

    $this->close();
    return $result;
  }

  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#
  
  # DB (create / drop / empty / check-if-exists / ...)

  public function create($database = null)
  {
    if (!$database) $database = $this->database;
    return config('app.live')
      ? (new CPanel())->createDatabase($database)
      : $this->query("CREATE DATABASE IF NOT EXISTS $database");
  }

  public function drop($database = null)
  {
    if (!$database) $database = $this->database;
    return config('app.live')
      ? (new CPanel())->deleteDatabase($database)
      : $this->query("DROP DATABASE IF EXISTS $database");
  }

  public function empty()
  {
    self::dropDB($this->database, $this->username, $this->password);
    self::createDB($this->database, $this->username, $this->password);

    if (config('app.live'))
    {
      $cpanel = new CPanel();
      $cpanel->setAllPrivilegesOnDatabase($this->username, $this->database);
    }
  }

  public function exists($database = null)
  {
    $databases = $this->query('SHOW DATABASES');
    $databases = array_map(function ($db) { return $db['Database']; }, $databases);
    return in_array($database ?? $this->database, $databases);
  }

  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#
  
  # Users (add-user / delete-user / ...)

  public function deleteUser($username = null)
  {
    if (!$username) $username = $this->username;
    return config('app.live')
      ? (new CPanel())->deleteDatabaseUser($username)
      : null;
  }
  
  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#
  
  # Operations on db (import / export / ...)

  public function export($tables = '*', $except = ['business_types'], $filepath = null, $options = [])
  {
    $options = array_merge([
      'create_tables' => true,
    ], $options);

    if (!$filepath)
    {
      $dir = time();
      $filepath = public_path("storage/backups/$dir/" . $this->database . "-" . time() . ".sql");
    }    
    create_dir_if_not_exist(dirname($filepath));

    $db = $this->connect();
  
    if ($tables == '*')
    {
      if (config('app.live'))
      {
        exec("mysqldump -u {$this->username} -p{$this->password} {$this->database} > $filepath");
        return $filepath;
      }
      else $tables = $this->showTables();
    } else $tables = is_array($tables) ? $tables : explode(',', $tables);
    
    $contents = "SET FOREIGN_KEY_CHECKS = 0;\r\n\r\n";
  
    foreach ($tables as $table)
    {
      $result = $db->query("SELECT * FROM $table");
      
      if (!$result) continue;
      $numColumns = $result->field_count;

      if ($options['create_tables'])
      {
        $contents .= "DROP TABLE IF EXISTS $table;\r\n";
    
        $result2 = $db->query("SHOW CREATE TABLE $table");
        $row2 = $result2->fetch_row();
  
        $contents .= $row2[1] . ";\r\n";
      }

      $values = [];

      for ($i = 0; $i < $numColumns; $i++)
      {
        while ($row = $result->fetch_row())
        {
          $row_content = "(";
          for ($j = 0; $j < $numColumns; $j++)
          {
            $row_content .= isNull($row[$j]) ? 'NULL' : (isset($row[$j]) ? '"' . addslashes($row[$j]) . '"' : '""');
  
            if ($j < ($numColumns - 1))
              $row_content .= ',';
          }
          $row_content .= ")";
          $values[] = $row_content;
        }
      }

      if (count($values))
      {
        $columns = implode(',', array_map(fn($col) => "`{$col['Field']}`", $this->query("SHOW COLUMNS FROM $table")));
        $contents .= "INSERT INTO `$table` ($columns) VALUES\r\n" . implode(",\r\n", $values) . ";\r\n\r\n";
      }
    }
  
    $contents .= "SET FOREIGN_KEY_CHECKS = 1;";

    return file_put_contents($filepath, $contents) ? $filepath : null;
  }

  public function import($filepath)
  {
    if (!$this->exists())
      throw new Exception("Database '" . $this->database . "' does not exist.");

    if (!file_exists($filepath))
      throw new Exception("File '$filepath' does not exist.");

    $link = $this->connect();
  
    $tempLine = '';
    $lines = array_merge(['SET FOREIGN_KEY_CHECKS = 0;'], file($filepath), ['SET FOREIGN_KEY_CHECKS = 1;']);
  
    foreach ($lines as $line)
    {
      if (substr($line, 0, 2) == '--' || $line == '')
        continue;
  
      $tempLine .= $line;
      if (substr(trim($line), -1, 1) == ';')
      {
        mysqli_query($link, $tempLine) or print("Error in " . $tempLine . ":" . mysqli_error($link));
        $tempLine = '';
      }
    }

    return true;
  }

  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#
  
  # Structure comparison

  public function getStructureAssoc()
  {
    foreach ($this->showTables() as $table) {
      foreach ($this->query("DESCRIBE $table") as $col) {
        $structure[$table][$col['Field']] = implode(' ', array_filter([
          "`{$col['Field']}`",
          $col['Type'],
          $col['Null'] == 'NO' ? 'NOT NULL' : 'NULL',
          $col['Default'] ? "DEFAULT {$col['Default']}" : ($col['Null'] == 'NO' ? null : 'DEFAULT NULL'),
          $col['Key'] == 'PRI' ? 'PRIMARY KEY' : '',
          $col['Extra'],
        ]));
      }
      if ($arr = explode("\n", $this->query("SHOW CREATE TABLE $table")[0]['Create Table'] ?? '')) {
        array_pop($arr);
        array_shift($arr);
        $structure[$table]['CONSTRAINTS'] = array_map(fn($x) => trim($x), array_filter($arr, fn($y) => !startsWith(trim($y, ' '), '`')));
      }
    }
    return $structure;
  }

  public function compareStructure($other_database, $with_constraints = false)
  {
    $differenes = [];

    $this_structure = $this->getStructureAssoc();
    $other_structure = $other_database->getStructureAssoc();

    foreach ($this_structure as $table => $columns) {
      foreach (array_diff_key($columns, array_flip(['CONSTRAINTS'])) as $name => $struct) {
        if (!isset($other_structure[$table][$name]) || !in_array($name, array_keys($other_structure[$table]))) {
          $differenes[$this->database][$table][$name] = $struct ?? null;
          if ($value = $other_structure[$table][$name] ?? null) $differenes[$other_database][$table][$name] = $value;
        }
      }
      if ($with_constraints) {
        foreach ($columns['CONSTRAINTS'] as $struct) {
          if (!isset($other_structure[$table]['CONSTRAINTS']) || !in_array($struct, $other_structure[$table]['CONSTRAINTS']))
            $differenes[$this->database][$table]['CONSTRAINTS'][] = $struct;
        }
      }
    }

    
    # provide sql solution
    $sql = '';
    if ($current_differences = $differenes[$this->database] ?? null) {
      foreach ($current_differences as $table => $columns)
      {
        if (count(array_diff_key($columns, array_flip(['CONSTRAINTS']))))
        {
          $sql .= (isset($other_structure[$table]) ? "ALTER" : "CREATE") . " TABLE `$table` (\r\n";
          $arr = array_diff_key($columns, array_flip(['CONSTRAINTS']));
          foreach ($arr as $name => $struct) {
            $sql .= "\t" . trim($struct, ',') . (array_keys($arr)[count($arr) - 1] == $name ? '' : ',') . "\r\n";
          }
          $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\r\n\r\n";
        }
        if ($constraints = $columns['CONSTRAINTS'] ?? null) {
          foreach ($columns['CONSTRAINTS'] as $name => $struct) {
            $sql .= "ALTER TABLE `$table` ADD " . trim($struct, ',') . ";\r\n\r\n";
          }
        }
      }
    }

    return $differenes;
  }

  public function compareAllStructures($other_database, $with_constraints = true)
  {
    return array_merge(
      $this->compareStructure($other_database, $with_constraints),
      $other_database->compareStructure($this, $with_constraints),
    );
  }

  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#

  # Tables (truncate / ..)

  public function showTables()
  {
    return array_map(fn($result) => $result['Tables_in_' . $this->database], $this->query("SHOW TABLES"));
  }

  public function truncate(...$tables)
  {
    foreach ($tables as $table) {
      $this->query("DELETE FROM $table");
      $this->query("ALTER TABLE $table AUTO_INCREMENT = 1");
    }
  }

  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#

  # Columns

  public function columns($table, $except = [])
  {
    $except = is_string($except) ? explode(',', $except) : $except;
    $results = $this->query("SHOW COLUMNS FROM $table");
    return $results ? [...array_filter(array_map(fn ($col) => $col['Field'], $results), fn($col) => !in_array($col, $except))] : $results;
  }

  public function tablesContainingColumn($columns)
  {
    $columns = is_string($columns) ? explode(',', $columns) : $columns;
    $columns = implode(',', array_map(fn ($x) => "'$x'", $columns));
    $results = $this->query("
      SELECT DISTINCT TABLE_NAME 
      FROM INFORMATION_SCHEMA.COLUMNS
      WHERE COLUMN_NAME IN ($columns)
      AND TABLE_SCHEMA='{$this->database}'
    ");
    return $results ? array_map(fn($x) => $x['TABLE_NAME'], $results) : $results;
  }

  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#
  
  # CRUD

  private function castCRUD($where = [], $separator = 'AND', $concat = '=?')
  {
    $cols = [];
    $vals = [];
    $types = [];

    foreach ($where as $col => $val) {
      $cols[] = "`$col`$concat";
      $vals[] = $val === false ? 0 : ($val === true ? 1 : $val);
    }

    foreach ($vals as $key => $val) {
      if (is_array($val))
      {
        $vals[$key] = json_encode($val);
        $types[] = 's';
      }
      else
        $types[] = is_integer($val) ? 'i' : (is_double($val) ? 'd' : (is_string($val) || is_null($val) ? 's' : 'b'));
    }

    return [
      count($cols) ? implode("$separator", $cols) : null,
      count($types) ? implode('', $types) : null,
      $vals,
    ];
  }

  public function select($select, $from, $where = [])
  {
    $conn = $this->connect();
    
    [$query, $types, $values] = $this->castCRUD($where);
    $sql = "SELECT $select FROM $from WHERE " . ($query ?? 1) . ";";

    $stmt = $conn->prepare($sql) or die ("$sql : $conn->error");
    if ($types) $stmt->bind_param($types, ...$values) or die ("$sql : $stmt->error");
    $stmt->execute() or die ("$sql : $stmt->error");

    $result = $stmt->get_result();
    $result = is_bool($result) ? $result : mysqli_fetch_all($result, MYSQLI_ASSOC);

    $this->close();
    return $result;
  }

  public function insert($table, $data)
  {
    $conn = $this->connect();
    
    [$query, $types, $values] = $this->castCRUD($data, ", ", '');
    $sql = "INSERT INTO $table ($query) VALUES (" . implode(', ', array_fill(0, count($values), "?")) . ");";

    $stmt = $conn->prepare($sql) or die ("$sql : $conn->error");
    if ($types) $stmt->bind_param($types, ...$values) or die ("$sql : $stmt->error");
    $result = $stmt->execute() or die ("$sql : $stmt->error");

    $this->close();
    return $result;
  }

  public function update($table, $data, $where = [])
  {
    $conn = $this->connect();
    
    [$query, $types, $values] = $this->castCRUD($data, ',');
    [$where_query, $where_types, $where_values] = $this->castCRUD($where, ',');
    $sql = "UPDATE $table SET $query WHERE " . ($where_query ?? 1) . ";";

    $types .= $where_types ?? '';
    $values = array_merge($values, $where_values);

    $stmt = $conn->prepare($sql) or die ("$sql : $conn->error");
    if ($types) $stmt->bind_param($types, ...$values) or die ("$sql : $stmt->error");
    $result = $stmt->execute() or die ("$sql : $stmt->error");

    $this->close();
    return $result;
  }

  public function delete($table, $where)
  {
    $conn = $this->connect();
    
    [$query, $types, $values] = $this->castCRUD($where, ',');
    $sql = "DELETE FROM $table WHERE " . ($query ?? 1) . ";";

    $stmt = $conn->prepare($sql) or die ("$sql : $conn->error");
    
    if ($types) $stmt->bind_param($types, ...$values) or die ("$sql : $stmt->error");
    $result = $stmt->execute() or die ("$sql : $stmt->error");

    $this->close();
    return $result;
  }

  #=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#
  
  # static functions

  public static function createDB($database = null, $username = null, $password = null)
  {
    $conn = new MysqlDB(null, $username, $password);
    return $conn->create($database);
  }

  public static function dropDB($database = null, $username = null, $password = null)
  {
    $conn = new MysqlDB(null, $username, $password);
    return $conn->drop($database);
  }

  public static function compareStructures(MysqlDB $first_db, MysqlDB $second_db)
  {
    return $first_db->compareAllStructures($second_db);
  }
}