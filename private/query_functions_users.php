<?php

  //
  // USER QUERIES
  //

  // Find all users, ordered last_name, first_name
  function find_all_users() {
    global $db;
    $sql = "SELECT * FROM users ";
    $sql .= "ORDER BY last_name ASC, first_name ASC;";
    $users_result = db_query($db, $sql);
    return $users_result;
  }

  // Find user using id
  function find_user_by_id($id=0) {
    global $db;
    $sql = "SELECT * FROM users ";
    $sql .= "WHERE id='" . db_escape($db, $id) . "' ";
    $sql .= "LIMIT 1;";
    $users_result = db_query($db, $sql);
    return $users_result;
  }

  // find_users_by_username('rockclimber67');
  function find_users_by_username($username='') {
    global $db;
    $sql = "SELECT * FROM users ";
    $sql .= "WHERE username = '" . db_escape($db, $username) . "';";
    $users_result = db_query($db, $sql);
    return $users_result;
  }

  function validate_user($user, $errors=array()) {
    if (is_blank($user['first_name'])) {
      $errors[] = "First name cannot be blank.";
    } elseif (!has_length($user['first_name'], array('min' => 2, 'max' => 255))) {
      $errors[] = "First name must be between 2 and 255 characters.";
    }

    if (is_blank($user['last_name'])) {
      $errors[] = "Last name cannot be blank.";
    } elseif (!has_length($user['last_name'], array('min' => 2, 'max' => 255))) {
      $errors[] = "Last name must be between 2 and 255 characters.";
    }

    if (is_blank($user['email'])) {
      $errors[] = "Email cannot be blank.";
    } elseif (!has_valid_email_format($user['email'])) {
      $errors[] = "Email must be a valid format.";
    }

    if (is_blank($user['username'])) {
      $errors[] = "Username cannot be blank.";
    } elseif (!has_length($user['username'], array('max' => 255))) {
      $errors[] = "Username must be less than 255 characters.";
    } elseif (!has_valid_username_format($user['username'])) {
      $errors[] = "Username can only contain letters, numbers, and underscores.";
    } elseif (!is_unique_username($user['username'], $user['id'])) {
      $errors[] = "Username not allowed. Try another.";
    }
    return $errors;
  }

  // Add a new user to the table
  // Either returns true or an array of errors
  function insert_user($user) {
    global $db;

    $errors = validate_user($user);
    if (!empty($errors)) {
      return $errors;
    }

    $created_at = date("Y-m-d H:i:s");
    $sql = "INSERT INTO users ";
    $sql .= "(first_name, last_name, email, username, password, created_at) ";
    $sql .= "VALUES (";
    $sql .= "'" . db_escape($db, $user['first_name']) . "',";
    $sql .= "'" . db_escape($db, $user['last_name']) . "',";
    $sql .= "'" . db_escape($db, $user['email']) . "',";
    $sql .= "'" . db_escape($db, $user['username']) . "',";
    $sql .= "'" . db_escape($db, $user['password']) . "',";
    $sql .= "'" . $created_at . "'";
    $sql .= ");";
    // For INSERT statements, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL INSERT statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  // Edit a user record
  // Either returns true or an array of errors
  function update_user($user) {
    global $db;

    $errors = validate_user($user);
    if (!empty($errors)) {
      return $errors;
    }

    $sql = "UPDATE users SET ";
    $sql .= "first_name='" . db_escape($db, $user['first_name']) . "', ";
    $sql .= "last_name='" . db_escape($db, $user['last_name']) . "', ";
    $sql .= "email='" . db_escape($db, $user['email']) . "', ";
    $sql .= "username='" . db_escape($db, $user['username']) . "', ";
    $sql .= "password='" . db_escape($db, $user['password']) . "' ";
    $sql .= "WHERE id='" . db_escape($db, $user['id']) . "' ";
    $sql .= "LIMIT 1;";
    // For update_user statements, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL UPDATE statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  // Delete a user record
  // Either returns true or false
  function delete_user($user) {
    global $db;

    $sql = "DELETE FROM users ";
    $sql .= "WHERE id='" . db_escape($db, $user['id']) . "' ";
    $sql .= "LIMIT 1;";
    // For update_user statements, $result is just true/false
    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL DELETE statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  //
  // Failed login queries
  //

  function find_failed_login($username){
    global $db;

    $sql = "SELECT * FROM failed_logins ";
    $sql .= "WHERE username='".$username."' ";
    $sql .= "LIMIT 1;";

    $failed_login_result = db_query($db, $sql);
    return $failed_login_result;
  }

  function insert_failed_login($failed_login){
    global $db;

    //$created_at = date("Y-m-d H:i:s");

    $sql = "INSERT INTO failed_logins ";
    $sql .= "(username, count, last_attempt) ";
    $sql .= "VALUES (";
    $sql .= "'".$failed_login['username']."', ";
    $sql .= $failed_login['count'].", ";
    $sql .= "'".$failed_login['last_attempt']."'";
    $sql .= ");";

    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL UPDATE statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  function update_failed_login($failed_login){
    global $db;

    $sql = "UPDATE failed_logins SET ";
    $sql .= "count=".$failed_login['count'].", ";
    $sql .= "last_attempt='".$failed_login['last_attempt']."' ";
    $sql .= "WHERE username='".$failed_login['username']."'";
    $sql .= ";";

    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL UPDATE statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

  function reset_failed_login($username){
    global $db;

    $sql = "UPDATE failed_logins SET ";
    $sql .= "count=0 ";
    $sql .= "WHERE username='".$username."'";
    $sql .= ";";

    $result = db_query($db, $sql);
    if($result) {
      return true;
    } else {
      // The SQL UPDATE statement failed.
      // Just show the error, not the form
      echo db_error($db);
      db_close($db);
      exit;
    }
  }

?>

<!-- failed_logins database table
create table failed_logins(
username VARCHAR(255) NOT NULL,
count INT NOT NULL,
last_attempt DATETIME NOT NULL
) -->
