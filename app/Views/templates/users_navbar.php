<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
   <div class="container-fluid">
      <div class="navbar-wrapper">
         <p class="navbar-brand"><b><?= $title; ?></b></p>
      </div>
      <!-- <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
         <span class="sr-only">Toggle navigation</span>
         <span class="navbar-toggler-icon icon-bar"></span>
         <span class="navbar-toggler-icon icon-bar"></span>
         <span class="navbar-toggler-icon icon-bar"></span>
      </button> -->
      <div class="collapse navbar-collapse justify-content-end">
         <ul class="navbar-nav">
            <li class="nav-item dropdown">
               <a class="nav-link <?= user()->toArray()['is_superadmin'] == '1' ? 'text-danger' : ''; ?>" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">person</i>
                  <p class="d-lg-none d-md-block">
                     Account
                  </p>
                  <span>User : <?= user()->toArray()['username']; ?></span>
               </a>
               <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                  <a class="dropdown-item" href="<?= base_url('/logout'); ?>">Log out</a>
               </div>
            </li>
         </ul>
      </div>
   </div>
</nav>
<!-- End Navbar -->