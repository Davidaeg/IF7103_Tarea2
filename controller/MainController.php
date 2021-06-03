<?php

class MainController
{
    public function __construct()
    {   
        $this->view = new View();
    }

    public function getStyle()
    {
        //Información que se recibe desdel view
        $input['EC'] = $_POST['ec'];
        $input['OR'] = $_POST['or'];
        $input['CA'] = $_POST['ca'];
        $input['EA'] = $_POST['ea'];

        //Se define las columnas que se van a evaluar
        $evaluated_data = ['EC', 'OR', 'CA', 'EA']; 

        // se envía a cladificar con naive bayes. último parametro: el nombre del archivo con los datos
        require_once 'algorithms/BayeClassifier.php';
        $algorithms = new BayeClassifier();
        $style = $algorithms->baye_classification($input, $evaluated_data, 'Style1');
        // echo  json_encode($style);

        // se obtiene el resultado y se interpreta 
        switch ($style) {
            case 0:
                $style = 'Acomodador';
                break;
            case 1:
                $style = 'Divergente';
                break;
            case 2:
                $style = 'Convergente';
                break;
            case 3:
                $style = 'Asimilador';
                break;
        }
        echo  $style;   //envia respuesta
    }

    public function getCampus()
    {
        //Información que se recibe desdel view
        $input['Sexo'] = $_POST['gender'];
        $input['Promedio'] = $_POST['average'];
        $input['Estilo'] = $_POST['style'];

        $evaluated_data = ['Sexo', 'Promedio', 'Estilo'];

        // se envía a cladificar con naive bayes. último parametro: el nombre del archivo con los datos
        require_once 'algorithms/BayeClassifier.php';
        $algorithms = new BayeClassifier();
        $campus = $algorithms->baye_classification($input, $evaluated_data, 'Campus');
        // echo  json_encode($campus);
        
        //se obtiene el resultado,se interpreta y se envía respuesta
        if ($campus == 0)
            echo 'Turrialba';
        else
            echo 'Paraíso';
    }

    public function getGender()
    {
        //Información que se recibe desdel view
        $input['Estilo'] = $_POST['style'];
        $input['Promedio'] = $_POST['average'];
        $input['Recinto'] = $_POST['campus'];

        //Se define las columnas que se van a evaluar
        $evaluated_data = ['Estilo', 'Promedio', 'Recinto'];

        // se envía a cladificar con naive bayes. último parametro: el nombre del archivo con los datos
        require_once 'algorithms/BayeClassifier.php';
        $algorithms = new BayeClassifier();
        $gender = $algorithms->baye_classification($input, $evaluated_data,'Gender');
        // echo  json_encode($gender);

        //se obtiene el resultado,se interpreta y se envía respuesta
        if ($gender == 0)
            echo 'Masculino';
        else
            echo 'Femenino';
    }

    public function getStyle2()
    {
        //Información que se recibe desdel view
        $input['Sexo'] = $_POST['gender'];
        $input['Promedio'] = $_POST['average'];
        $input['Recinto'] = $_POST['campus'];

        //Se define las columnas que se van a evaluar
        $evaluated_data = ['Sexo', 'Promedio', 'Recinto'];

        // se envía a cladificar con naive bayes. último parametro: el nombre del archivo con los datos
        require_once 'algorithms/BayeClassifier.php';
        $algorithms = new BayeClassifier();
        $style = $algorithms->baye_classification($input, $evaluated_data, 'Style2');

        //se obtiene el resultado,se interpreta y se envía respuesta
        if ($style == 0)
            echo 'Acomodador';
        elseif ($style == 1)
            echo 'Divergente';
        elseif ($style == 2)
            echo 'Convergente';
        else echo 'Asimilador';
    }

    public function professorType()
    {
        //Información que se recibe desdel view
        $input['A'] = $_POST['A'];
        $input['B'] = $_POST['B'];
        $input['C'] = $_POST['C'];
        $input['D'] = $_POST['D'];
        $input['E'] = $_POST['E'];
        $input['F'] = $_POST['F'];
        $input['G'] = $_POST['F'];
        $input['H'] = $_POST['F'];

        //Se define las columnas que se van a evaluar
        $evaluated_data = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        // se envía a cladificar con naive bayes. último parametro: el nombre del archivo con los datos
        require_once 'algorithms/BayeClassifier.php';
        $algorithms = new BayeClassifier();
        $type = $algorithms->baye_classification($input, $evaluated_data, 'Professor');

        //se obtiene el resultado,se interpreta y se envía respuesta
        if ($type == 1)
            echo 'Principiante';
        elseif ($type == 2)
            echo 'Intermedio';
        elseif ($type == 3)
            echo 'Avanzado';
    }

    public function networkClass()
    {

        //Información que se recibe desdel view
        $input['RE'] = $_POST['RE'];
        $input['LI'] = $_POST['LI'];
        $input['CA'] = $_POST['CA'];
        $input['CO'] = $_POST['CO'];

        //Se define las columnas que se van a evaluar
        $evaluated_data = ['RE', 'LI', 'CA', 'CO'];

        // se envía a cladificar con naive bayes. último parametro: el nombre del archivo con los datos
        require_once 'algorithms/BayeClassifier.php';
        $algorithms = new BayeClassifier();
        $type = $algorithms->baye_classification($input, $evaluated_data, 'Network');

        //se obtiene el resultado,se interpreta y se envía respuesta
        if ($type == 1)
            echo 'A';
        else
            echo 'B';
    }
}
