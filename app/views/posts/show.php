<?php require APPROOT . '/views/inc/header.php'; ?>
<section class="">
    <div class="w-lg-75 m-auto">
        <a href="<?php echo URLROOT ?>/posts" class="btn btn-light mb-3"><i class="fa fa-backward"></i> &nbsp;Back</a>
        <?php flash('edit_message'); ?>
        <h1><?php echo $data['post']->title; ?></h1>
        <div class="bg-secondary text-white p-2 mb-3">
            Posted by <?php echo $data['post']->author; ?> on <?php echo $data['post']->created_at; ?>
        </div>
        <p><?php echo htmlspecialchars_decode($data['post']->body); ?></p>
    </div>
    <?php if ($data['post']->author_id == $_SESSION['user_id']) : ?>
        <hr>
        <a href="<?php echo URLROOT; ?>/posts/edit/<?php echo $data['post']->id; ?>" class="btn btn-dark">Edit</a>
        <form action="<?php echo URLROOT; ?>/posts/delete/<?php echo $data['post']->id; ?>" method="post" class="float-right">
            <input type="submit" value="Delete" class="btn btn-danger">
        </form>
    <?php endif; ?>
</section>
<?php require APPROOT . '/views/inc/footer.php'; ?>