<?php
require('staff.inc.php');
require_once(INCLUDE_DIR . 'class.task.php');

require_once(INCLUDE_DIR . 'class.export.php');
include "class.member.php";

//if (isset($_POST['']))
$ost->addExtraHeader('<script type="text/javascript" src="js/ticket.js?e148727"></script>');
$ost->addExtraHeader('<script type="text/javascript" src="js/thread.js?e148727"></script>');
$ost->addExtraHeader('<meta name="tip-namespace" content="tasks.queue" />',
    "$('#content').data('tipNamespace', 'tasks.queue');");

$inc = "RepoTable.php";
require_once(STAFFINC_DIR . 'header.inc.php');
$link = mysqli_connect("localhost", "anas", "22173515", "osticket");
if (!$link)
    die("Error: Unable to connect to MySQL." . PHP_EOL);
$sql = "select * from repos where id = " . $_GET['idr'];
$result = mysqli_query($link, $sql);
$repository = mysqli_fetch_array($result);
mysqli_close($link);
?>
    <h1><?php echo $repository['title']; ?></h1>
    <p>
        <?php echo $repository['description']; ?>
    </p>
    <form method="post" action="">
        <label>Invite members via email</label>
        <input type="email" placeholder="email">
        <input type="submit" value="Invite">
    </form>
    <br>
    <link rel="stylesheet" href="css/Members.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css"
          integrity="sha256-2XFplPlrFClt0bIdPgpz8H7ojnk10H69xRqd9+uTShA=" crossorigin="anonymous"/>
    <div class="container mt-3 mb-4">
    <div class="col-lg-9 mt-4 mt-lg-0">
    <div class="row">
    <div class="col-md-12">
    <div class="user-dashboard-info-box table-responsive mb-0 bg-white p-4 shadow-sm">
    <!--                        change starts here -->
    <table class="table manage-candidates-top mb-0">
    <thead>
    <tr>
        <th>Candidate Name</th>
        <th class="text-center">Status</th>
        <th class="action text-right">Action</th>
    </tr>
    </thead>
    <tbody>
<?php
include "class.member.php";
$members = Member::getAllMembers();
foreach ($members as $m) { ?>
    <tr class="candidates-list">

        <td class="title">
            <div class="thumb">
                <img class="img-fluid" src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="">
            </div>
            <div class="candidate-list-details">
                <div class="candidate-list-info">
                    <div class="candidate-list-title">
                        <h5 class="mb-0"><a href="#"><?php echo $m['name'] ?></a></h5>
                    </div>
                    <div class="candidate-list-option">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-filter pr-1"></i>Information Technology</li>
                            <li><i class="fas fa-map-marker-alt pr-1"></i>Rolling Meadows, IL 60008</li>
                        </ul>
                    </div>
                </div>
            </div>
        </td>
        <td class="candidate-list-favourite-time text-center">
            <a class="candidate-list-favourite order-2 text-danger" href="#"><i class="fas fa-heart"></i></a>
            <span class="candidate-list-time order-1">Shortlisted</span>
        </td>
        <td>
            <ul class="list-unstyled mb-0 d-flex justify-content-end">
                <li><a href="#" class="text-primary" data-toggle="tooltip" title="" data-original-title="view"><i
                                class="far fa-eye"></i></a></li>
                <li><a href="#" class="text-info" data-toggle="tooltip" title="" data-original-title="Edit"><i
                                class="fas fa-pencil-alt"></i></a></li>
                <li><a href="#" class="text-danger" data-toggle="tooltip" title="" data-original-title="Delete"><i
                                class="far fa-trash-alt"></i></a></li>
            </ul>
        </td>
    </tr>
<?php } ?>
    </tbody>
    </table>
    <!--                        changes ends here-->
    <!--<div class="text-center mt-3 mt-sm-3">
        <ul class="pagination justify-content-center mb-0">
            <li class="page-item disabled"> <span class="page-link">Prev</span> </li>
            <li class="page-item active" aria-current="page"><span class="page-link">1 </span> <span class="sr-only">(current)</span></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">...</a></li>
            <li class="page-item"><a class="page-link" href="#">25</a></li>
            <li class="page-item"> <a class="page-link" href="#">Next</a> </li>
        </ul>
    </div>-->
    </div>
    </div>
    </div>
    </div>
    </div>
    <?php
    require_once(STAFFINC_DIR . 'footer.inc.php');