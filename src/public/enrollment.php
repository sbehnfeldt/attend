<?php

require('../lib/bootstrap.php');


$loader = new Twig_Loader_Filesystem('../templates');
$twig   = new Twig_Environment($loader, array(
    'cache' => false
));

echo $twig->render('enrollment.html.twig');