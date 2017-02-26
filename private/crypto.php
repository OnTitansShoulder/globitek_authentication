<?php

  function make_random_str($length=22){
    $length = max(1, (int) $length);
    $rand_str = base64_encode(random_bytes($length));
    return substr($rand_str, 0, $length);
  }

  function make_salt(){
    $rand_str = make_random_str();
    // Bcrypt doesn't like to have '+'
    $salt = strtr($rand_str, '+', '.');
    return $salt;
  }

  function my_password_hash($raw_password){
    $hash_format = "$2y$11$";
    $salt = make_salt();
    $hashed = crypt($raw_password, $hash_format.$salt);
    return $hashed;
  }

  function my_password_verify($entered_password, $stored_hash){
    $new_hash = crypt($entered_password, $stored_hash);
    return ($new_hash === $stored_hash);
  }

  function encrypt_password($raw_password){
    return my_password_hash($raw_password);
  }

  function decrypt_password_compare($entered_password, $stored_hash){
    return my_password_verify($entered_password, $stored_hash);
  }

 ?>
