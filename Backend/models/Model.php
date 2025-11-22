<?php 

abstract class Model{

    protected static string $table;
    protected static string $primary_key = "id";

    public static function findById(mysqli $connection, int $id){
        $sql = sprintf("SELECT * FROM %s WHERE %s = ?",
                        static::$table,
                        static::$primary_key);

        $query = $connection->prepare($sql);
        $query->bind_param("i", $id);
        $query->execute();

        $data = $query->get_result()->fetch_assoc();
        return $data ? new static($data) : null;
    }
    
    public static function create(mysqli $connection, array $data){//$data = ['email' => 'mahdi', 'pass' => '123' ]
        
        if(empty($data) || !is_array($data)){
            throw new INVALIDARGUMENTEXCEPTION("Data must be a non-empty array");
        }

        $key = array_keys($data);// ("email", "pass")
        $value = array_values($data);//("mahdi, "123")
        $cols = implode(',', $key);//("email,pass");
        $placeholder = implode("," , array_fill( 0 , count($key) , '?'));
        $types = '';

        foreach($value as $v){
            if(is_int($v)){
              $types .= 'i';
            }else{
                $types .= 's';
            }
        }

        $sql = "INSERT INTO " . static::$table . "(" . $cols . ") VALUES ($placeholder)";
        $query = $connection->prepare($sql);
        $query->bind_param($types, ...$value);

        $query->execute();

        return $query->insert_id;
    }

    public static function update(mysqli $connection ,int $id,  array $data){
        
   if(empty($data) || !is_array($data)){
            throw new InvalidArgumentException('Data must be a non-empty array.');
        }
        $key = array_keys($data);//("name", "color", "year")
        $values = array_values($data); //("merc", "blue", "2025")


        $newx = [];
        foreach($key as $k){
            $newx[] = " $k = ?"; // ("name = ?","color = ?","year = ?") 
        }
        $setSql = implode(',', $newx);//("name = ?,color = ?,year = ?")
 
        $sql = "UPDATE " . static::$table . " SET $setSql WHERE " . static::$primary_key . " =?";
        $query = $connection->prepare($sql);
        $types = '';

        
            foreach ($values as $v) {
            if (is_int($v)) {
                $types .= 'i';
            }else {
                $types .= 's';
            }
        }
        $types .= 'i'; // for id
        $bindValues = array_merge($values, [$id]);
        $query->bind_param($types, ...$bindValues);
        $query->execute();
        $query->close();
    }

    public static function delete(mysqli $connection, int $id){


        $sql = sprintf("DELETE FROM %s WHERE %s = ?",
                static::$table,
                static::$primary_key);
        $query = $connection->prepare($sql);
        $query->bind_param("i", $id);
        $query->execute();

        if(!$query){return "fail to delete";
        }
    }

}
?>