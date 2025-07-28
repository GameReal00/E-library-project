<?php 

session_start();

include('server/db_connection.php');

//if user has already register take the user to account page 
if (isset($_SESSION['logged_in'])){
    header('location: account.php');
    exit;
}

if(isset($_POST['register'])){

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirmPassword = $_POST['confirmPassword'];
//if password dont march
if($password !== $confirmPassword){
    header('location: register.php?error=passwords dont march');

//if password is less than  6 char
}else if(strlen($password) < 6){
    header('location: register.php?error=passwords must be at least 6 charachters');


//if there is no error
}else{
//check whether there is user with this email or not 
$stmt1 = $conn->prepare("SELECT count(*) FROM users WHERE user_email=?");
$stmt1->bind_param('s',$email);
$stmt1->execute();
$stmt1->bind_result($num_rows);
$stmt1->store_result();
$stmt1->fetch();
//if there is a user already registerd with this email 
if($num_rows !=0){
    header('location: register.php?error=user with this email exist');
    //if no user registerd with this email befor 
}else{


//create a new user
$stmt = $conn->prepare("INSERT INTO users (user_name,user_email,user_password)
                VALUES (?,?,?)");


$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt->bind_param('sss', $name, $email, $hashed_password);


//if account was created successfully 
if($stmt->execute()){
    $user_id = $stmt->insert_id;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $name;
    $_SESSION['logged_in'] = true;
    header('location: account.php?register_success=You registered successfully');
//account could not be created 
}else{
    header('location: register.php?error=could not create an account at the moment');

}
}
}



}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/style.css"/>

<style>
    .product img{
        width: 50%;
        height: auto;
        box-sizing: border-box;
        object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);

    }
     .logo {
          width: 50px;
          height: 50px;
          border-radius: 50%;
     }
</style>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>

    <link rel="stylesheet" href="assets/css/style.css"/>


</head>
<body>
   <!--Navbar-->
   <nav class="navbar navbar-expand-lg bg-white py-3 fixed-top">
    <div class="container">
      <img class="logo"  src="assets/imgs/logo.jpg"/>
      <h2 class="brand">E-Library</h2>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse nav-buttons" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          
          <li class="nav-item">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="shop.php">Shop</a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="#">Blog</a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link" href="contact.php">Contact Us</a>
          </li>

          <li class="nav-item">
            <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
           <a href="account.php"><i class="fas fa-user"></i></a> 
          </li>
          
         
        </ul>
       
      </div>
    </div>
  </nav>


<!--Register-->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        
        <h2 class="form-weight-bold">Register</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container" >
        <form id="register-form" method="POST" action="register.php">
            <p style="color: red;"><?php if(isset($_GET['error'])){ echo $_GET['error'];}?>
            <div class="form-group container">
                <level>Name</level>
                <input type="text" class="form-control" id="register-name" name="name" placeholder="Name" required/>
            </div>
            <div class="form-group container">
            <level>Email</level>
            <input type="text" class="form-control" id="login-email" name="email" placeholder="Email" required/>
        </div>
        <div class="form-group container">
            <level>Password</level>
            <input type="password" class="form-control" id="register-password" name="password" placeholder="password" required/>
        </div>
        <div class="form-group container">
            <level>Confirm Password</level>
            <input type="password" class="form-control" id="register-confirm-password" name="confirmPassword" placeholder="Confirm Password" required/>
        </div>
        <div class="form-group container">
            <input type="submit" class="btn" id="register-btn" name="register" value="Register"/>
        </div>
        <div class="form-group container">
            <a id="login-url" href="login.php" class="btn">Do you have an account? Login</a>
        </div>
        </form>

    </div>

</section>



  <!--footer-->
<Footer class="mt-5 py-5">
    <div class="row container mx-auto pt-5">
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <img class="logo" src="assets/imgs/logo.jpg"/> 
            <p class="pt-3">We provide the best product for the best price</p>
        </div>
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pb-2">Featured</h5>
            <ul class="text-uppercase">
                <li><a href="#">men</a></li>
                <li><a href="#">women</a></li>
                <li><a href="#">boys</a></li>
                <li><a href="#">girls</a></li>
                <li><a href="#">new arrivals</a></li>
                <li><a href="#">cloths</a></li>
    
            </ul>
        </div>
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pb-2">Contact Us</h5>
            <div>
                <h6 class="text-uppercase">Address</h6>
                <p>No. 583 SGD, qtrs. kano</p>
            </div>
            <div>
                <h6 class="text-uppercase">Phone No.</h6>
                <p>09083166579</p>
            </div>
            <div>
                <h6 class="text-uppercase">Email</h6>
                <p>Sibawayhmohd@gmail.com</p>
            </div>
        </div>
        <div class="footer-one col-lg-3 col-md-6 col-sm-12">
            <h5 class="pb-2">Instagram</h5>
            <div class="row">
                <img src="assets/imgs/RMWatch.webp" class="img-fluid w-25 h-100 m-2"/>
                <img src="assets/imgs/ball2.jpg" class="img-fluid w-25 h-100 m-2"/>
                <img src="assets/imgs/FB.jpg" class="img-fluid w-25 h-100 m-2"/>
                <img src="assets/imgs/ManUkit.jpeg" class="img-fluid w-25 h-100 m-2"/>
                <img src="assets/imgs/iphone.jpeg" class="img-fluid w-25 h-100 m-2"/>
            </div>
        </div>
    </div>
    <div class="copyright mt-5">
        <div class="row container mx-auto">
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4">
                <img src="assets/imgs/payment.jpg">
            </div>
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4 text-nowrap mb-2">
                <p>Ecommerce @2025 All Right Reserved</p>
            </div>
            <div class="col-lg-3 col-md-5 col-sm-12 mb-4">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
    
    </Footer>
    
    
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
    </html>