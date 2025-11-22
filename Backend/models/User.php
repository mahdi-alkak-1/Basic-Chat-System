<?php 

require_once __DIR__ . '/Model.php';
class User extends Model{
    private int $id;
    private string $email;
    private string $password;
    private ? string $auth_token;
    private string $created_at;


    protected static string $table = "users";

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->email = $data["email"];
        $this->password = $data["password"];
        $this->auth_token = $data["auth_token"] ?? null;
        $this->created_at = $data["created_at"];
    }
    
    public static function findByEmail($connection, $email){
        $sql = sprintf("SELECT * FROM users WHERE email = ? LIMIT 1");
        $query = $connection->prepare($sql);
        $query->bind_param('s', $email);
        $query->execute ();

        $data = $query->get_result()->fetch_assoc();
        
        return  $data ? new User($data) : null ;    
    }
    
    // public static function updateToken(){  //// i may implement it later its impo for security issues

    // }


    public function getId(){
        return $this->id;
    }

    public function setEmail(string $email){
        $this->email = $email;
    }
    public function getEmail(){
        return $this->email;
    }

     public function setPassword(string $password){
        $this->password = $password;
    }
    public function getPassword(){
        return $this->password;
    }

    public function setCreatedAt(string $created){
        $this->created_at = $created;
    }
    public function getCreatedAt(){
        return $this->created_at;
    }
    public function setAuthToken(string $token){
        $this->auth_token = $token;
    }
    public function getAuthToken(){
        return $this->auth_token;
    }

    public function __toString(){
        return $this->id . " | " . $this->email . " | " . $this->password. " | " . $this->created_at .  " | " . $this->auth_token;
    }
    
    public function toArray(){
        return ["id" => $this->id, "email" => $this->email, "password" => $this->password,"created_at" => $this->created_at, "auth_token" => $this->auth_token];
    }
}


?>