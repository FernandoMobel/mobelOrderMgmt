

<?php include_once 'includes/nav.php';?>

<div class="container">
    <div class="row">
      <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
        <div class="card card-signin my-5">
          <div class="card-body">
            <h5 class="card-title text-center">Sign In</h5>
            <form class="form-signin" action="auth.php" method="post">
              <div class="floating-label">
              <label for="inputEmail">Email address/username:</label>
                <input name="email" type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
              </div><br>
              <div class="floating-label">
              	<label for="inputPassword">Password:</label>
                <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
              </div>
              <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" class="custom-control-input" id="customCheck1">
                <label class="custom-control-label" for="customCheck1">Remember password</label>
              </div>
              <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Sign in</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>


<?php include 'includes/foot.php';?>