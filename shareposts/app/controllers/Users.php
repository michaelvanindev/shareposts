<?php
  class Users extends Controller {
    private $userModel;

    public function __construct() {
      $this->userModel = $this->model('User');
    }
    
    public function register() {
      // Check for POST
      if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Process the form

        // Get and sanitize POST data
        // Used null coalescing to avoid trim(null) error deprecated
        $data = [
          'name' => filter_input(INPUT_POST, 'name'),
          'email' => trim(filter_input(INPUT_POST, 'email') ?? ''),
          'password' => trim(filter_input(INPUT_POST, 'password') ?? ''),
          'confirm_password' => trim(filter_input(INPUT_POST, 'confirm_password') ?? ''),
          'name_error' => '',
          'email_error' => '',
          'password_error' => '',
          'confirm_password_error' => ''
        ];

        // Validate email
        if(empty($data['email'])) {
          $data['email_error'] = 'Please enter email.';
        } else {
          // Check email
          if($this->userModel->findUserByEmail($data['email'])) {
            $data['email_error'] = 'Email is already taken.';
          }
        }
        
        // Validate name
        if(empty($data['name'])) {
          $data['name_error'] = 'Please enter name.';
        }

        // Validate password
        if(empty($data['password'])) {
          $data['password_error'] = 'Please enter password.';
        } elseif(strlen($data['password']) < 6) {
          $data['password_error'] = 'Password must be at least 6 characters.';
        }

        // Validate confirm password
        if(empty($data['confirm_password'])) {
          $data['confirm_password_error'] = 'Please confirm password.';
        } else {
          if($data['password'] != $data['confirm_password']) {
            $data['confirm_password_error'] = 'Passwords do not match.';
          }
        }

        // Make sure no errors
        if(empty($data['email_error']) && empty($data['name_error']) && empty($data['password_error']) && empty($data['confirm_password_error'])) {
          // Validated
          
          // Hash the password
          $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

          // Register user
          if($this->userModel->register($data)) {
            flash('register_success', 'You are registered and can log in.');
            redirect('users/login');
          } else {
            die('Something went wrong.');
          }

        } else {
          // Load view with errors
          $this->view('users/register', $data);
        }

      } else {
        // Initialize data
        $data = [
          'name' => '',
          'email' => '',
          'password' => '',
          'confirm_password' => '',
          'name_error' => '',
          'email_error' => '',
          'password_error' => '',
          'confirm_password_error' => ''
        ];

        // Load the view
        $this->view('users/register', $data);
      }
    }

    public function login() {
      // Check for POST
      if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Process the form

        // Initialize data
        // Used null coalescing to avoid trim(null) error deprecated
        $data = [
          'email' => trim(filter_input(INPUT_POST, 'email') ?? ''),
          'password' => trim(filter_input(INPUT_POST, 'password') ?? ''),
          'email_error' => '',
          'password_error' => ''
        ];

        // Validate email
        if(empty($data['email'])) {
          $data['email_error'] = 'Please enter email.';
        }
        
        // Validate password
        if(empty($data['password'])) {
          $data['password_error'] = 'Please enter password.';
        }

        // Check for user/email
        if($this->userModel->findUserByEmail($data['email'])) {
          // User found
        } else {
          $data['email_error'] = 'No user found';
        }

        // Make sure no errors
        if(empty($data['email_error']) && empty($data['password_error'])) {
          // Validated
          // Check and set logged in user
          $loggedInUser = $this->userModel->login($data['email'], $data['password']);

          if($loggedInUser) {
            // Create session
            $this->createUserSession($loggedInUser);
          } else {
            $data['password_error'] = 'Password incorrect.';
            $this->view('users/login', $data);
          }
        } else {
          // Load view with errors
          $this->view('users/login', $data);
        }

      } else {
        // Init data
        $data =[    
          'email' => '',
          'password' => '',
          'email_error' => '',
          'password_error' => ''
        ];

        // Load view
        $this->view('users/login', $data);
      }
    }

    public function createUserSession($user) {
      // Set session user variables (these come from the row that was returned in the model)
      $_SESSION['user_id'] = $user->id;
      $_SESSION['user_email'] = $user->email;
      $_SESSION['user_name'] = $user->name;
      redirect('posts');
    }

    public function logout() {
      unset($_SESSION['user_id']);
      unset($_SESSION['user_email']);
      unset($_SESSION['user_name']);
      session_destroy();
      redirect('users/login');
    }
  }