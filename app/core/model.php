<?php
    class Model
    {
        protected $db;

        function __construct() {
            try {
                $this->db = new PDO(DATABASE);
                //echo "Connected successfully";
            } catch (PDOException $e) {
                die('Connection failed: ' . $e->getMessage());
            }
        }
    }