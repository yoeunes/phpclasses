<?php

namespace App\Classes;

class Crud {
    private $bdd = null;
    protected $variables = null;
    protected $pk = null;
    protected $table = null;

    public function __construct(DataBase $bdd, $table = null, $pk = null, $data = array ()) {
        $this->bdd = $bdd;
        $this->variables = $data;
        $this->table = $table;
        $this->pk = $pk;
    }

    public function __set($name, $value) {
        if (strtolower($name) === $this->pk) {
            $this->variables[ $this->pk ] = $value;
        } else {
            $this->variables[ $name ] = $value;
        }
    }

    public function __get($name) {
        if (is_array($this->variables)) {
            if (array_key_exists($name, $this->variables)) {
                return $this->variables[ $name ];
            }
        }

        return null;
    }

    public function getArrayVariables() {
        return $this->variables;
    }

    public function create() {
        return $this->bdd->insert($this->table, $this->variables);
    }

    public function delete($id = "") {
        $id = ( empty( $this->variables[ $this->pk ] ) ) ? $id : $this->variables[ $this->pk ];

//        if (empty( $id )) return false;

        return $this->bdd->delete($this->table, $this->pk . " = " . $id);
    }

    public function save($id = "") {
        $id = ( empty( $this->variables[ $this->pk ] ) ) ? $id : $this->variables[ $this->pk ];

//        if (empty( $id )) return false;

        return $this->bdd->update($this->table, $this->variables, $this->pk . " = " . $id);
    }

    public function search($fields = array (), $sort = array ()) {
        $bindings = empty( $fields ) ? $this->variables : $fields;

        $sql = "SELECT * FROM " . $this->table;

        if (!empty( $bindings ) && is_array($bindings)) {
            $fieldsvals = array ();
            $columns = array_keys($bindings);
            foreach ($columns as $column) {
                $fieldsvals[] = $column . " = :" . $column;
            }
            $sql .= " WHERE " . implode(" AND ", $fieldsvals);
        }

        if (!empty( $sort ) && is_array($bindings)) {
            $sortvals = array ();
            foreach ($sort as $key => $value) {
                $sortvals[] = $key . " " . $value;
            }
            $sql .= " ORDER BY " . implode(", ", $sortvals);
        }

        return $this->bdd->run($sql, $bindings);
    }

    public function first($fields = array (), $sort = array ()) {
        $bindings = empty( $fields ) ? $this->variables : $fields;

        $sql = "SELECT * FROM " . $this->table;

        if (!empty( $bindings ) && is_array($bindings)) {
            $fieldsvals = array ();
            $columns = array_keys($bindings);
            foreach ($columns as $column) {
                $fieldsvals[] = $column . " = :" . $column;
            }
            $sql .= " WHERE " . implode(" AND ", $fieldsvals);
        }

        if (!empty( $sort ) && is_array($sort)) {
            $sortvals = array ();
            foreach ($sort as $key => $value) {
                $sortvals[] = $key . " " . $value;
            }
            $sql .= " ORDER BY " . implode(", ", $sortvals);
        }

        $result = $this->bdd->run($sql, $bindings);
        if ($result && is_array($result)) return $result[ 0 ];
        else return $result;
    }

    public function all() {
        return $this->bdd->select($this->table);
    }

    public function min($field) {
        if ($field) {
            $result = $this->bdd->select($this->table, null, null, 'min(' . $field . ')');
            if ($result && is_array($result) && isset( $result[ 0 ][ 'min(' . $field . ')' ] )) return $result[ 0 ][ 'min(' . $field . ')' ];
        }

        return null;
    }

    public function max($field) {
        if ($field) {
            $result = $this->bdd->select($this->table, null, null, 'max(' . $field . ')');
            if ($result && is_array($result) && isset( $result[ 0 ][ 'max(' . $field . ')' ] )) return $result[ 0 ][ 'max(' . $field . ')' ];
        }

        return null;
    }

    public function avg($field) {
        if ($field) {
            $result = $this->bdd->select($this->table, null, null, 'avg(' . $field . ')');
            if ($result && is_array($result) && isset( $result[ 0 ][ 'avg(' . $field . ')' ] )) return $result[ 0 ][ 'avg(' . $field . ')' ];
        }

        return null;
    }

    public function sum($field) {
        if ($field) {
            $result = $this->bdd->select($this->table, null, null, 'sum(' . $field . ')');
            if ($result && is_array($result) && isset( $result[ 0 ][ 'sum(' . $field . ')' ] )) return $result[ 0 ][ 'sum(' . $field . ')' ];
        }

        return null;
    }

    public function count($field) {
        if ($field) {
            $result = $this->bdd->select($this->table, null, null, 'count(' . $field . ')');
            if ($result && is_array($result) && isset( $result[ 0 ][ 'count(' . $field . ')' ] )) return $result[ 0 ][ 'count(' . $field . ')' ];
        }

        return null;
    }

    public function find($id = "") {
        $id = ( empty( $this->variables[ $this->pk ] ) ) ? $id : $this->variables[ $this->pk ];
//        if(empty($id)) return null;
//        $result = $this->bdd->select($this->table, $this->pk . " = " . $id)[ 0 ];
        $result = $this->bdd->select($this->table, $this->pk . " = " . $id);

        return $this->variables = ( $result !== null && is_array($result) && isset( $result[ 0 ] ) ) ? $result[ 0 ] : null;
    }

    public function run($sql, $bind = "") {
        return $this->bdd->run($sql, $bind);
    }

    public function selectFromOtherTable($table, $where = "", $bind = "", $fields = "*") {
        return $this->bdd->select($table, $where, $bind, $fields);
    }

    public function insertIntoOtherTable($table, $info) {
        return $this->bdd->insert($table, $info);
    }

    public function deleteFromOtherTable($table, $where, $bind = "") {
        return $this->bdd->delete($table, $where, $bind);
    }

    public function updateOtherTable($table, $info, $where, $bind = "") {
        return $this->bdd->update($table, $info, $where, $bind);
    }

    public function selectFirstFromOtherTable($table, $where = "", $bind = "", $fields = "*") {
        $result = $this->bdd->select($table, $where, $bind, $fields);
        if ($result == is_array($result)) return $result[ 0 ];
        else return $result;
    }

}