# stackbuilders
Script for peak and plate prediction

For testing, edit predictor.php and easy change the parameters as you require.

$predictor->set_schedule([["07:00","09:30"],["16:00","19:30"]]);
$predictor->set_policy([[],[1,2],[3,4],[5,6],[7,8],[9,0],[]]);
$predictor->set_param("DATE", "2020/09/11");
$predictor->set_param("TIME", "12:59");
$predictor->set_param("PLATE", "PCM6029");

To run under console.

php-cgi -q predictor.php

TODO
Depending on future requirements it could be sat as webservice or
just a simple output for web forms or even for been queried by other programs.
