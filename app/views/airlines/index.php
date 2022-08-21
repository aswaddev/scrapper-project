<?php require APPROOT . '/views/inc/header.php'; ?>

<section class="jumbotron jumbotron-fluid py-2">
    <div class="container text-center">
        <h1 class="display-3">Airlines Index</h1>
    </div>
</section>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="btn btn-success mr-2">
        Export
    </div>
    <div>
        <a href="<?= URLROOT . '/airlines/getAirlineDetails' ?>" class="btn btn-outline-primary mr-2">
            Scrap Fleet Data
        </a>
        <a href="<?= URLROOT . '/airlines/links' ?>" class="btn btn-outline-secondary">
            Scrap Airlines
        </a>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Airline</th>
                <th>Link</th>
                <th>Country</th>
                <th>Fleet</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['airlines'] as $key => $airline) { ?>
                <tr>
                    <td><?= $key + 1 ?></td>
                    <td><?= $airline->name ?></td>
                    <td>
                        <a href="<?= $airline->link ?>" target="_blank"><?= $airline->link ?></a>
                    </td>
                    <td><?= $airline->country ?></td>
                    <td><?= $airline->fleet ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require APPROOT . '/views/inc/footer.php'; ?>