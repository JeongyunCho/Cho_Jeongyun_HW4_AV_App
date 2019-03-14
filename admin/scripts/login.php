<?php

function login($username, $password, $ip)
{
    

require_once('connect.php');
  //check if username exists
  $check_exist_query = "SELECT COUNT(*) FROM tbl_user WHERE user_name = :username";
  
  // var_dump($check_exist_query);
  // $user_set = $pdo->query($check_exist_query);
  $user_set = $pdo->prepare($check_exist_query);
  $user_set->execute(
    array(
      ':username' => $username
    )
  );
    // var_dump($user_set);exit;
  if ($user_set->fetchColumn() > 0) {
    $get_user_query = "SELECT * FROM tbl_user WHERE user_pass = :psw AND user_name = :username";
    //var_dump($get_user_query);exit;
    $get_user_set = $pdo->prepare($get_user_query);
    $get_user_set->execute(
      array(
        ":psw" => $password,
        ":username" => $username
      )
    );

    while ($found_user = $get_user_set->fetch(PDO::FETCH_ASSOC)) {
      $id = $found_user['user_id'];
      $_SESSION['user_id'] = $id;
      $_SESSION['user_name'] = $found_user['user_name'];      
      $bdate = $found_user['user_bdate'];
      //Update user login IP
			$update_ip_query = 'UPDATE tbl_user SET user_ip=:ip WHERE user_id=:id';
			$update_ip_set = $pdo->prepare($update_ip_query);
			$update_ip_set->execute(
				array(
					':ip'=>$ip,
					':id'=>$id
				)
      );

      $update_days_query = 'UPDATE tbl_user SET user_agedays=TO_DAYS(NOW())-TO_DAYS("'.$bdate.'") WHERE user_id= '.$id;
			$update_days_set = $pdo->query($update_days_query);
  
      $user_info_check = getSingle("tbl_user", "user_id", $id);
      while ($user_info = $user_info_check->fetch(PDO::FETCH_ASSOC)) {
        $user = array();

        $user['user_id'] = $found_user['user_id'];
        $user['username'] = $found_user['user_name'];
        $user['user_agedays'] =$user_info['user_agedays'];
      }

     
   
 
    
      // add any other non-sensitive details here...

      return $user;
    }

    if (empty($id)){
        $message = 'No ID';
        return $message;
    }

  } else {
    $message = 'No User';
    return $message;
  }
}