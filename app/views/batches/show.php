<?php require APPROOT . '/views/inc/header.php'; ?>

<a href="<?= URLROOT . '/batches/index' ?>" class="btn btn-secondary mb-2">
    Back
</a>

<section class="jumbotron jumbotron-fluid py-2">
    <div class="container text-center">
        <h1 class="display-4">Airlines - Batch (<?= $data['batch']->name ?>)</h1>
    </div>
</section>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <a href='<?= URLROOT . '/batches/export/' . $data['batch']->id ?>' class="btn btn-success">
            Export
        </a>
    </div>
    <div>
        <a href="<?= URLROOT . '/airlines/links/' . $data['batch']->id  ?>" class="btn btn-outline-secondary">
            Scrap Airlines
        </a>
        <button id="checkAll" class="btn btn-outline-secondary">Check / Uncheck All</button>
        <button onclick='startImporting()' style='display: none' id='scrapFleetBtn' class="btn btn-outline-primary mr-2">
            Scrap Fleet Data
        </button>
        <!-- <form class="d-inline-block" action="<?= URLROOT . '/fleets/scrap/' . $data['batch']->id ?>" method="post">
            <input type="hidden" name="airlines" id="selectedAirlines" value='[]'>
            <button type="submit" id='scrapFleetBtn' class="btn btn-outline-primary mr-2" style='display: none'>
                Scrap Fleet Data
            </button>
        </form> -->
    </div>
</div>

<?php flash('import_success'); ?>

<div class="table-responsive pb-5">
    <table id="dataTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th></th>
                <th>#</th>
                <th>Airline</th>
                <th>Status</th>
                <th>Link</th>
                <th>Country</th>
                <th>Fleet</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($data['airlines'])) { ?>
                <?php foreach ($data['airlines'] as $key => $airline) { ?>
                    <tr>
                        <td>
                            <input value='<?= $airline->id ?>' onchange="syncSelectedAirlines()" type="checkbox" name="selectedAirlines[]">
                        </td>
                        <td><?= $key + 1 ?></td>
                        <td><?= $airline->name ?></td>
                        <td class="<?= $airline->imported_at ? 'table-success' : 'table-danger' ?>">
                            <?= $airline->imported_at ? 'Imported' : 'Not Imported' ?>
                        </td>
                        <td>
                            <a href="<?= $airline->link ?>" target="_blank"><?= $airline->link ?></a>
                        </td>
                        <td><?= $airline->country ?></td>
                        <td><?= $airline->fleet ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="5">No Data</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>
    var scrapFleetBtn = document.getElementById('scrapFleetBtn');
    var airlinesToImport = [];

    function syncSelectedAirlines() {
        var checkboxes = document.querySelectorAll('input[name="selectedAirlines[]"]:checked'),
            values = [];

        Array.prototype.forEach.call(checkboxes, function(el) {
            values.push(el.value);
        });

        airlinesToImport = values;
        // selectedAirlines.value = JSON.stringify(values);

        if (values.length) {
            scrapFleetBtn.style.display = 'inline-block';
        } else {
            scrapFleetBtn.style.display = 'none';
        }
    }
</script>

<script>
    function check(checked = true) {
        const checkboxes = document.querySelectorAll('input[name="selectedAirlines[]"]');

        checkboxes.forEach((checkbox) => {
            checkbox.checked = checked;
        });

        syncSelectedAirlines();
    }

    function checkAll() {
        check();
        this.onclick = uncheckAll;
    }

    function uncheckAll() {
        check(false);
        this.onclick = checkAll;
    }

    const btn = document.querySelector('#checkAll');
    btn.onclick = checkAll;
</script>

<script>
    function startImporting() {
        if (!airlinesToImport.length) return alert('No airlines selected');

        Swal.fire({
            title: `Import Process Started!`,
            text: `You'll be alerted along the way!`,
            toast: true,
            icon: 'success',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        airlinesToImport.forEach(airline => {
            axios.post("<?= URLROOT . '/fleets/scrap/' . $data['batch']->id ?>", {
                airlines: [airline]
            }).then(res => {
                Swal.fire({
                    title: `Import Successfull!`,
                    text: `Airline #${airline} imported successfully`,
                    toast: true,
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            }).catch(err => {
                Swal.fire({
                    title: `Import Failed!`,
                    text: `Airline #${airline} failed to import`,
                    toast: true,
                    icon: 'error',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            });
        });
    }
</script>

<?php require APPROOT . '/views/inc/footer.php'; ?>