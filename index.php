<?php

    session_set_cookie_params(0);
    session_start();
    require 'libs/FrontController.php';
    FrontController::main();

?>
