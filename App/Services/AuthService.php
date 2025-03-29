<?php

namespace App\Services;

use App\Helper\Validator;
use App\Model\UserModel;

class AuthService {
    public function __construct(
        private UserModel $model,
        private Validator $validator
    ) {
    }

    public function validateRegistration($data) {
        // Update validation rules to match the form fields
        $this->validator->setAliases([
            'username' => 'username',
            'confirm_password' => 'confirm password'
        ]);

        $this->validator->make(
            $data,
            [
                'username' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'confirm_password' => 'required|same:password',
            ]
        );

        if ($this->validator->fails()) {
            return ['success' => false, 'errors' => $this->validator->getErrors()];
        }

        // Check if email already exists
        if ($this->model->emailExists($data['email'])) {
            return ['success' => false, 'errors' => ['email' => 'L\'adresse email est déjà utilisée']];
        }

        // Check if username already exists
        if ($this->model->usernameExists($data['username'])) {
            return ['success' => false, 'errors' => ['username' => 'L\'utilisateur est déjà utilisé']];
        }

        return ['success' => true, 'data' => $data];
    }

    public function register($data) {
        $result = $this->model->createUser(
            $data['username'], 
            $data['email'], 
            $data['password']
        );

        return $result;
    }

    public function validateLogin($data) {
        $this->validator->make(
            $data,
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );

        if ($this->validator->fails()) {
            return ['success' => false, 'errors' => $this->validator->getErrors()];
        }

        return ['success' => true];
    }

    public function login($data) {
        if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
            // Protection contre les attaques par force brute
            if ($this->isLoginThrottled($data['email'])) {
                return [
                    'success' => false,
                    'errors' => ['email' => 'Trop de tentatives de connexion. Veuillez réessayer dans 15 minutes.']
                ];
            }
            
            $user = $this->model->getUserByEmail($data['email']);
            
            if ($user && password_verify($data['password'], $user['password_hash'])) {
                // Réinitialiser le compteur de tentatives
                $this->resetLoginAttempts($data['email']);
                
                // Store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['authenticated'] = true;
                
                return ['success' => true, 'user' => $user];
            }
            
            // Enregistrer la tentative échouée
            $this->recordFailedLogin($data['email']);
            
            return [
                'success' => false,
                'errors' => ['email' => 'L\'adresse email ou le mot de passe est invalide']
            ];
        }

        return [
            'success' => false,
            'errors' => ['csrf' => 'L\'authentification CSRF est invalide']
        ];
    }
    
    /**
     * Enregistre une tentative de connexion échouée
     */
    private function recordFailedLogin($email) {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = md5($email . $ip);
        
        if (!isset($_SESSION['login_attempts'][$key])) {
            $_SESSION['login_attempts'][$key] = [
                'count' => 0,
                'time' => time()
            ];
        }
        
        $_SESSION['login_attempts'][$key]['count']++;
        $_SESSION['login_attempts'][$key]['time'] = time();
    }
    
    /**
     * Vérifie si les tentatives de connexion doivent être limitées
     */
    private function isLoginThrottled($email) {
        if (!isset($_SESSION['login_attempts'])) {
            return false;
        }
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = md5($email . $ip);
        
        if (!isset($_SESSION['login_attempts'][$key])) {
            return false;
        }
        
        $attempts = $_SESSION['login_attempts'][$key];
        
        // Si plus de 15 minutes se sont écoulées, réinitialiser le compteur
        if (time() - $attempts['time'] > 900) {
            unset($_SESSION['login_attempts'][$key]);
            return false;
        }
        
        // Limiter à 5 tentatives en 15 minutes
        return $attempts['count'] >= 5;
    }
    
    /**
     * Réinitialise le compteur de tentatives de connexion
     */
    private function resetLoginAttempts($email) {
        if (!isset($_SESSION['login_attempts'])) {
            return;
        }
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = md5($email . $ip);
        
        if (isset($_SESSION['login_attempts'][$key])) {
            unset($_SESSION['login_attempts'][$key]);
        }
    }
}