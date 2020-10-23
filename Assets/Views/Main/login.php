
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" sizes="16x16" href="<?= web::getImageUrl('logo.png') ?>" >
    <title>Nutritionist | Login</title>
    <link rel="canonical" href="<?= SELF_DIR ?>">
    <link href="<?= SELF_DIR ?>Assets/Resources/bootstrap.min.css" rel="stylesheet">
    <link href="<?= SELF_DIR ?>Assets/Resources/signin.css" rel="stylesheet">
    <script>let BASE_DIR  ="<?= SELF_DIR ?>";</script>
    <script src="<?= SELF_DIR ?>Assets/Libraries/JQuery/jquery-3.4.0.min.js" ></script>
    <script src="<?= SELF_DIR ?>Assets/System/sys.js" ></script>
  </head>
  <body class="text-center">
    <form class="form-signin">
      <input type="hidden" name="key" value="login/submit">
        <img class="mb-4" src="<?= web::getImageUrl('logo.png') ?>" alt="" width="72" height="72">
      <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
      <label for="inputEmail" class="sr-only">Email address</label>
      <input name="email" type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
      <label for="inputPassword" class="sr-only">Password</label>
      <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password" required>
      <div class="checkbox mb-3">
        <label>
          <input type="checkbox" value="remember-me"> Remember me
        </label>
      </div>
        <button to="CT<?= time() ?>" class="btn btn-lg btn-primary btn-block submitForm" type="button" onclick="SYS.XHRForm(this);">Sign in</button>
        <div id="CT<?= time() ?>" class="CT1"></div>
      <p class="mt-5 mb-3 text-muted">&copy; <?= date("Y",time()) ?></p>
    </form>
    <script>
        $(document).on('keydown','input',function(e){
            if(e.keyCode == 13){
                e.preventDefault();
                $(this).parents("form").find(".submitForm").click();
            }
        });
    </script>
  </body>
</html>
