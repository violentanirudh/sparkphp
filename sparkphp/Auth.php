<?php

namespace SparkPHP;

// Authentication class
class Auth
{
    protected $db; // Database connection
    protected $jwt_secret; // JWT secret key

    // Constructor: set database and JWT secret
    public function __construct($db, $jwt_secret)
    {
        $this->db = $db;
        $this->jwt_secret = $jwt_secret;
    }

    // Register a new user
    public function signup($name, $email, $password, $role = 'user', $extra = [])
    {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $exists = $this->db->table('users')->where('email = ?', [ $email ])->first();
        if ($exists) return ['error' => 'Email already exists'];

        $fields = [
            'name' => $name,
            'email' => $email,
            'password' => $hashed,
            'role' => $role,
            'email_verified' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $fields = array_merge($fields, $extra);

        $id = $this->db->table('users')->insert($fields)->execute();
        return $id;
    }

    // User login
    public function signin($email, $password)
    {
        $user = $this->db->table('users')->where('email = ? AND email_verified = 1', [ $email ])->first();
        if (!$user || !password_verify($password, $user['password'])) {
            return ['error' => 'Invalid credentials'];
        }
        unset($user['password']);
        $payload = [
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'expire' => time() + 3600*24*7
        ];
        $jwt = JWT::create($payload, $this->jwt_secret);
        return ['token' => $jwt, 'user' => $user];
    }

    // Validate JWT token
    public function validate($token)
    {
        $payload = JWT::verify($token, $this->jwt_secret);
        if (!$payload || ($payload['exp'] ?? 0) < time()) return false;
        return $payload;
    }

    // Check if user has required roles
    public function authorize($payload, $roles = [])
    {
        if (!$payload) return false;
        if (empty($roles)) return true;
        return in_array($payload['role'], $roles);
    }

    // Change user password
    public function change_password($user_email, $old, $new)
    {
        $user = $this->db->table('users')->where('email = ?', [ $user_email ])->first();
        if (!$user || !password_verify($old, $user['password'])) {
            return ['error' => 'Old password incorrect'];
        }
        $hashed = password_hash($new, PASSWORD_BCRYPT);
        $this->db->table('users')->where('email = ?', [ $user_email ])->update(['password' => $hashed]) -> execute();
        return true;
    }

    // Get or update user profile
    public function profile($user_email, $details = [])
    {
        if (empty($details)) {
            $user = $this->db->table('users')->where('email = ?', [ $user_email ])->first();
            if ($user) unset($user['password']);
            return $user;
        } else {
            unset($details['password']);
            $details['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('users')->where('id = ?', [ $user_email ])->update($details) -> execute();
            return true;
        }
    }

    // Handle email verification
    public function verification($email = null, $token = null)
    {
        // Generate verification token
        if ($email !== null && $token === null) {
            $user = $this->db->table('users')->where('email = ?', [ $email ])->first();
            if (!$user) return ['error' => 'User not found'];
            $random = sha1($user['role'] . $user['name'] . $user['id'] . bin2hex(random_bytes(8)));
            $payload = [
                'email' => $user['email'],
                'hash'  => $random,
                'exp'   => time() + 900
            ];
            return JWT::create($payload, $this->jwt_secret);
        // Validate verification token
        } elseif ($email === null && $token !== null) {
            $payload = JWT::verify($token, $this->jwt_secret);
            if (!$payload || ($payload['exp'] ?? 0) < time()) {
                return ['error' => 'Invalid or expired verification link'];
            }
            $user = $this->db->table('users')->where('email = ?', [ $payload['email'] ])->first();
            if (!$user) {
                return ['error' => 'User not found'];
            }
            $this->db->table('users')->where('email = ?', [ $payload['email'] ])->update([
                'email_verified' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]) -> execute();
            return ['success' => true];
        }
        return ['error' => 'Invalid usage'];
    }

}
