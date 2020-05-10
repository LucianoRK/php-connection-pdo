<?php

class DB {

    private static $instance;
    private $link;
    private $host = '';
    private $data_base_name = '';
    private $data_base_user = '';
    private $data_base_passwd = '';

    function __construct() {
        if (empty($this->link)) {
            try {
                $this->link = new PDO("mysql:host=" . $this->host . ';port=3306;dbname=' . $this->data_base_name, $this->data_base_user, $this->data_base_passwd);
                $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->link->query('SET NAMES utf8');
                $this->link->query('SET CHARACTER SET utf8');
            } catch (PDOException $exc) {
               return $exc;
            }
        }
    }

    static function getInstance($force_new_instance = false) {
        if ($force_new_instance) {
            return new DB();
        } else {
            if (!isset(self::$instance)) {
                $c = new DB();
                self::$instance = $c;
            }
            return self::$instance;
        }
    }

    function execute($query) {
        try {
            $sth = $this->link->prepare($query);
            $sth->execute();
        } catch (PDOException $exc) {
            return $exc;
        }
    }

    function fetch($query) {
        try {
            $sth = $this->link->query($query);
            return $sth->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $exc) {
            return $exc;
        }
    }

    function fetchAll($query) {
        try {
            $sth = $this->link->query($query);
            return $sth->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exc) {
            return $exc;
        }
    }

    function fetchAttr($query, $attribute) {
        try {
            $sth = $this->link->query($query);
            $result = $sth->fetch(PDO::FETCH_ASSOC);
            if (isset($result[$attribute])) {
                return $result[$attribute];
            } else {
                throw new Exception("Atributo {$attribute} não encontrado");
            }
        } catch (PDOException $exc) {
            return $exc;
        }
    }

    function rowCount($query) {
        $sth = $this->link->query($query);
        return $sth->rowCount();
    }

    function lastId() {
        $sth = $this->link->query("SELECT LAST_INSERT_ID()");
        return $sth->fetchColumn();
    }

    function beginTransaction() {
        $this->link->beginTransaction();
    }

    function commit() {
        $this->link->commit();
    }

    function rollback() {
        $this->link->rollBack();
    }

}