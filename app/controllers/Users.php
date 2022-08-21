<?php
class Users extends Controller
{
    public function __construct()
    {
        $this->userModel = $this->loadModel('User');
    }

    public function index()
    {
        redirect('users/register');
    }

    public function register()
    {
        // Init Data
        $data = [
            'name' => '',
            'email' => '',
            'password' => '',
            'confirm_password' => '',
            'name_err' => '',
            'email_err' => '',
            'password_err' => '',
            'confirm_password_err' => '',
            'active' => 'register',
        ];
        //Check For POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            // Sanititze post data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            // Filtered form data
            $data['name'] = trim($_POST['name']);
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);
            $data['confirm_password'] = trim($_POST['confirm_password']);

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            } else if ($this->userModel->findUserByEmail($data['email'])) {
                // Check if user already exists
                $data['email_err'] = 'Email already taken';
            }

            // Validate Name
            if (empty($data['name'])) {
                $data['name_err'] = 'Please enter name';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            } else if (strlen($data['password']) < 6) {
                $data['password_err'] = 'Password must be atleast 6 characters';
            }

            // Validate Confirm Password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'Please confirm password';
            } else if ($data['password'] != $data['confirm_password']) {
                $data['password_err'] = 'Passwords do not match';
            }

            // Making sure that errors are empty
            if (empty($data['name_err']) && empty($data['email_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
                // Validated
                // Hash the password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                // Register User
                if ($this->userModel->register($data)) {
                    flash('registeration_successful', 'Registered Successfully! Please log in to continue');
                    redirect('users/login');
                } else {
                    die('Something went wrong.');
                }
            } else {
                //load view
                flash('registeration_failed', 'Please fill in valid information only', 'alert alert-danger');
                $this->loadView('users/register', $data);
            }
        } else {
            // Load view
            $this->loadView('users/register', $data);
        }
    }

    public function login()
    {
        // Init Data
        $data = [
            'email' => '',
            'password' => '',
            'email_err' => '',
            'password_err' => '',
            'active' => 'login',
        ];

        //Check For POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process Form
            // Sanititze post data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            // Filtered form data
            $data['email'] = trim($_POST['email']);
            $data['password'] = trim($_POST['password']);
            $data['email_err'] = '';
            $data['password_err'] = '';

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'Please enter email';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'Please enter password';
            }

            // Check if user exists
            if ($this->userModel->findUserByEmail($data['email'])) {
                // User Found
            } else {
                flash('login_failed', 'Invalid Email or Password', 'alert alert-danger');
                redirect('users/login');
            }

            // Making sure that errors are empty
            if (empty($data['email_err']) && empty($data['password_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);
                if ($loggedInUser) {
                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    // Load view
                    flash('login_failed', 'Invalid Email or Password', 'alert alert-danger');
                    redirect('users/login');
                }
            } else {
                //load view
                flash('login_failed', 'Invalid Email or Password', 'alert alert-danger');
                $this->loadView('users/login', $data);
            }
        } else {
            // Load view
            $this->loadView('users/login', $data);
        }
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['email']);
        unset($_SESSION['user_name']);
        session_destroy();
        redirect('users/login');
    }

    public function createUserSession($user)
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['email'] = $user->email;
        $_SESSION['user_name'] = $user->name;

        flash('login_successfull', 'Hi ' . explode(' ', trim($user->name))[0] . ', Welcome to SharePosts', 'alert alert-success text-center');

        redirect('');
    }
}
