<?php
$dbname='church';
$conID=mysql_pconnect('localhost', 'root', '');
@mysql_select_db($dbname, $conID);
// Get All Column //
$sql='Select * From '.$_GET['table'];
$result=@mysql_query($sql, $conID);
for($i=0; $i < @mysql_num_fields($result); $i++){
	// Get Column //
	$meta=@mysql_fetch_field($result, $i);
	if($meta->primary_key){
		$primary_key=$meta->name;
	}
	$columnArr[$i]=$meta->name;
}
@mysql_free_result($result);
// Main Templat //	
if($_GET['act'] == 'main'){
?>
<h3><?php echo $_GET['table']  ?> Data</h3>
<ul class="button-group">
	<li><a href="#" class="mainView small button secondary"><i class="foundicon-star"></i> Main</a></li>
	<li><a href="#" class="createView small button secondary"><i class="foundicon-plus"></i> Create</a></li>
	<li><a href="#" class="save small button secondary"><i class="foundicon-inbox"></i> Save</a></li>
</ul>
<table width='100%'>
<thead>
<tr>
<?php for($i=0; $i < count($columnArr); $i++){ ?>	
<th><?php echo $columnArr[$i] ?></th>
<?php } ?>
<th></th>
</tr>
</thead>
<tbody>
<% _.each(models, function(model) { %>
<tr>
<?php for($i=0; $i < count($columnArr); $i++){ ?>	
<td><%= model.<?php echo $columnArr[$i] ?> %></td>
<?php } ?>
<td>
<a href='#' class='readView button small' data-id="<%= model.<?php echo $primary_key ?> %>"><i class="foundicon-page"></i></a>
<a href='#' class='updateView button small' data-id="<%= model.<?php echo $primary_key ?> %>"><i class="foundicon-tools"></i></a>
<a href='#' class='deleteSubmit button small' data-id="<%= model.<?php echo $primary_key ?> %>"><i class="foundicon-remove"></i></a>
</td>
</tr>
<% }); %>
</tbody>
</table>
<?php
}
// Create Templat //
if($_GET['act'] == 'create'){
?>
<h3><?php echo $_GET['table']  ?> Create</h3>
<form>
<?php for($i=0; $i < count($columnArr); $i++){ ?>	
	<div class='row'>
		<div class='large-1 columns'><?php echo $columnArr[$i] ?></div>
		<div class='large-9 columns'><input type='text' name='<?php echo $columnArr[$i] ?>' id='<?php echo $columnArr[$i] ?>' value='' data-check='null' /></div>
	</div>
<?php } ?>
	<div class='row'>
		<div class='large-1 columns'></div>
		<div class='large-9 columns'><a href='#' class='createSubmit button small success radius'>Submit</a></div>
	</div>
</form>
<?php
}
// Read Templat //
if($_GET['act'] == 'read'){
?>
<h3><?php echo $_GET['table']  ?> Read</h3>
<form>
<?php for($i=0; $i < count($columnArr); $i++){ ?>	
	<div class='row'>
		<div class='large-1 columns'><?php echo $columnArr[$i] ?></div>
		<div class='large-9 columns'><%= <?php echo $columnArr[$i] ?> %></div>
	</div>
<?php } ?>
</form>
<?php
}
// Update Templat //
if($_GET['act'] == 'update'){
?>
<h3><?php echo $_GET['table']  ?> Update</h3>
<form>
<?php for($i=0; $i < count($columnArr); $i++){ ?>	
	<div class='row'>
		<div class='large-1 columns'><?php echo $columnArr[$i] ?></div>
		<div class='large-9 columns'><input type='text' name='<?php echo $columnArr[$i] ?>' id='<?php echo $columnArr[$i] ?>' value='<%= <?php echo $columnArr[$i] ?> %>' data-check='null' /></div>
	</div>
<?php } ?>
	<div class='row'>
		<div class='large-1 columns'></div>
		<div class='large-9 columns'><a href='#' class='updateSubmit button small success radius'>Submit</a></div>
	</div>
</form>
<?php
}
?>