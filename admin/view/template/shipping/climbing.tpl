<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-option" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-option" class="form-horizontal">
          <table id="option-value" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <td class="text-left"><?php echo $entry_option_value; ?></td>
                <td class="text-left"><?php echo $entry_image; ?></td>
                <td class="text-right"><?php echo $entry_sort_order; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody>

              <?php $option_value_row = 0; ?>

              <?php foreach ($options as $option) { ?>
                <tr id="option-value-row<?php echo $option_value_row; ?>">

                  <td class="text-right">
                    <p class="text-left"><?php echo $weight; ?></p>
                    <input type="text" name="option_value[<?php echo $option_value_row; ?>][weight]" value="<?php echo $option['weight']; ?>" class="form-control" /></td>
                  </td>

                  <td class="text-right">
                    <p class="text-left"><?php echo $price; ?></p>
                    <input type="text" name="option_value[<?php echo $option_value_row; ?>][price]" value="<?php echo $option['price']; ?>" class="form-control" /></td>
                  </td>

                  <td class="text-left"></td>
                  <td class="text-left"></td>
              </tr>
              <?php $option_value_row++; ?>
              <?php } ?>

            </tbody>
          </table>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript"><!--

$('select[name=\'type\']').trigger('change');

var option_value_row = <?php echo $option_value_row; ?>;

function addOptionValue() {
	html  = '<tr id="option-value-row' + option_value_row + '">';	

    html += '  <td class="text-right"><p class="text-left"><?php echo $weight; ?></p><input type="text" name="option_value[' + option_value_row + '][weight]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
	html += '  <td class="text-right"><p class="text-left"><?php echo $price; ?></p><input type="text" name="option_value[' + option_value_row + '][price]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';

	html += '  <td class="text-left"><button type="button" onclick="$(\'#option-value-row' + option_value_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';	
	
	$('#option-value tbody').append(html);
	
	option_value_row++;
}
//--></script></div>
<?php echo $footer; ?>