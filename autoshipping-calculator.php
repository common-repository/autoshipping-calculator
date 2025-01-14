<?php
/*
Plugin Name: Autoshipping Calculator
Plugin URI: http://www.montway.com/
Description: The autoshipping calculator provides a free widget to your wordpress blog for instant shipping quotes.
Version: 1.0.1
Author: George Jenkins
Author URI: http://www.montway.com/
License: GPL2
*/

/*  Copyright 2013  George Jenkins  (email : g.jenkins@montway.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//add_option($name, $value);
//get_option($option);
//update_option($option_name, $newvalue);

add_action( 'admin_menu', 'calculator_options_menu' );

add_action( 'widgets_init', 'calculator_register_widget');

add_filter('the_content', 'autoshippingCalculatorContentHook');

function calculator_register_widget()
{
	register_widget( 'Autoshipping_Calculator_Widget' );
}

function calculator_options_menu()
{
	add_options_page( 'Autoshipping Calculator Options', 'Autoshipping Calculator', 'manage_options', 'autoshipping-calculator', 'autoshipping_calculator_options' );
}

function autoshippingCalculatorContentHook($content = '')
{
	$toBeReplaced = '';
	
	$currentWidth = get_option('autoshipping-calculator-width');
	if(!$currentWidth) $currentWidth = 180;
	$currentHeight = get_option('autoshipping-calculator-height');
	if(!$currentHeight) $currentHeight = 550;
	
	$toBeReplaced .= '<div style="width:100%;text-align:center;margin:10px auto;">';
		$toBeReplaced .= '<iframe id="autoshipping-calculator-hook-content" src="http://www.montway.com/affiliate.php?src=wordpress-calculator" width="'.$currentWidth.'" height="'.$currentHeight.'" style="width:'.$currentWidth.'px;height:'.$currentHeight.'px;border:none;overflow-x:hidden;" scrolling="no"></iframe>';
	$toBeReplaced .= '</div>';
	
	return str_replace("[autoshipping-calculator]", $toBeReplaced, $content);
}

function autoshipping_calculator_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	$neverSaved = true;
	$currentWidth = get_option('autoshipping-calculator-width');
	if(!$currentWidth) $currentWidth = 160;
	else $neverSaved = false;
	
	$currentHeight = get_option('autoshipping-calculator-height');
	if(!$currentHeight) $currentHeight = 600;
	else $neverSaved = false;
	
	if(isset($_POST['width']) && $_POST['width'] > 0 && isset($_POST['height']) && $_POST['height'] > 0)
	{
		if($neverSaved)
		{
			add_option('autoshipping-calculator-width', intval($_POST['width']));
			add_option('autoshipping-calculator-height', intval($_POST['height']));
			
			add_option('autoshipping-calculator-content', intval($_POST['width']).'x'.intval($_POST['height']));
		} else {
			update_option('autoshipping-calculator-width', intval($_POST['width']));
			update_option('autoshipping-calculator-height', intval($_POST['height']));
				
			update_option('autoshipping-calculator-content', intval($_POST['width']).'x'.intval($_POST['height']));
		}
		
		$currentWidth = intval($_POST['width']);
		$currentHeight = intval($_POST['height']);
	}
	
	$sizes = array(
		'160x600' => array('title'=>'160 x 600 size option', 'width'=>160, 'height'=>600),
		'225x410' => array('title'=>'225 x 410 size option', 'width'=>225, 'height'=>410),
		'250x550' => array('title'=>'250 x 550 size option', 'width'=>250, 'height'=>550),
		'300x550' => array('title'=>'300 x 550 size option', 'width'=>300, 'height'=>550),
		'600x600' => array('title'=>'600 x 600 size option', 'width'=>600, 'height'=>600),
		'960x600' => array('title'=>'960 x 600 size option', 'width'=>960, 'height'=>600),
	);
	
	?>
	<div class="wrap">
		<form name="form-autoshipping-calculator-options" method="post" action="">
			<input type="hidden" name="iframe-width" value="<?php echo $currentWidth; ?>">
			<input type="hidden" name="iframe-height" value="<?php echo $currentHeight; ?>">
			
			<p style="text-align:center;width:100%;height:auto;">
				<?php _e("&nbsp;&nbsp;&nbsp;&nbsp;Choose size:", 'autoshipping-calculator-size' ); ?> 
				<select name="form-autoshipping-calculator-size" id="form-autoshipping-calculator-size" style="width:120px;" onchange="autoshippingCalculatorSizeChanged();">
					<option value="0x0">Custom size</option>
					<?php foreach($sizes as $s) { ?>
					<option value="<?php echo $s['width']; ?>x<?php echo $s['height']; ?>"<?php echo $s['height']==$currentHeight&&$s['width']==$currentWidth?'selected="selected"':''; ?>><?php echo $s['title']; ?></option>
					<?php } ?>
				</select>
				<span id="custom-sizes-autoshipping-calculator" style="<?php if(isset($sizes[$currentWidth.'x'.$currentHeight])) { ?>display:none;<?php } else { ?>display:block;<?php } ?>">
					<?php _e("&nbsp;&nbsp;&nbsp;Custom width:", 'autoshipping-calculator-size' ); ?>
					<input type="text" style="width:120px;" name="width" id="autoshipping-calculator-width" value="<?php echo $currentWidth; ?>" class="custom-autoshipping-calculator" onkeyup="return autoshippingCalculatorSizeChanged();" />
					<br/>
					<?php _e("Custom height:", 'autoshipping-calculator-size' ); ?>
					<input type="text" style="width:120px;" name="height" id="autoshipping-calculator-height" value="<?php echo $currentHeight; ?>" class="custom-autoshipping-calculator" onkeyup="return autoshippingCalculatorSizeChanged();" />
				</span>
				<span style="display:block">
					<?php _e("Use this autoshipping calculator in your pages and posts by adding [autoshipping-calculator] to the content!", 'autoshipping-calculator-size' ); ?>
				</span>
			</p>
			<div id="autoshipping-calculator-previewHolder" style="width:100%;">
				<div style="text-align:center;margin:10px auto;">
					<iframe id="autoshipping-calculator-frame" src="http://www.montway.com/affiliate.php?src=wordpress-calculator" width="<?php echo $currentWidth; ?>" height="<?php echo $currentHeight; ?>" style="width:<?php echo $currentWidth; ?>px;height:<?php echo $currentHeight; ?>px;border:none;overflow-x:hidden;" scrolling="no"></iframe>
				</div>
			</div>
			
			<hr />
			
			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<script type="text/javascript">
		function autoshippingCalculatorSizeChanged()
		{
			var sizes = document.getElementById('form-autoshipping-calculator-size');
			var width = document.getElementById('autoshipping-calculator-width');
			var height = document.getElementById('autoshipping-calculator-height');
			var selectedSize = sizes.options[sizes.selectedIndex].value;
			var frame = document.getElementById('autoshipping-calculator-frame');
			
			if(selectedSize == '0x0')
			{
				document.getElementById('custom-sizes-autoshipping-calculator').style.display = 'block';
			}
			else if(selectedSize.indexOf('x'))
			{
				document.getElementById('custom-sizes-autoshipping-calculator').style.display = 'none';
				var splitSizes = selectedSize.split('x')
				if(splitSizes && splitSizes.length == 2 && parseInt(splitSizes[0]) > 0 && parseInt(splitSizes[1]) > 0)
				{
					width.value = parseInt(splitSizes[0]);
					height.value = parseInt(splitSizes[1]);
				}
			}

			if(width.value != parseInt(width.value) && height.value != parseInt(height.value))
			{
				width.value = parseInt(width.value);
				height.value = parseInt(height.value);
			}

			if(parseInt(width.value) > 0 && parseInt(height.value) > 0)
			{
				frame.width = parseInt(width.value);
				frame.style.width = parseInt(width.value)+'px';
				frame.height = parseInt(height.value);
				frame.style.height = parseInt(height.value)+'px';
			}
			
			return false;
		}
	</script>
	<?php
}

class Autoshipping_Calculator_Widget extends WP_Widget {
	
	private $width = 160;
	private $height = 600;

	public function __construct() {
		// widget actual processes
		$this->id_base = empty($id_base) ? preg_replace( '/(wp_)?widget_/', '', strtolower(get_class($this)) ) : strtolower($id_base);
		$this->name = 'Autoshipping Calculator';
		
		$control_options = array();
		$widget_options = array(
			'description' => 'Widget for Autoshipping Calculator!'
		);
		
		$this->option_name = 'widget_' . $this->id_base;
		$this->widget_options = wp_parse_args( $widget_options, array('classname' => $this->option_name) );
		$this->control_options = wp_parse_args( $control_options, array('id_base' => $this->id_base) );
		
		$currentWidth = get_option('autoshipping-calculator-widget-width');
		if($currentWidth) $this->width = $currentWidth;
		
		$currentHeight = get_option('autoshipping-calculator-widget-height');
		if($currentHeight) $this->height = $currentHeight;
	}

	public function widget( $args, $instance ) {
		// outputs the content of the widget
		extract($args);
		$c = ! empty( $instance['count'] ) ? '1' : '0';
		$d = ! empty( $instance['dropdown'] ) ? '1' : '0';
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Autoshipping Calculator') : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		?>
		<div style="text-align:center;margin:10px auto;">
			<iframe id="<?php echo $this->id_base.'-autoshipping-calculatort'; ?>" src="http://www.montway.com/affiliate.php?src=wordpress-calculator" width="<?php echo $this->width; ?>" height="<?php echo $this->height; ?>" style="width:<?php echo $this->width; ?>px;height:<?php echo $this->height; ?>px;border:none;overflow-x:hidden;" scrolling="no"></iframe>
		</div>
		<?php
		echo $after_widget;
	}
	
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance )
	{
		$sizes = array(
			'160x600' => array('title'=>'160 x 600 size option', 'width'=>160, 'height'=>600),
			'225x410' => array('title'=>'225 x 410 size option', 'width'=>225, 'height'=>410),
			'250x550' => array('title'=>'250 x 550 size option', 'width'=>250, 'height'=>550),
			'300x550' => array('title'=>'300 x 550 size option', 'width'=>300, 'height'=>550),
			'600x600' => array('title'=>'600 x 600 size option', 'width'=>600, 'height'=>600),
			'960x600' => array('title'=>'960 x 600 size option', 'width'=>960, 'height'=>600),
		);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e("Choose size:", 'autoshipping-calculator-size' ); ?> </label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_id( 'size' ); ?>">
				<?php foreach($sizes as $s) { ?>
				<option value="<?php echo $s['width']; ?>x<?php echo $s['height']; ?>" <?php echo $s['height']==$this->height&&$s['width']==$this->width?'selected="selected"':''; ?>><?php echo $s['title']; ?></option>
				<?php } ?>
			</select>
		</p>
		<?php 
	}
		
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		if(isset($_POST[$this->get_field_id( 'size' )]) && $_POST[$this->get_field_id( 'size' )])
		{
			$val = $_POST[$this->get_field_id( 'size' )];
			list($width, $height) = explode('x', $val);
			
			if(isset($width) && isset($height) && $width > 0 && $height > 0)
			{
				if(get_option('autoshipping-calculator-widget-width'))
				{
					update_option('autoshipping-calculator-widget-width', intval($width));
					update_option('autoshipping-calculator-widget-height', intval($height));
				} else {
					add_option('autoshipping-calculator-widget-width', intval($width));
					add_option('autoshipping-calculator-widget-height', intval($height));
				}
			}
			$this->width = intval($width);
			$this->height = intval($height);
		}
	
		return $instance;
	}
}