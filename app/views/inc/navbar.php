<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3">
    <div class="container">
        <a class="navbar-brand" href="<?php echo URLROOT; ?>"><?php echo SITENAME ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item  <?php echo ($data['active'] == 'home' ? 'active' : ''); ?>">
                    <a class="nav-link" href="<?php echo URLROOT; ?>">Home</a>
                </li>

                <?php if (isset($_SESSION['user_id'])) : ?>
                    <li class="nav-item  <?php echo ($data['active'] == 'batches/index' ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo URLROOT; ?>/batches/index">Scrapper</a>
                    </li>
                <?php endif ?>
            </ul>

            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Welcome <?php echo $_SESSION['user_name']; ?>
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?php echo URLROOT . '/users/logout'; ?>">
                                Logout
                            </a>
                        </div>
                    </li>
                <?php else : ?>
                    <li class="nav-item <?php echo ($data['active'] == 'login' ? 'active' : ''); ?>">
                        <a class="nav-link" href="<?php echo URLROOT . '/users/login'; ?>">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>