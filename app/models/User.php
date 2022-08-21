<?php
class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Register User
    public function register($data)
    {
        // Prepare query
        $this->db->query("INSERT INTO users (name, email, password) VALUES(:name, :email, :password)");
        // Bind params
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    //Login User
    public function login($email, $password)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        $user = $this->db->fetchOne();
        $hashed_password = $user->password;
        if (password_verify($password, $hashed_password)) {
            return $user;
        } else {
            return false;
        }
    }

    // Find user by email
    public function findUserByEmail($email)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        $user = $this->db->fetchOne();
        if ($this->db->count() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
