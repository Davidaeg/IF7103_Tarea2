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
    private function get_atributes_probability($data, $requested_data, $evaluated_data, $classes){
        $x_probabilities = [];
        foreach ($data as $tuple) { //$tuple = cada fila(tupla) del dataset
            $class = $tuple[$requested_data]; //obtiene la clase de la tupla
            foreach($evaluated_data as $attribute){ //recorre cada uno de loa atributos 
                // crea el espacio para guardar la lista de atributos para cada clase
                if(!isset($x_probabilities[$class][$attribute] )){ 
                    $x_probabilities[$class][$attribute] = [];
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
                /*Va calculadndo la probabilidad de que el atributo tenga ese valor en la calse actual dividiendo
                  el total de apariciones de ese atributo con ese valor 
                  entre todas las apariciones de esa clase en el dataset
                 */
                $x_probabilities[$class][$attribute][$attribute_value]['probability'] =  $x_probabilities[$class][$attribute][$attribute_value]['quantity'] / $classes[$class]['quantity'];

            }
        }

        return $x_probabilities;
    }

    private function classify($input, $classes_probabilities, $atributtes_probabilities, $evaluated_data, $requested_data){
        $classifier = [];
        $input_probability = [];
        foreach ($classes_probabilities as $c) { 
            $class = $c['class'];
            foreach($evaluated_data as $attribute){ // obtiene probabilidad cada atributo en el dato solicitado .
                foreach($atributtes_probabilities as $attribute_probability){
                    foreach(array_values($attribute_probability[$attribute]) as $ap){
                        if(!isset($classifier[$class][$attribute]['input'])){
                            $classifier[$class][$attribute]['input'] = $input[$attribute];
                        }
                        if(!isset($classifier[$class][$attribute]['diferencia'])){
                            $classifier[$class][$attribute]['diferencia'] = $input[$attribute];
                        }
                        if(abs($ap['value'] - $input[$attribute] )<$classifier[$class][$attribute]['diferencia']){
                            $classifier[$class][$attribute]['diferencia'] = abs($ap['value'] - $input[$attribute] );
                            $classifier[$class][$attribute]['aprox'] = $ap['value'] ;
                        }
                        $classifier[$class][$attribute]['probability'] = $ap['probability'];
                    }
                }
            }
        }

        foreach ($classes_probabilities as $c) { 
            $class = $c['class'];
            foreach($evaluated_data as $attribute){
                    if(!isset($input_probability[$class])){
                        $input_probability[$class] = 1;
                    }else{
                        $input_probability[$class] *= $classifier[$class][$attribute]['probability'];
                    }
            }
            $input_probability[$class] *= $c['probability'];
        }
        return max(array_keys($input_probability));
    }

    public function baye_classification($data, $input, $evaluated_data, $requested_data)
    {
        $classes = $this->get_classes($data, $requested_data);
        $x_probabilities = $this->get_atributes_probability($data, $requested_data, $evaluated_data, $classes);
        $result = $this->classify($input, $classes, $x_probabilities, $evaluated_data, $requested_data);

        return $result;
    }






}
