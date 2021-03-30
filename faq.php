<?php include 'includes/nav.php'; ?>
<?php include 'includes/db.php'; ?>
<?php
$sql = 'SELECT * FROM faq';
$result = opendb($sql);
?>

<style>
    .titleText {
        font-size: 15pt;
    }
</style>
<div class="container-fluid mx-0">
    <div class="row">
        <div class="col-lg-12 px-0">
            <div class="card card-signin my-2">
            <div class="card-header">
                <h2>Frequently Asked Questions</h2>
            </div>
                <div class="card-body">
                    <div class="card card-signin">
                        <div class="accordion" id="accordionExample">
                            <?php
                            $i = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                <div class="card">
                                    <div class="card-header" id="heading<?php echo $i; ?>">
                                        <button class="btn btn-link <?php if ($i > 1) echo "collapsed"; ?>" type="button" data-toggle="collapse" data-target="#collapse<?php echo $i; ?>" aria-expanded="<?php echo ($i == 1) ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $i; ?>">
                                            <?php echo $row['question']; ?>
                                        </button>
                                    </div>
                                    <div id="collapse<?php echo $i; ?>" class="collapse <?php if ($i == 1) echo 'show'; ?>" aria-labelledby="heading<?php echo $i; ?>" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <?php echo $row['answer']; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                $i++;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/foot.php'; ?>