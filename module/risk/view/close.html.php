<?php include "../../common/view/header.html.php";?>
<?php include '../../common/view/kindeditor.html.php';?>
<div id='mainContent' class='main-content'>
 <div class='main-header'>
    <h2>
      <span class='prefix label-id'><strong><?php echo $risk->id;?></strong></span>
      <?php echo "<span title='$risk->name'>" . $risk->name . '</span>';?>
    </h2>
  </div> 
  <form class='load-indicator main-form' method='post' target='hiddenwin'>
    <table class='table table-form'>
      <tbody>
        <tr>
          <th class='w-100px'><?php echo $lang->risk->resolvedBy;?></th>
          <td><?php echo html::select('resolvedBy', $users, $this->app->user->account, "class='form-control chosen'");?></td>
          <td></td>
        </tr>
        <tr>
          <th class='w-100px'><?php echo $lang->risk->closedDate;?></th>
          <td><?php echo html::input('actualClosedDate', helper::today(),  "class='form-control form-date'");?></td>
          <td></td>
        </tr>       <tr>
          <th><?php echo $lang->risk->resolution;?></th>
          <td colspan='2'><?php echo html::textarea('resolution', '', "rows='6' class='form-control kindeditor' hidefocus='true'");?></td>      </tr>
        <tr>
          <td class='text-center form-actions' colspan='3'><?php echo html::submitButton(); ?></td>        </tr>
      </tbody>
    </table>
  </form>
</div>
<?php include "../../common/view/footer.html.php";?>

