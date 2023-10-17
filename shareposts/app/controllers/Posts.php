<?php
  class Posts extends Controller {
    private $postModel;
    private $userModel;

    public function __construct() {
      if(!isLoggedIn()) {
        redirect('users/login');
      }

      $this->postModel = $this->model('Post');
      $this->userModel = $this->model('User');
    }

    public function index() {
      // Get posts
      $posts = $this->postModel->getPosts();

      $data = [
        'posts' => $posts
      ];


      $this->view('posts/index', $data);
    }

    public function add() {
      if($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Sanitize POST array
        $data = [
          'title' => trim(filter_input(INPUT_POST, 'title') ?? ''),
          'body' => trim(filter_input(INPUT_POST, 'body') ?? ''),
          'user_id' => $_SESSION['user_id'],
          'title_error' => '',
          'body_error' => ''
        ];

        // Validate data
        if(empty($data['title'])) {
          $data['title_error'] = 'Please enter a title';
        }

        if(empty($data['body'])) {
          $data['body_error'] = 'Please enter body text';
        }
  
        // Make sure no errors
        if(empty($data['title_error']) && empty($data['body_error'])) {
          // Validated
          if($this->postModel->addPost($data)) {
            flash('post_message', 'Post Added');
            redirect('posts');
          } else {
            die('Something went wrong.');
          }
        } else {
          // Load view with errors
          $this->view('posts/add', $data);
        }
      } else {
        $data = [
          'title' => '',
          'body' => ''
        ];

        $this->view('posts/add', $data);
      }
    }

    public function edit($id) {
      if($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Sanitize POST array
        $data = [
          'id' => $id,
          'title' => trim(filter_input(INPUT_POST, 'title') ?? ''),
          'body' => trim(filter_input(INPUT_POST, 'body') ?? ''),
          'user_id' => $_SESSION['user_id'],
          'title_error' => '',
          'body_error' => ''
        ];

        // Validate data
        if(empty($data['title'])) {
          $data['title_error'] = 'Please enter a title';
        }

        if(empty($data['body'])) {
          $data['body_error'] = 'Please enter body text';
        }
  
        // Make sure no errors
        if(empty($data['title_error']) && empty($data['body_error'])) {
          // Validated
          if($this->postModel->updatePost($data)) {
            flash('post_message', 'Post Updated');
            redirect('posts');
          } else {
            die('Something went wrong.');
          }
        } else {
          // Load view with errors
          $this->view('posts/edit', $data);
        }
      } else {
        // Get existing post from model
        $post = $this->postModel->getPostById($id);

        // Check for owner
        if($post->user_id != $_SESSION['user_id']) {
          redirect('posts');
        }
        $data = [
          'id' => $id,
          'title' => $post->title,
          'body' => $post->body
        ];

        $this->view('posts/edit', $data);
      }
    }

    public function show($id) {
      $post = $this->postModel->getPostById($id);
      $user = $this->userModel->getUserById($post->user_id);

      $data = [
        'post' => $post,
        'user' => $user
      ];

      $this->view('posts/show', $data);
    }

    public function delete($id) {
      if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get existing post from model
        $post = $this->postModel->getPostById($id);

        // Check for owner
        if($post->user_id != $_SESSION['user_id']) {
          redirect('posts');
        }
        
        if($this->postModel->deletePost($id)) {
          flash('post_message', 'Post Removed');
          redirect('posts');
        } else {
          die('Something went wrong.');
        }
      } else {
        redirect('posts');
      }
    }
  }