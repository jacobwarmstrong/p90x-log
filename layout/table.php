<?php
//table.php
//display our data as a simple table list

var_dump($request->server->all());

$users = Database::do('SELECT', $data->className, '*');
var_dump($users);
die;
?>

<div class="container mx-auto">
        <div class="col-lg-12">
        
          <?php echo display_errors(); echo display_success(); ?>
          
        <div class="clearfix">
                <h3 class="text-muted subtext my-2 float-left"><?php echo $class . 'S'; ?></h3>
                <?php if(isAdmin()) : ?>
                <a class="badge badge-success mr-2 my-2 float-right" href="add.php?pageView=table&class=<?php echo $class; ?>"><?php echo "+ Add " . $class; ?></a>
                <?php endif; ?> 
        </div>
            
            <?php if (empty($tableData)) : ?>
              No Table Data was returned.
            <?php else : ?>
                <table id="adminTable" class="table table-striped">
                <thead class="thead-dark">
                <tr>
                <?php foreach($tableData[0]['rowData'] as $key => $column) : ?>
                    <th><?php echo $key; ?></th>
                <?php endforeach; ?>
                <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($tableData as $item) : ?>
                    <tr>
                    <?php foreach($item['rowData'] as $column) : ?>
                        <td><?php echo $column; ?></td>
                    <?php endforeach; ?>
                    <td><a href="edit.php?class=<?php echo $class; ?>&id=<?php echo $item['rowId']; ?>">More</a></td>
                    <!-----Old modal via bootstrap js------>
                    <!----<td><a href="#" data-toggle="modal" data-target="#modal<?php echo $class; ?>-<?php echo $item['rowId']; ?>" class="">More</a></td>----->
                    </tr>
                    
                    <?php include( __DIR__ .'/template-parts/modal.php');
                endforeach; ?>
                
                </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
<script src="inc/editModal.js"></script>
<script src="/inc/validatePhone.js"></script>
<?php require_once __DIR__ . '/inc/footer.php'; ?>
