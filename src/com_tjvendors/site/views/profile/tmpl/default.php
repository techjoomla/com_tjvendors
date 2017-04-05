<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="container-fluid">
	<form action="" method="post" name="adminForm" id="adminForm">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#Vendor"><?php echo JText::_('COM_TJVENDORS_TAB_VENDOR_INFO');?></a></li>
			<li><a data-toggle="tab" href="#Payment"><?php echo JText::_('COM_TJVENDORS_TAB_GATEWAY');?></a></li>
		</ul>
		<div class="tab-content">
			<div id="Vendor" class="tab-pane fade in active">
				<h3>Shows Vendor information</h3>
			</div>
			<div id="Payment" class="tab-pane fade">
				<div>
					<?php
					$TjvendorsController = new TjvendorsController;
					$html = $TjvendorsController->pregetHTML();

					for ($i = 0; $i < count($html); $i++)
					{
						$key = key($html);
						$val = $html[$key];

						if (!empty($val))
						{
							echo $val ." <br> ";
						}

						next($html);
					}?>
				</div>
			</div>
		</div>
	</form>
</div>
