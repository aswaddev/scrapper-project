<?php require APPROOT . '/views/inc/header.php'; ?>
<div class="w-75 w-lg-50 m-auto">
    <?php flash('login_successfull'); ?>
    <?php flash('post_message'); ?>
    <section class="row my-5">
        <div class="col-md-8">
            <h1 class="mute"><i class="far fa-newspaper"></i> News Feeds</h1>
        </div>
        <div class="col-md-4">
            <a href="<?php echo URLROOT; ?>/posts/add" class="btn btn-primary float-right">
                <i class="fas fa-pen"></i> Add Post
            </a>
        </div>
    </section>
    <?php foreach ($data['posts'] as $post) : ?>
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="card-title"><?php echo $post->title; ?></h4>
                <div class="bg-light p-1 mb-3">
                    Posted by <?php echo $post->author; ?> on <?php echo $post->created_at; ?>
                </div>
                <p class="card-text"><?php echo htmlspecialchars_decode($post->body); ?></p>
                <a href="<?php echo URLROOT; ?>/posts/show/<?php echo $post->id; ?>" class="btn btn-dark float-right">Read
                    More</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php require APPROOT . '/views/inc/footer.php'; ?>