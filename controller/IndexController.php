<?php


class IndexController
{
    public function __construct()
    { 
        $this->view = new View();
    } 

    public function showIndex()
    {
        /**
         * Carga los datos sólo una vez, al cerrar el navegador 
         * la sessión se destruye, session_set_cookie_params(0); en el index.php
         */
        if(!isset($_SESSION['TRAINING_DONE'])){
            require_once 'algorithms/Training.php';
            $train = new Training();
            $train->training();
            $_SESSION['TRAINING_DONE'] = true;      
        }
        
        $this->view->show("IndexView.php");
    } 

    public function showStyle()
    {
        $this->view->show("LearningStyleView.php");
    } 

    public function showCampus()
    {
        $this->view->show("CampusView.php");
    } 

    public function showGender()
    {
        $this->view->show("GenderView.php");
    } 

    public function showStyle2()
    {
        $this->view->show("Style2View.php");
    } 

    public function showProfessor()
    {
        $this->view->show("ProfessorView.php");
    }

    public function showNetWorks()
    {
        $this->view->show("NetworksView.php");
    }

    public function showCrossValidation()
    {
        $this->view->show("CrossValidationView.php");
    }
    
} 