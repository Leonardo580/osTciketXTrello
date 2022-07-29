<?php

if (isset($_GET['del'])){
    $id = $_GET['del'];
    $sql = "delete from repos where id = $id";
    $link = mysqli_connect("localhost", "anas", "22173515", "osticket");
    if (!$link)
        die( "Error: Unable to connect to MySQL." . PHP_EOL);
    mysqli_query($link, $sql);
    mysqli_close($link);
    //http_redirect("addRepositories.php");
}
$queue_columns = array(
    'Title' => array(
        'width' => '8%',
        'heading' => __('Title'),
    ),
    'Creator' => array(
        'width' => '16%',
        'heading' => __('Creator'),
        'sort_col'  => 'ticket__number',
    ),
    'date' => array(
        'width' => '20%',
        'heading' => __('Date Created'),
        'sort_col' => 'created',
    ),
    'Description' => array(
        'width' => '38%',
        'heading' => __('Description'),
        'sort_col' => 'cdata__title',
    ),

);
$refresh_url="";
$results_type="";
$showing="";
$count=0;

?>
<div id='basic_search'>
    <div class="pull-right" style="height:25px">
        <span class="valign-helper"></span>
        <?php
        require STAFFINC_DIR.'templates/tasks-queue-sort.tmpl.php';
        ?>
    </div>
    <form action="tasks.php" method="get" onsubmit="javascript:
        $.pjax({
        url:$(this).attr('action') + '?' + $(this).serialize(),
        container:'#pjax-container',
        timeout: 2000
        });
        return false;">
        <input type="hidden" name="a" value="search">
        <input type="hidden" name="search-type" value=""/>
        <div class="attached input">
            <input type="text" class="basic-search" data-url="ajax.php/tasks/lookup" name="query"
                   autofocus size="30" value="<?php echo Format::htmlchars($_REQUEST['query'], true); ?>"
                   autocomplete="off" autocorrect="off" autocapitalize="off">
            <button type="submit" class="attached button"><i class="icon-search"></i>
            </button>
        </div>
    </form>

</div>
<div class="clear"></div>
<div style="margin-bottom:20px; padding-top:5px;">
    <div class="sticky bar opaque">
        <div class="content">
            <div class="pull-left flush-left">
                <h2><a href="<?php echo $refresh_url; ?>"
                       title="<?php echo __('Refresh'); ?>"><i class="icon-refresh"></i> <?php echo
                            $results_type.$showing; ?></a></h2>
            </div>
            <div class="pull-right flush-right">
                <?php
                if ($count)
                   // echo Task::getAgentActions($thisstaff, array('status' => $status));
                ?>
            </div>
        </div>
    </div>
    <div class="clear"></div>
    <form action="tasks.php" method="POST" name='tasks' id="tasks">
        <?php csrf_token(); ?>
        <input type="hidden" name="a" value="mass_process" >
        <input type="hidden" name="do" id="action" value="" >
        <input type="hidden" name="status" value="<?php echo
        Format::htmlchars($_REQUEST['status'], true); ?>" >
        <table class="list" border="0" cellspacing="1" cellpadding="2" width="940">
            <thead>
            <tr>


                <?php
                // Query string
                $qstr="";
                // Show headers
                foreach ($queue_columns as $k => $column) {
                    echo sprintf( '<th width="%s"><a href="?sort=%s&dir=%s&%s"
                        class="%s">%s</a></th>',
                        $column['width'],
                        $column['sort'] ?: $k,
                        $column['sort_dir'] ? 0 : 1,
                        $qstr,
                        isset($column['sort_dir'])
                            ? ($column['sort_dir'] ? 'asc': 'desc') : '',
                        $column['heading']);
                }
                ?>
                <td>delete</td>
                <td>Edit</td>
            </tr>
            </thead>
            <tbody>
<!--            // display all repos from database-->

<?php
//$repos = Repositories::getAllRepositoreis();
$link = mysqli_connect("localhost", "anas", "22173515", "osticket");
if (!$link)
    die( "Error: Unable to connect to MySQL." . PHP_EOL);
$sql = "select * from repos";
$result = mysqli_query($link, $sql);
$repositories = array();
while($row = mysqli_fetch_array($result)){
    $repositories[] = $row;
}
mysqli_close($link);

foreach ($repositories as $r){
?>
            <tr>
                 <td><a href="DetailedRepo.php?idr=<?php echo $r['id']; ?>"><?php echo $r['title']; ?></a></td>
                <td><?php echo $r['creator']; ?></td>
                <td><?php echo $r['dateCreated']; ?></td>
                <td><?php echo
                    $st=$r['description'];
                if (strlen($st) > 50) {
                    $st = substr($st, 0, 100);
                    $st .= "...";
                }
                echo $st
                ?></td>
                <td><a href="Repositories.php?del=<?php echo $r['id']; ?>"><button>Delete</button></a></td>
                <td> <input type="button" onclick="editRepo(<?php echo $r['id'];?>)" value="Edit"> </td>
            </tr>
<?php } ?>
                </tbody>

            </tfoot>
        </table>

    </form>
</div>
<!--<script type="text/javascript">
    $(function() {

        $(document).off('.new-task');
        $(document).on('click.new-task', 'a.new-task', function(e) {
            e.preventDefault();
            var url = 'ajax.php/'
                +$(this).attr('href').substr(1)
                +'?_uid='+new Date().getTime();
            //console.log(url);
            url='ajax.php/repositories/add'
            var $options = $(this).data('dialogConfig');
            $.dialog(url, [201], function (xhr) {
                var tid = parseInt(xhr.responseText);
                if (tid) {
                    window.location.href = 'tasks.php?id='+tid;
                } else {
                    $.pjax.reload('#pjax-container');
                }
            }, $options);

            return false;
        });

        $('[data-toggle=tooltip]').tooltip();
    });
</script>-->
<script>
    function editRepo(id) {
        window.location.href = "http://localhost/osTicket/upload/scp/"+'editRepositories.php?edit='+id;
    }
</script>