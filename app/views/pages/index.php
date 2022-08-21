<?php require APPROOT . '/views/inc/header.php';?>
<section class="jumbotron jumbotron-fluid">
    <div class="container text-center">
        <h1 class="display-3"><?php echo $data['title']; ?></h1>
        <p class="lead"><?php echo $data['description']; ?></p>
    </div>
</section>

<?php require APPROOT . '/views/inc/footer.php';?>