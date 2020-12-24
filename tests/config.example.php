<?php

// Rename this file to config.php

// Public Key
define("GETREVUE_API_KEY", getenv('REVUE_API_KEY'));

// Email from existing subscriber
define("GETREVUE_TEST_EMAIL", getenv('REVUE_TEST_EMAIL'));

// Email from existing subscriber
define("GETREVUE_TEST_LIST_ID", "64471");

// Minimum amount of subscribers this list has
define("GETREVUE_TEST_LIST_MIN_SUBSCRIBER_COUNT", 5000);

// Add this test subscriber
define("GETREVUE_TEST_ADD_EMAIL", "getrevue-php-test@firewards.com");
