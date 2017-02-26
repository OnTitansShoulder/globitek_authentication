<?php
require_once('../../../private/initialize.php');
require_login();

if(!isset($_GET['id'])) {
  redirect_to('index.php');
}
$users_result = find_user_by_id($_GET['id']);
// No loop, only one result
$user = db_fetch_assoc($users_result);

// Set default values for all variables the page needs.
$errors = array();

if(is_post_request() && request_is_same_domain()) {
  ensure_csrf_token_valid();
  $changePW = false;

  // Confirm that values are present before accessing them.
  if(isset($_POST['first_name'])) { $user['first_name'] = $_POST['first_name']; }
  if(isset($_POST['last_name'])) { $user['last_name'] = $_POST['last_name']; }
  if(isset($_POST['username'])) { $user['username'] = $_POST['username']; }
  if(isset($_POST['email'])) { $user['email'] = $_POST['email']; }
  if(isset($_POST['previous_password'])) { $previous_password = $_POST['previous_password']; }
  if(isset($_POST['password'])) { $password = $_POST['password']; }
  if(isset($_POST['confirm_password'])) { $confirm_password = $_POST['confirm_password']; }
  if(isset($_POST['changePW'])) {
    $user['changePW'] = $_POST['changePW'];
    $changePW = $_POST['changePW'];
  }

  $errors = check_POST_blanks($_POST, $errors);

  if(empty($errors) && $changePW){
    $stored_hash = $user['password'];
    if(!decrypt_password_compare($previous_password, $stored_hash)) {
      $errors[] = "The previous password you entered does not match the one stored on database.";
    }
  }

  if(empty($errors) && $changePW){
    if ($password != $confirm_password){
      $errors[] = "The two passwords you entered don't match.";
    } else if(!has_valid_passwrod_format($password)){
      $errors[] = "Password format is incorrect.";
    } else{
      $user['password'] = encrypt_password($password);
    }
  }

  if(empty($errors)){
    $result = update_user($user);
    if($result === true) {
      redirect_to('show.php?id=' . $user['id']);
    } else {
      $errors = $result;
    }
  }
}
?>
<?php $page_title = 'Staff: Edit User ' . $user['first_name'] . " " . $user['last_name']; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<div id="main-content">
  <a href="index.php">Back to Users List</a><br />

  <h1>Edit User: <?php echo h($user['first_name']) . " " . h($user['last_name']); ?></h1>

  <p>Note: Passwords should be at least 12 characters and include at least one from each of following categories:<br />
    uppercase letter, lowercase letter, number, and symbols(such as '!', '*', '+', ',', '-', '.', '@', '_').</p>

  <p>A good password suggestion: </p>
  <?php echo generate_strong_password(12)."<br /><br />"; ?>

  <?php echo display_errors($errors); ?>

  <form action="edit.php?id=<?php echo h(u($user['id'])); ?>" method="post">
    <?php echo csrf_token_tag(); ?>
    First name:<br />
    <input type="text" name="first_name" value="<?php echo h($user['first_name']); ?>" /><br />
    Last name:<br />
    <input type="text" name="last_name" value="<?php echo h($user['last_name']); ?>" /><br />
    Username:<br />
    <input type="text" name="username" value="<?php echo h($user['username']); ?>" /><br />
    Email:<br />
    <input type="text" name="email" value="<?php echo h($user['email']); ?>" /><br /><br />
    <input type="checkbox" name="changePW" value="true" />
    &#9Change Password<br /><br />
    Previous Password:<br />
    <input type="password" name="previous_password" value="" /><br />
    Password:<br />
    <input type="password" name="password" value="" /><br />
    Confirm Password:<br />
    <input type="password" name="confirm_password" value="" /><br />
    <br />
    <input type="submit" name="submit" value="Update"  />
  </form>

</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
