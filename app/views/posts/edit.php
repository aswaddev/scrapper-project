<?php require APPROOT . '/views/inc/header.php'; ?>
<section class="">
    <a href="<?php echo URLROOT ?>/posts" class="btn btn-light"><i class="fa fa-backward"></i> &nbsp;Back</a>
    <div class="card card-body bg-light my-3">
        <h2 class="text-center">Edit Post</h2>
        <p class="text-center">Edit post details in the form below and submit to update</p>
        <?php flash('edit_err_message'); ?>
        <form action="<?php echo URLROOT; ?>/posts/edit/<?php echo $data['id']; ?>" method="post">
            <div class="form-group">
                <label for="title">Title:<sup>*</sup> </label>
                <input type="text" name="title" class="form-control form-control-lg" value="<?php echo $data['title']; ?>">
            </div>
            <div class="form-group">
                <label for="body">Body:<sup>*</sup> </label>
                <textarea id="body" name="body" class="form-control form-control-lg"><?php echo $data['body']; ?></textarea>
            </div>
            <input type="submit" value="Update" class="btn btn-success">
        </form>
    </div>
</section>
<?php require APPROOT . '/views/inc/footer.php'; ?>