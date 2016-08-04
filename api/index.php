<?php

// Set timezone.
date_default_timezone_set('Europe/Budapest');

// Set config.
require 'config.php';
require 'db.php';

// Set models.
require 'models/Factory.php';
require 'models/Crud.php';
require 'models/Input.php';
require 'models/Model.php';
require 'models/Products.php';
require 'models/Categories.php';
require 'models/Conditions.php';
require 'models/ProductsToCategories.php';

// Set controllers.
require 'controllers/CrudController.php';

Factory::CrudController()->start();

?>
