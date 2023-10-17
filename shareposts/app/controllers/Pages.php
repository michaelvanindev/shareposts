<?php

// namespace app\controllers;

class Pages extends Controller {
    // private $postModel;

    public function __construct(){
      // load the model
      // $this->postModel = $this->model('Post');
    }

    public function index() {
      // $posts = $this->postModel->getPosts();

      if(isLoggedIn()) {
        redirect('posts');
      }

      $data = [
        'title' => 'SharePosts',
        'description' => 'Simple social network built on a custom MVC PHP framework.'
        // pass posts variable into the view to be accessed in the view
        // 'posts' => $posts
      ];

      $this->view('pages/index', $data);
    }

    public function about() {
      $data = [
        'title' => 'About Us',
        'description' => 'App to share posts with other users.'
      ];

      $this->view('pages/about', $data);
    }
  }