<?php
session_start();
$user_xml = simplexml_load_file("./xml/users.xml");
$is_invalid = true;
$is_blank = false;
$error_message = '';
if(isset($_POST['login_submit'])) {
    $username = $_POST['username_input'];
    $password = md5($_POST['password_input']);

    if($username === '' || $password === 'd41d8cd98f00b204e9800998ecf8427e') {
        $error_message = "<div class='alert alert-danger'>Username/Password fields cannot be empty</div>";
        $is_blank = true;
    }
    if($is_blank === false) {
        foreach ($user_xml->user as $user) {
            if((string)$user->username === $_POST['username_input']) {
                if((string)$user->password === $password) {
                    $_SESSION['userid'] = (string)$user->user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['password'] = $password;
                    $is_invalid = false;
                    if((string)$user->attributes() === "admin" || (string)$user->attributes() === "support") {
                        $_SESSION['priv'] = '494ceea5e99b9ddbd222afab8d72ce8f';
                    } else {
                        $_SESSION['priv'] = 'user';
                    }
                    header('Location: main.php');
                }
            }
        }
        $error_message = "<div class='alert alert-danger'>Username/Password is invalid</div>";
    }


}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Login page</title>


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</head>

<body>
<div class="jumbotron">
    <h1 class="text-center">Login</h1>

</div>




<div class="container">
    <section>
        <form name="login_form" id="login_form" action="" method="post">
            <fieldset>
                <legend>User Authentication</legend>
                <div class="form-group">
                    <label for="username_input">Username:</label>
                    <input type="text" class="form-control" name="username_input" id="username_input" />
                </div>
                <div class="form-group">
                    <label for="password_input">Password:</label>
                    <input type="password" class="form-control" name="password_input" id="password_input"/>
                </div>
                <input type="submit" name="login_submit" class="form-control" value="Submit" />
            </fieldset>
        </form>
    </section>
</div>


</body>
</html>
