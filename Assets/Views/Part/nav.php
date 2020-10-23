<header>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark" >
        <a class="navbar-brand" href="<?= SELF_DIR ?>">
            <img height="30px" src="<?=WEB::getImageURL("logo.png"); ?>" alt="" srcset="">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="<?= SELF_DIR ?>home">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item"> <a class="nav-link" href="<?= SELF_DIR ?>about-us">About Us</a></li>
                <?php if(!isset($_SESSION[SI]['user'])){  ?>
                    <li class="nav-item"> <a class="nav-link" href="<?= SELF_DIR ?>login">Login</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= SELF_DIR ?>register">Register</a></li>
                <?php } else {?>
                    <li class="nav-item"> <a class="nav-link" href="<?= SELF_DIR ?>profile">Profile</a></li>
                    <li class="nav-item"> <a class="nav-link" href="<?= SELF_DIR ?>logout">Logout</a></li>
                <?php } ?>
            </ul>
            <form class="form-inline mt-2 mt-md-0">
                <input class="form-control mr-sm-2" name="q" type="text" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </form>
        </div>
    </nav>
</header>
<main role="main">