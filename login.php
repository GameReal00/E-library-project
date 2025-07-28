<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Login - SOT E-Library</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
     <div class="container mt-5">
          <div class="row justify-content-center">
               <div class="col-md-6">
                    <div class="card">
                         <div class="card-header">
                              <h3 class="text-center">Login to E-Library</h3>
                         </div>
                         <div class="card-body">
                              <form action="process_login.php" method="POST">
                                   <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                   </div>
                                   <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                             required>
                                   </div>
                                   <button type="submit" class="btn btn-primary w-100">Login</button>
                              </form>
                              <div class="text-center mt-3">
                                   <p>Don't have an account? <a href="register.php">Register here</a></p>
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</body>

</html>