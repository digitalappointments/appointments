<?php

printf("%s\n", __FILE__);
           

printf("<PRE>\nargs:\n"); 
parse_str($_SERVER['QUERY_STRING'], $args);
print_r($args);