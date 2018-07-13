<?php

class myPDO extends PDO
{
    private $_dsn = 'mysql:host=localhost;dbname=message';
    private $_user = 'sukamo';
    private $_password = 'toloveru1121';
    private $_encode = 'utf8';
    private $_stmt;
    private $_data = [];

    //__set __get用法要了解一下
    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }
        return false;
    }

    public function __construct()
    {
        try {
            parent::__construct($this->_dsn, $this->_user, $this->_password);
            $this->_setEncode();
        } catch (Exception $ex) {
            print_r($ex);
        }
    }

    private function _setEncode()
    {
        $this->query("SET NAMES '{$this->_encode}");
    }

    //搜尋功能＋bindValue
    public function bindQuery($sql, array $bind = [])
    {
        $this->_stmt = $this->prepare($sql);
        $this->_bind($bind);
        $this->_stmt->execute();
        return $this->_stmt->fetchall();
    }

    public function bindQuery_noarray($sql)
    {
        $this->_stmt = $this->prepare($sql);
        $this->_stmt->execute();
        return $this->_stmt->fetchall();
    }

    private function _bind($bind)
    {
        foreach ($bind as $key => $value) {
            $this->_stmt->bindValue($key, $value, is_numeric($value)?PDO::PARAM_INT:PDO::PARAM_STR);
        }
    }
    public function error()
    {
        $error = $this->_stmt->errorInfo();
        echo 'errorCode:'.$error[0].'<br>';
        echo 'errorString:'.$error[2].'<br>';
    }

    public function getData()
    {
        return $this->_data;
    }

    public function getID()
    {
        $sql = "SELECT * FROM test ORDER BY id ASC";
        $query = $this->query($sql);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query;
    }

    //insert功能整合（包括bind）
    public function insert($table, array $param = [])
    {
        $data = array_merge($this->_data, $param);//若value是post進來，便和原本的data做合併
        $colume = array_keys($data);//設定欄位
        $values = [];
        $bind_data = [];
        //為bind做準備
        foreach ($data as $key => $value) {
            //time是吃function,不用bind value
            if ($key=="time") {
                $values[] = "$value";
            } else {
                $values[] = ":{$key}";//sql上的語法
                $bind_data[":{$key}"] = $value;//sql上的語法對應其value
            }
        }
        $sql = "INSERT INTO {$table} (".implode(',', $colume).") VALUE (".implode(',', $values).")";
        $this->_stmt = $this->prepare($sql);
        $this->_bind($bind_data);
        $this->_stmt->execute();
        //$this->lastInsertId();//回傳insert後的id
    }

    public function get_last_id()
    {
        return $this->lastInsertId();
    }

    //update功能整合（包括bind）
    public function update($table, array $param = [], $id = false)
    {
        //確認id是否有設置，沒有便返回false
        if ($id == false && ! ($id = $this->id)) {
            return false;
        }
        $data = array_merge($this->_data, $param);
        $bind_temp = [];
        $bind_value = [];
        foreach ($data as $key => $value) {
            //因為id不能被設置，所以要把id從array中移除
            if ($key != 'id') {
                $bind_temp[] = "{$key} = :{$key}";
                $bind_value[":{$key}"] = $value;
            }
        }
        $sql = "UPDATE {$table} SET ".implode(',', $bind_temp)." WHERE id = :id";
        $this->_stmt = $this->prepare($sql);
        $this->_bind($bind_value);
        //為id做bind
        $temp = [':id' => "$id"];
        $this->_bind($temp);
        $this->_stmt->execute();
    }

    public function delete($table, array $param = [])
    {
        if (!isset($param)) {
            return false;
        }
        foreach ($param as $key => $value) {
            $temp = [':id' => "$value"];
            $sql = "DELETE FROM {$table} WHERE id = :id";
            $this->_stmt = $this->prepare($sql);
            $this->_bind($temp);
            $this->_stmt->execute();
        }
    }
}
