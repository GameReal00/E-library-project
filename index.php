<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous"/>

    <link rel="stylesheet" href="assets/css/style.css"/>

    <style>
        .product img{
            width: 50%;
            height: auto;
            box-sizing: border-box;
            object-fit: cover;
    
        }
    </style>

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

<!--home-->

<section id="home">
<div class="container"> 
    
    <h5>New Arrivals</h5>
    <h1><span>Best Books </span>this season</h1>
    <p>E-Library offers the best books for the most affordable prices</p>
   <a class="btn shop-now-btn" href="shop.php">Browse Books</a>
   

</div>

</section>

<!--brands-->
 <Section id="brand" class="container"> 
<div class="row">
    <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/book_brand1.png"/>
    <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/book_brand2.png"/>
    <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/book_brand3.png"/>
    <img class="img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/book_brand4.png"/>
</div>

 </Section>

<!--New-->
<section id="new" class="w-100"> 
<div class="row p-0 m-0"> 
    <!--One-->
    <div class="one col-lg-4 col-md-12 col-sm-12 p-0 container pt-5">   
        <img class="img-fluid" src="assets/imgs/FB.jpg"/>
        <div class="details"> 
            <h2 class="pt-5"> New Book Releases </h2>
            <button class="text-uppercase"> Browse Now</button>
        </div>
    </div>
    <!--Two-->
    <div class="one col-lg-4 col-md-12 col-sm-12 p-0 container pt-5 ">   
        <img class="img-fluid" src="assets/imgs/RMWatch.webp"/>
        <div class="details"> 
            <h2 class="pt-5"> Bestsellers </h2>
            <button class="text-uppercase"> Browse Now</button>
        </div>
 
    </div>
</div>
</section>

<!--Featured-->
<section id="feature" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3>Our Featured</h3>
        <hr>
        <p>Here You Can Check Out Our Featured products</p>
    </div> 
    <div class="row mx-auto container-fluid">
       
<?php include('server/get_featured_products.php');?>
<?php while($row= $featured_products->fetch_assoc()){   ?>

    <div class="product text-center col-lg-3 col-md-4 col-sm-12">
            <img class="img-fluid mb-3 " src="assets/imgs/<?php echo $row['product_image1'];?>"/>
            <div class="star"> 
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
</div>
           
            <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
            <h4 class="p-price">₦ <?php echo $row['product_price']; ?></h4>
           <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"> <button class="buy-btn">Buy Now</button></a>
        </div>
      
    <?php  }  ?>
    </div>
</section>
 
<!--Banner-->
<Section id="banner" class="my-5 py-5">
<div class="container ">
    <h4>Special Offers</h4>
    <h1>Autumn Book Collection <br> Up to 30% Off</h1>
    <a  class="btn shop-now-btn" href="shop.php" >Browse Books</a>
</div>
</Section>

<!--Cloths-->
<section id="feature" class="my-5">
    <div class="container text-center mt-5 py-5">
        <h3>Fiction</h3>
        <hr>
        <p>Explore our collection of fiction books</p>
    </div> 
    <div class="row mx-auto container-fluid">

<?php   include('server/get_dresses.php');?>
<?php while($row= $cloths_products->fetch_assoc()){   ?>

    <div class="product text-center col-lg-3 col-md-4 col-sm-12">
            <img class="img-fluid mb-3 " src="assets/imgs/<?php echo $row['product_image1'];?>"/>
           <div class="star"> 
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                
                
</div>
           
            <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
            <h4 class="p-price">₦ <?php echo $row['product_price']; ?></h4>
            <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now</button></a>
        </div>
       
        <?php  }  ?>
    </div>
</section>

<!--ball-->
<section id="ball" class="my-5">
    <div class="container text-center mt-5 py-5">
        <h3>Children's Books</h3>
        <hr>
        <p>Discover our range of children's books</p>
    </div> 
    <div class="row mx-auto container-fluid">

    <?php   include('server/get_football.php');?>
<?php while($row= $football_products->fetch_assoc()){   ?>

        <div class="product text-center col-lg-3 col-md-4 col-sm-12">
            <img class="img-fluid mb-3 " src="assets/imgs/<?php echo $row['product_image1'];?>"/>
           <div class="star"> 
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
</div>
          
            <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
            <h4 class="p-price">₦ <?php echo $row['product_price']; ?></h4>
            <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now</button></a>
        </div>
     <?php  }  ?>
    </div>
</section>

<!--Watches-->
<section id="watches" class="my-5">
    <div class="container text-center mt-5 py-5">
        <h3>Non-Fiction</h3>
        <hr>
        <p>Browse our best non-fiction books</p>
    </div> 
    <div class="row mx-auto container-fluid">

    <?php   include('server/get_watches.php');?>
<?php while($row= $watches_products->fetch_assoc()){   ?>

        <div class="product text-center col-lg-3 col-md-4 col-sm-12">
            <img class="img-fluid mb-3 " src="assets/imgs/<?php echo $row['product_image1'];?>"/>
           <div class="star"> 
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
</div>
          
            <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
            <h4 class="p-price">₦ <?php echo $row['product_price']; ?></h4>
            <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now</button></a>
        </div>

        <?php  }  ?>
        
    </div>
</section>

<!--footer-->
<Footer class="mt-5 py-5">
<div class="row container mx-auto pt-5">
    <div class="footer-one col-lg-3 col-md-6 col-sm-12">
        <img class="logo" src="assets/imgs/logo.jpg"/> 
        <p class="pt-3">We provide the best books for the best price</p>
    </div>
    <div class="footer-one col-lg-3 col-md-6 col-sm-12">
        <h5 class="pb-2">Featured</h5>
        <h5 class="pb-2">Categories</h5>
        <ul class="text-uppercase">
            <li><a href="#">Fiction</a></li>
            <li><a href="#">Non-Fiction</a></li>
            <li><a href="#">Children's Books</a></li>
            <li><a href="#">New Arrivals</a></li>
            <li><a href="#">Best Sellers</a></li>
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
            <img src="assets/imgs/book1.jpg" class="img-fluid w-25 h-100 m-2"/>
            <img src="assets/imgs/book2.jpg" class="img-fluid w-25 h-100 m-2"/>
            <img src="assets/imgs/book3.jpg" class="img-fluid w-25 h-100 m-2"/>
            <img src="assets/imgs/book4.jpg" class="img-fluid w-25 h-100 m-2"/>
            <img src="assets/imgs/book5.jpg" class="img-fluid w-25 h-100 m-2"/>
        </div>
    </div>
</div>
<div class="copyright mt-5">
    <div class="row container mx-auto">
        <div class="col-lg-3 col-md-5 col-sm-12 mb-4">
            <img src="assets/imgs/payment.jpg">
        </div>
        <div class="col-lg-3 col-md-5 col-sm-12 mb-4 text-nowrap mb-2">
            <p>E-Library @2025 All Rights Reserved</p>
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