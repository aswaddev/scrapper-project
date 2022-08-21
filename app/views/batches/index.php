<?php require APPROOT . '/views/inc/header.php'; ?>

<section class="jumbotron jumbotron-fluid py-2">
    <div class="container text-center">
        <h1 class="display-3">Batches Index</h1>
    </div>
</section>

<div class="text-right">
    <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#createBatchModal">
        Create New
    </button>
</div>

<!-- Modal -->
<div class="modal fade" id="createBatchModal" tabindex="-1" aria-labelledby="createBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBatchModalLabel">
                    Create New Batch
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= URLROOT . '/batches/store' ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" required id="name" name="name" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Batch</th>
                <th>Created At</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['batches'] as $key => $batch) { ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= $batch->name ?></td>
                    <td>
                        <?= $batch->created_at ?>
                    </td>
                    <td><?= $batch->updated_at ?></td>
                    <td>
                        <a class="btn btn-primary" href="<?= URLROOT . '/batches/show/' . $batch->id ?>">
                            View
                        </a>
                        <a href='<?= URLROOT ?>/batches/delete/<?= $batch->id ?>' onclick="confirm('Are you sure you want to delete this batch? All data will be lost.')" class="btn btn-danger">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>