<?php
class Zend_View_Helper_Submenu extends Zend_View_Helper_Abstract{
	function submenu(){
		echo '<ul>';
		foreach($this->submenu as $item){
			echo '<li><a href="'.$item->url.'">'.$item->name.'</a></li>';
		}
		echo '</ul>';
	}
}