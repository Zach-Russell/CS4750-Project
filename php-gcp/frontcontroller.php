<?php

/**
 * This is an example of a front controller for a flat file PHP site. Using a
 * Static list provides security against URL injection by default. See README.md
 * for more examples.
 */
# [START gae_simple_front_controller]
switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/index.php':
        require 'index.php';
        break;
    case '/login.php':
        require 'login.php';
        break;
    case '/dbconnection.php':
        require 'dbconnection.php';
        break;
    case '/logout.php':
        require 'logout.php';
        break;
    case '/groceryItems.php':
        require 'groceryItems.php';
        break;
    case '/myLists.php':
        require 'myLists.php';
        break;
    case '/register.php':
        require 'register.php';
        break;
    case '/search.php':
        require 'search.php';
        break;
    case '/searchCategory.php':
        require 'searchCategory.php';
        break;
    case '/settings.php':
        require 'settings.php';
        break;
    case '/':
        require 'index.php';
        break;
    default:
        http_response_code(404);
        exit('Not Found');
  }  
  ?>