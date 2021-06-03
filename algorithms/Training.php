<?php

class Training
{
    /**
     *  NO recibe ni retorna nada, sólo ejecuta los procediemientos almacenados e invoca a Bayes
     *  Para cargar las posibilidades de las diferentes tablas
     */
    public function training(){
        require_once 'model/IndexModel.php';
        $model = new IndexModel();

        require_once 'BayeClassifier.php';
        $algorithms = new BayeClassifier();

        //guarda en $data un array con la tabla que viene del procediemiento almacenado
        $data = $model->exec_query('sp_get_estilo_recinto()');  
        $evaluated_data = ['EC', 'OR', 'CA', 'EA']; // Los datos que se evaluaran 
        $requested_data = 'Estilo'; //El dato que se necesita clasificar(Clase)
        $name = "Style1"; //Nombre que va a tener el archivo .json

        //Envia esos datos a procesar y se crean los archivos con las probabilidades
        $algorithms->load_probabilities($data, $evaluated_data, $requested_data, $name);
        

        $data = $model->exec_query('sp_get_sexo_estilo()');
        $evaluated_data = ['Sexo', 'Promedio', 'Estilo'];
        $requested_data = 'Recinto';
        $name = "Campus";
        $algorithms->load_probabilities($data, $evaluated_data, $requested_data, $name);

        $data = $model->exec_query('sp_get_sexo_estilo()');
        $evaluated_data = ['Estilo', 'Promedio', 'Recinto'];
        $requested_data = 'Sexo';
        $name = "Gender";
        $algorithms->load_probabilities($data, $evaluated_data, $requested_data, $name);

        $data = $model->exec_query('sp_get_sexo_estilo()');
        $evaluated_data = ['Sexo', 'Promedio', 'Recinto'];
        $requested_data = 'Estilo';
        $name = "Style2";
        $algorithms->load_probabilities($data, $evaluated_data, $requested_data, $name);

        $data = $model->exec_query('sp_get_profesores()');
        $evaluated_data = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $requested_data = 'Class';
        $name = "Professor";
        $algorithms->load_probabilities($data, $evaluated_data, $requested_data, $name);

        $data = $model->exec_query('sp_get_redes()');
        $evaluated_data = ['RE', 'LI', 'CA', 'CO'];
        $requested_data = 'Class';
        $name = "Network";
        $algorithms->load_probabilities($data, $evaluated_data, $requested_data, $name);

    }

}
//guarda en $data un array con la tabla resultante de la consulta
        