<?php
require_once __DIR__ . '../Models/User.php';

class UserController{

private $model;


public function __construct()
{
    $this->model = new UserModel();
}
  


}

?>