<?php

#Purpose: suitecrm api for inserting records in any module
#Author: kaushik karan
#Created on: 2016-11-05 14:30
#dependencies:
#  *register_argc_argv should be "ON" in php.ini


$url = "http://<server_ip>/suitecrm/service/v4_1/rest.php"; //complete the url
$username = ""; //insert the suitecrm username
$password = ""; //insert the suitecrm password


//name of the csv file from which data is to be inserted in suitecrm
$fileName = $argv[1];
//setting csv file field delimitter
$csvDelimitter = ";";

//----------function to make request starts----------
function call($method, $parameters, $url){
    ob_start();
    $curl_request = curl_init();

    curl_setopt($curl_request, CURLOPT_URL, $url);
    curl_setopt($curl_request, CURLOPT_POST, 1);
    curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl_request, CURLOPT_HEADER, 1);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

    $jsonEncodedData = json_encode($parameters);

    $post = array(
        "method" => $method,
        "input_type" => "JSON",
        "response_type" => "JSON",
        "rest_data" => $jsonEncodedData
    );

    curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($curl_request);
    curl_close($curl_request);

    $result = explode("\r\n\r\n", $result, 2);
    $response = json_decode($result[1]);
    ob_end_flush();

    return $response;
}
//---xxx--- function to make request ends ---xxx---


//login ----------------------------------------- 
$login_parameters = array(
    "user_auth" => array(
        "user_name" => $username,
        "password" => md5($password),
        "version" => "1"
    ),
    "application_name" => "RestTest",
    "name_value_list" => array(),
);

//login into suitecrm
$login_result = call("login", $login_parameters, $url);


//get session id
//session id is required to make request to suitecrm
$session_id = $login_result->id;


//----------getting headers from file started----------
$csvReadFile = fopen($fileName, 'r');
$csvFileHeaderList = fgets($csvReadFile);
fclose($csvReadFile);

$csvFileHeader = explode($csvDelimitter, $csvFileHeaderList);

//printing headers of csv file
$headerCount = count($csvFileHeader);
for($i=2; $i < $headerCount; $i++){
    $fileHeader[$i-2] = preg_replace("/\r\n|\r|\n/",'',$csvFileHeader[$i]);
}
#uncomment below line to view headers
#echo "printing file headers\n";
#var_dump($fileHeader);

//---xxx--- getting headers from file ended ---xxx---


//the parameters which are required for the suitecrm api
$suitecrm_parameters = array(
    //session id
    "session" => $session_id,

    //The name of the module from which to retrieve records.
    "module_name" => "", //will be set while reading the csv file

    //Record attributes
    "name_value_list" => array(
    )
);



//--------- getting data from file and pushing into suitecrm started ---------
//print csv file data
echo "printing data \n";

$row = 1;
if (($handle = fopen($fileName, "r")) !== FALSE)
{
    while (($data = fgetcsv($handle, 1000, $csvDelimitter)) !== FALSE)
    {
        $num = count($data);
        $method = $data[0];
        $suitecrm_parameters["module_name"] = $data[1];
        if($row != 1)
        {
            for ($c=2; $c < $num; $c++)
            {
                $name_value_list_array = array("name" => $fileHeader[$c-2], "value"=> $data[$c]);
                array_push($suitecrm_parameters["name_value_list"], $name_value_list_array);
            }
            $suite_result = call($method, $suitecrm_parameters, $url);
            echo "\n\nprinting suiteresult\n";
            print_r($suite_result);
            $suitecrm_parameters["name_value_list"] = array();
        }
        $row++;
    }
    fclose($handle);
}

//---xxx--- getting data from file and pushing into suitecrm ended ---xxx---



?>
