<?php
/**
 * File for displaying the formular for creating a new training.
 * Call:   class.Training.create.php
 */
require('class.Frontend.php');
$Frontend = new Frontend(true, __FILE__);
$Frontend->displayHeader();

Training::displayCreateWindow();

$Frontend->displayFooter();
$Frontend->close();
?>