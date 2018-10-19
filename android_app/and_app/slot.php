<?php

require_once 'update_slot_info.php';
$db = new update_slot_info();

// json response array
$response = array("error" => FALSE);

if (isset($_POST['startT']) && isset($_POST['endT']) && isset($_POST['build'])) 
{

    // receiving the post params
    $startT = $_POST['startT'];
    $endT = $_POST['endT'];
    $build = $_POST['build'];
    $email=$_POST['email'];
     if ($db->CheckExistingUser($email)) 
     {
        // user already existed
        $response["error"] = TRUE;
        $response["error_msg"] = "Slot already booked with " . $email;
        echo json_encode($response);
     }
    else
    {

        // create a new user
        $user = $db->StoreUserInfo($email,$startT, $endT, $build);
        if ($user) 
        {
            // user stored successfully
            $response["error"] = FALSE;
            $response["user"]["startT"] = $user["startT"];
            $response["user"]["endT"] = $user["endT"];
            $response["user"]["build"] = $user["build"];
           
            echo json_encode($response);
        } 
        else 
        {
            // user failed to book
            $response["error"] = TRUE;
            $response["error_msg"] = "Unknown error occurred in booking!";
            echo json_encode($response);
        }
    }
}
else 
{
    $response["error"] = TRUE;
    $response["error_msg"] = "Required parameters (start time, end time, building) is missing!";
    echo json_encode($response);
}

?>