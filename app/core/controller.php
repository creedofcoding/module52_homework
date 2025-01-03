<?php
    class Controller 
    {
        protected $db;

        public $model;
        public $view;
        
        function __construct()
        {
            $this->model = new Model();
            $this->view = new View();

            try {
                $this->db = new PDO(DATABASE);
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }
    }