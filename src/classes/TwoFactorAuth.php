<?php
class TwoFactorAuth
{
    private $db;
    private $crud;
    private $input;
    private $validator;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->crud = new Crud();
        $this->input = new Input();
        $this->validator = new Validate();
    }

    public function validate2FA($userID){
        return false;
    }

    public function sendOTP($userID){

    }

    private function removeOTP($userID)
    {

    }

   
}
