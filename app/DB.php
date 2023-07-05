<?php

namespace App;

use Exception;
use PDO;
use PDOException;

class DB
{
    protected $table_name = '';
    protected $table_column = '';
    protected $table_column_count = '';
    protected $table_operation = '';
    protected $table_where = '';
    protected $table_order_limit_offset = '';
    protected $table_query_data = [];
    protected $table_query = '';

    protected PDO $db;

    function __construct(string $table_name)
    {
        $this->table_name = $table_name;
    }

    static public function table(string $name)
    {
        $qb = new DB($name);
        return $qb;
    }

    /**
     * Query
     * @param string $sql SQL statement
     * 
     * @param array $data query data
     * 
     * @param boolean $single return fetch or fetchAll
     * 
     * @return array
     */
    public static function query(string $sql, $data = [], $single = false)
    {
        // get Database info from ENV
        $DB_HOST = $_ENV["DB_HOST"] ?? "";
        $DB_PORT = $_ENV["DB_PORT"] ?? "";
        $DB_DATABASE = $_ENV["DB_DATABASE"] ?? "";
        $DB_USERNAME = $_ENV["DB_USERNAME"] ?? "";
        $DB_PASSWORD = $_ENV["DB_PASSWORD"] ?? "";

        try {
            $db = new PDO("mysql:host=$DB_HOST;dbname=$DB_DATABASE", $DB_USERNAME, $DB_PASSWORD);

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt = $db->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $pe) {
            throw new Exception("Could not connect to the database $DB_DATABASE :" . $pe->getMessage());
        }

        if ($single)
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        else
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * Select
     * @param string|array $column columns name
     * 
     * @return self
     */
    public function select(string|array $column = "*")
    {
        $this->table_operation = "select";

        // if string
        if (!is_array($column)) {
            $this->table_column = $column;
            return $this;
        }

        // if array
        $column_string = "";
        for ($i = 0; $i < count($column); $i++) {
            if ($i != 0)
                $column_string .= ", ";

            $column_string .= "`" . $column[$i] . "`";
        }

        $this->table_column = $column_string;

        return $this;
    }

    /**
     * Delete
     * 
     * @return boolean true or false
     */
    public function delete()
    {
        $this->table_operation = "delete";
        return $this->run();
    }

    /**
     * Update
     * 
     * @param string|array $column column and value
     * 
     * @return boolean true or false
     */
    public function update(string|array $column)
    {
        $this->table_operation = "update";

        // if string
        if (!is_array($column)) {
            $this->table_column = $column;
            return $this->run();
        }

        // if array
        $column_string = "";
        $i = 0;
        $temp = [];
        foreach ($column as $key => $value) {
            if ($i != 0)
                $column_string .= ", ";

            $column_string .= "`" . $key . "` = ?";
            $i++;
            $temp[] = $value;
        }
        $this->table_query_data = array_merge($temp, $this->table_query_data);

        // add updated time stamp
        $column_string .= ", `updated_at` = now() ";

        $this->table_column = $column_string;

        return $this->run();
    }

    /**
     * Insert
     * @param string|array $column column and value
     * 
     * @return int insert id
     */
    public function insert(string|array $column)
    {
        $this->table_operation = "insert";

        // if string
        if (!is_array($column)) {
            $this->table_column = $column;
            return $this->run();
        }

        // if array
        $column_string = "";
        $column_query = "";
        $i = 0;
        $temp = [];
        foreach ($column as $key => $value) {
            if ($i != 0) {
                $column_string .= ", ";
                $column_query .= ", ";
            }

            $column_string .= " `" . $key . "`";
            $column_query .= " ?";
            $i++;
            $temp[] = $value;
        }
        $this->table_query_data = array_merge($temp, $this->table_query_data);

        // add created time stamp
        $column_string .= ", `created_at` ";
        $column_query .= ", now() ";

        $this->table_column = $column_string;
        $this->table_query = $column_query;

        return $this->run();
    }

    /**
     * Where
     * @param string $column table column name
     * 
     * @param string|int $value column value
     * 
     * @param string $operator where operation
     *  =, !=, >, <, >=, <=
     */
    public function where(string $column, $value, $operator = "=")
    {
        if (trim($this->table_where) != "")
            $this->table_where .= " AND ";

        $this->table_where .= " `" . $column . "` " . $operator . " ?";
        array_push($this->table_query_data, $value);

        return $this;
    }

    public function andWhere(string $column, $value, $operator = "=")
    {
        $this->table_where .= " AND `" . $column . "` " . $operator . " ?";
        array_push($this->table_query_data, $value);

        return $this;
    }

    public function orWhere(string $column, $value)
    {
        $this->table_where .= " OR `" . $column . "` = ?";
        array_push($this->table_query_data, $value);

        return $this;
    }

    public function whereLike(string $column, $value)
    {
        $this->table_where .= " `" . $column . "` LIKE ?";
        array_push($this->table_query_data, $value);

        return $this;
    }

    /**
     *  Order By
     * column => column name
     * type => DESC, ASC
     */
    public function orderBy(string $column, $type = "ASC")
    {
        $this->table_order_limit_offset .= " ORDER BY `" . $column . "` " . $type;

        return $this;
    }

    public function limit(int $value = 5)
    {
        $this->table_order_limit_offset .= " LIMIT " . $value . " ";

        return $this;
    }

    public function offset(int $value = 0)
    {
        $this->table_order_limit_offset .= " OFFSET " . $value . " ";

        return $this;
    }

    /**
     * Get Return Array
     */
    function get()
    {
        if ($this->table_operation != 'select')
            throw new Exception("Invalid Table Operation");

        return $this->run() ?? [];
    }

    /**
     * First row
     * 
     * @return boolean|array false if not found | data array on success
     */
    function first()
    {
        if ($this->table_operation != 'select')
            throw new Exception("Invalid Table Operation");

        $temp = $this->run();

        if (!$temp)
            return false;

        if (count($temp) < 1)
            return false;

        return $temp[0];
    }

    /**
     * Last row
     * 
     * @return boolean|array false if not found | data array on success
     */
    function last()
    {
        if ($this->table_operation != 'select')
            throw new Exception("Invalid Table Operation");

        $temp = $this->run();

        if (!$temp)
            return false;

        if (count($temp) < 1)
            return false;

        return $temp[count($temp) - 1];
    }

    /**
     * Count of row
     * 
     * @return int number of match the query
     */
    public function count(string $column = "*")
    {
        $this->table_column = " COUNT( ";

        if ($column != "")
            $this->table_column .= $column;

        $this->table_column .= " ) AS total";

        $data = $this->run();

        if (!count($data))
            return 0;

        return $data[0]['total'] ?? 0;
    }

    public function run()
    {
        // echo $this->table_operation;
        // make database connection
        switch ($this->table_operation) {
            case 'select':
                $sql =   "SELECT " . $this->table_column . " FROM `" . $this->table_name . "` ";
                if (trim($this->table_where) != "")
                    $sql .= " WHERE " . $this->table_where;
                if (trim($this->table_order_limit_offset) != "")
                    $sql .= $this->table_order_limit_offset;
                break;

            case 'delete':
                if (trim($this->table_where) == "")
                    throw new Exception("Mass Row Delete Not Allow");

                $sql =   "DELETE FROM `" . $this->table_name . "` WHERE " . $this->table_where;
                break;

            case 'update':
                if (trim($this->table_where) == "")
                    throw new Exception("Mass Row Update Not Allow");

                $sql =   "UPDATE `" . $this->table_name . "` SET " . $this->table_column . " WHERE " . $this->table_where;
                break;

            case 'insert':
                $sql =   "INSERT INTO `" . $this->table_name . "` ( " . $this->table_column . " ) VALUES ( " . $this->table_query . " )";
                break;
                // INSERT INTO `ami`(`id`, `name`, `age`) VALUES ('[value-1]','[value-2]','[value-3]')

            default:
                throw new Exception("Invalid Table Operation");
        }
        // Utils::dd($sql);
        // Utils::dd($this->table_query_data);
        // echo "============<br>";
        // exit;

        // get Database info from ENV
        $DB_HOST = $_ENV["DB_HOST"] ?? "";
        $DB_PORT = $_ENV["DB_PORT"] ?? "";
        $DB_DATABASE = $_ENV["DB_DATABASE"] ?? "";
        $DB_USERNAME = $_ENV["DB_USERNAME"] ?? "";
        $DB_PASSWORD = $_ENV["DB_PASSWORD"] ?? "";

        try {
            $db = new PDO("mysql:host=$DB_HOST;dbname=$DB_DATABASE", $DB_USERNAME, $DB_PASSWORD);

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt = $db->prepare($sql);
            $stmt->execute($this->table_query_data);

            switch ($this->table_operation) {
                case 'select':
                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    break;

                case 'delete':
                    $data = $stmt->rowCount() ? true : false;
                    break;

                case 'update':
                    $data = $stmt->rowCount() ? true : false;
                    break;

                case 'insert':
                    $data = $db->lastInsertId();
                    break;
                default:
                    throw new Exception("Invalid Table Operation");
            }
        } catch (PDOException $pe) {
            throw new Exception($pe->getMessage());
        }

        return $data;
    }
}
