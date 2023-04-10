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

    public static function query(string $sql, $data = [])
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

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function select(string|array $column)
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

    public function delete()
    {
        $this->table_operation = "delete";
        return $this;
    }

    public function update(string|array $column)
    {
        $this->table_operation = "update";

        // if string
        if (!is_array($column)) {
            $this->table_column = $column;
            return $this;
        }

        // if array
        $column_string = "";
        $i = 0;
        foreach ($column as $key => $value) {
            if ($i != 0)
                $column_string .= ", ";

            $column_string .= "`" . $key . "` = ?";
            $i++;
            array_push($this->table_query_data, $value);
        }

        $this->table_column = $column_string;

        return $this;
    }

    public function insert(string|array $column)
    {
        $this->table_operation = "insert";

        // if string
        if (!is_array($column)) {
            $this->table_column = $column;
            return $this;
        }

        // if array
        $column_string = "";
        $column_query = "";
        $i = 0;
        foreach ($column as $key => $value) {
            if ($i != 0) {
                $column_string .= ", ";
                $column_query .= ", ";
            }

            $column_string .= " `" . $key . "`";
            $column_query .= " ?";
            $i++;
            array_push($this->table_query_data, $value);
        }

        $this->table_column = $column_string;
        $this->table_query = $column_query;

        return $this;
    }

    /**
     * Where
     * @param string column table column name
     * 
     * @param string|int value column value
     * 
     * @param string oparator where operation
     *  =, !=, >, <, >=, <=
     */
    public function where(string $column, $value, $operator = "=")
    {
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

    function query_builder()
    {
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
        } catch (PDOException $pe) {
            throw new Exception($pe->getMessage());
        }

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    }
}
