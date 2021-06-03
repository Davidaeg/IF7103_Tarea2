<?php

class BayeClassifier
{
    /** 
     *    Devuelve Las clases del data set con su probabilidad general.
     *
     *    Obtiene las clases según la $request_data, cuenta cuantas veces aparece en todo el data set
     *    y calcula el promedio de aparición de cada una de las clases. En el vector resultante se guarda
     *    la cantidad de apariciones, el nombre de la clase y su promedio.
     *    
     *
     *    @param array $data dataset(tabla de la BD).
     *    @param string $requested_data nombre del atributo que contiene las clases.
     *    
     *    @return array $classes lista de clases y su probabilidad.
     *    
     */
    private function get_classes($data, $requested_data){
        $classes = [];
        foreach ($data as $tuple) { //$tuple = cada fila(tupla) del dataset
            $class = $tuple[$requested_data]; //Obtiene la clase de la columna según sea requerido.
            if(!isset($classes[$class])){ //Si no existe crea el espacio para almacenar los datos de esa clase y los inicializa.
                $classes[$class] = [];
                $classes[$class]['quantity'] = 1;
                $classes[$class]['class'] = $class ;
            }else{
                $classes[$class]['quantity'] += 1; //aumenta la cantidad cada vez que aparece 
                $classes[$class]['probability'] = $classes[$class]['quantity'] / sizeof($data); // va calculado el promedio de la clase
            }
        }
        return $classes;
    }

    /** 
     *    Devuelve los atributos(x) con su probabilidad y clasificados por apariciones en clases.
     *
     *    Obtiene los atributos con todas sus posibilidades de aparicion en el data_set clasificados por clase
     *    y con su probabilidad de aparicion en cada una de las clases.
     *    
     *    @param array $data dataset(tabla de la BD).
     *    @param string $requested_data nombre del atributo que contiene las clases.
     *    @param array $evaluated_data el resto de atributos que es la información que se va a evaluar.
     *    
     *    @return array $x_probabilities con la lista de atributos con su promedio y clasificados por clase.
     *    
     */
    private function get_atributes_probability($data, $requested_data, $evaluated_data, $classes, $name){
        $x_probabilities = [];
        foreach ($data as $tuple) { //$tuple = cada fila(tupla) del dataset
            $class = $tuple[$requested_data]; //obtiene la clase de la tupla
            foreach($evaluated_data as $attribute){ //recorre cada uno de loa atributos 
                // crea el espacio para guardar la lista de atributos para cada clase
                if(!isset($x_probabilities[$class][$attribute] )){ 
                    $x_probabilities[$class][$attribute] = [];
                    $x_probabilities[$class]['probability'] = $classes[$class]['probability'];
                }
                $attribute_value = $tuple[$attribute]; //Obtiene el valor del atributo en la tupla actual
                
                //Si no existe ya lo crea el espacio e inicializa
                if(!isset( $x_probabilities[$class][$attribute][$attribute_value])){ 
                    $x_probabilities[$class][$attribute][$attribute_value]['quantity'] = 1;
                    $x_probabilities[$class][$attribute][$attribute_value]['value'] = $attribute_value;
                }else{
                    //Cada vez que lo encuentra suma uno
                    $x_probabilities[$class][$attribute][$attribute_value]['quantity'] += 1; 
                }
                /*Va calculadndo la probabilidad de que el atributo tenga ese valor en la clase actual dividiendo
                  el total de apariciones de ese atributo con ese valor 
                  entre la cantidad de veces que se encuentra esa clase en el dataset
                 */
                $x_probabilities[$class][$attribute][$attribute_value]['probability'] =  $x_probabilities[$class][$attribute][$attribute_value]['quantity'] / $classes[$class]['quantity'];

            }
        }
        //Guarda el arreglo de probabilidades en un .json en la carpeta files
        file_put_contents('./files/'.$name.'.json', json_encode($x_probabilities));
    }

    /** 
     *    Devuleve la clase a la que pertenece según la clasificavion de Bayes
     *
     *    Obtiene la clase a la que pertebece la información ingresada realizando el producto 
     *    de las probabilidades de cada atributo y la probabilidad de cada una de las clases, y la clase con el 
     *    mayor resultado es la escogida. 
     *    
     *    @param array $input información ingresada por el usuario.
     *    @param array $evaluated_data el resto de atributos que son la información que se va a evaluar.
     *    @param string $name nombre del archivo que contiene los datos del entrenamiento.
     *    
     *    @return array $obtained_class con la clase obtenida.
     *    
     */
    private function classify($input, $evaluated_data, $name){
        $json = file_get_contents('./files/'.$name.'.json'); // se obtiene los datos del json respectivo
        $probabilities = json_decode($json, true); // se convierte a  un arreglo 
        $clases = array_keys($probabilities); // se sacan las clases

        $classifier = []; //arreglo que almacena la clasificación para las clases 

        $input_probability = []; // arreglo con las probabilidades de clas clases para comparar y obtener la mayor
        

                                                     //Para cada clase
        foreach($clases as $class){
            foreach($evaluated_data as $attribute){ // obtiene probabilidad cada atributo del input.
                foreach( $probabilities[$class][$attribute] as $ap){
                    if(!isset($classifier[$class][$attribute]['input'])){                //se captura
                        $classifier[$class][$attribute]['input'] = $input[$attribute];  // el dato que ingresó el usuario para ese atributo
                        $classifier[$class]['class'] = $probabilities[$class]['probability']; // la probabilidad de la clase actual
                    }
                    //se setea la diferencia que se va a usar para aproximar el valor si no existe la probabilidad 
                    //de este exactamente
                    if(!isset($classifier[$class][$attribute]['diferencia'])){
                        $classifier[$class][$attribute]['diferencia'] = $input[$attribute]; 
                        //En caso de que el valor sea 0, ya no habría menor diferencia así que se asigna su probabilidad de 
                        //de una vez
                        $classifier[$class][$attribute]['probability'] = $ap['probability'];
                    }
                    //Se comprueba si hay un valor que se aproxime mejor al ingresado por el usuario
                    // y se va reduciendo la diferencia
                    if(abs($ap['value'] - $input[$attribute] )<$classifier[$class][$attribute]['diferencia']){
                        $classifier[$class][$attribute]['diferencia'] = abs($ap['value'] - $input[$attribute] ); //Nueva diferencia más pequeña
                        $classifier[$class][$attribute]['aprox'] = $ap['value'] ; //Nueva aproximación
                        $classifier[$class][$attribute]['probability'] = $ap['probability']; //Actualiza la probabilidad
                    }
                    
                }
            }
        }

        $max=0; //para guardar la probabilidad más alta
        $obtained_class = 0; //para guardar la clase elegida.

        //Se hace el producto de los atributos por la probabilidad de cada clase
        foreach($clases as $class){
            foreach($evaluated_data as $attribute){
                    if(!isset($input_probability[$class])){
                        $input_probability[$class]= 1; //se inicializa el resultado
                    }
                    //Se va agregando cada atributo a la multiplicación, usando las probabilidades respectivas según la clase actual
                    $input_probability[$class]=  $input_probability[$class] * $classifier[$class][$attribute]['probability'];
            }
            //Se multiplica por la probabilidad de la clase actual.
            $input_probability[$class] = $input_probability[$class] * $classifier[$class]['class']; 
            
            //Se va comparando para obtener la mayor probabilidad y su clase
            if($input_probability[$class]>$max ){
                $max = $input_probability[$class]; //Se guarda la probabilidad más alta en max
                $obtained_class = $class; // Se obtiene la clase 
            }
        }
        
        //Se retorna la clase con mayor probabilidad
        return $obtained_class;
    }

    /**
     *  Entrena al algoritmo
     * 
     *  Calcula las distintas probabilidades de clases y sus atributos y 
     *  crea un archivo JSON para cada formulario con los datos de probabilidades necesarios.
     * 
     *  @param array $data dataset(tabla de la BD).
     *  @param string $requested_data nombre del atributo que contiene las clases.
     *  @param  array $evaluated_data lista de atributos para evaluar.
     *  @param string $name nombre del archivo donde se almacenará.
     * 
     */
    public function load_probabilities($data, $evaluated_data, $requested_data, $name){
        $classes = $this->get_classes($data, $requested_data);
        $this->get_atributes_probability($data, $requested_data, $evaluated_data, $classes, $name);
    }

    /**
     *  Ejecuta la clasificación de naive bayes
     * 
     *  @param array $input datos del formulario, ingresados por el usuario.
     *  @param  array $evaluated_data lista de atributos para evaluar.
     *  @param string $name nombre del archivo donde estan almacenadas las probabilidades.
     */
    public function baye_classification($input, $evaluated_data, $name)
    {
        $result = $this->classify($input, $evaluated_data, $name);

        return $result;
    }






}
