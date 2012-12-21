<?php
class SFMultipleInstanceTemplate{

	public $template_name;
	public $disabled;
	public $section;
	public $add_button_text;
	public $instance = 0;

	public $instances = array();

	/**
	 * Constructor
	 * @param String $template_name The template name to parse
	 */
	public function __construct($template_name){
		$this->template_name = $template_name;
		$this->add_button_text = wfMsg( 'sf_formedit_addanother' );
	}

	/**
	 * Set the fields to be rendered as disabled
	 * @param boolean $bool True to disable fields, false to enable (they are enabled by default)
	 */
	public function setDisabled($bool=true){
		$this->disabled = $bool;
	}

	/**
	 * Add an instance. This will be rendered when getHTML is called.
	 * @param String $instance A HTML string generated from the fields in this instance
	 */
	public function addInstance($instance){
		$this->instances[] = $instance;
	}

	/**
	 * Iterate through all instances and return a HTML string of the entire multiple instance template list
	 * @return String HTML of all instance with a hidden template and add button
	 */
	public function getHTML(){
		$html = $this->beforeHTML();
		$last_inst = count($this->instances)-1;
		foreach($this->instances as $i=>$inst){
			//if we're on the last instance, it's the blank one which acts as a template adder
			if($i == $last_inst){
				$html .= $this->adderHTML( $inst );
			}else{
				$html .= $this->itemHTML($inst, $i);
			}
		}
		$html .= $this->afterHTML();
		return $html;
	}

	/**
	 * Increment the instance count
	 */
	public function increment(){
		$this->instance++;
	}

	/**
	 * Creates the HTML for the inner table for every instance of a
	 * multiple-instance template in the form.
	 *
	 * @return String Return a html string of the inner content of each row
	 */
	public function innerHTML( $instance ) {
		global $sfgTabIndex, $sfgScriptPath;

		$attributes = array(
			'tabindex' => $sfgTabIndex,
			'class' => 'remover',
		);

		$rearranger = 'class="rearrangerImage"';

		if ( $this->disabled ) {
			$attributes['disabled'] = 'disabled';
			$rearranger = '';
		}

		$removeButton = Html::input( null, wfMsg( 'sf_formedit_remove' ), 'button', $attributes );

		$html = <<<END
			<table>
			<tr>
			<td>$instance</td>
			<td class="removeButton">$removeButton</td>
			<td class="instanceRearranger">
			<img src="$sfgScriptPath/skins/rearranger.png" $rearranger />
			</td>
			</tr>
			</table>
END;
		
		wfRunHooks( 'sfMultipleInstanceTemplateInnerHTML', array( &$html, &$instance, $this ) );

		return $html;
	}



	/**
	 * Creates the HTML for a single instance of a multiple-instance template
	 * Modify the $html value passed to the hook 'sfMultipleInstanceTemplateHTML' to override the HTML output.
	 * @return String Returns the HTML string for an item row
	 */
	public function itemHTML($instance, $instance_no) {

		//wrap the content in the inner html
		
		$content = str_replace( '[num]', '['.$instance_no.'a]', $this->innerHTML($instance, $instance_no) );

		$html = "\t\t" . Html::rawElement( 'div',
			array(
				// The "multipleTemplate" class is there for
				// backwards-compatibility with any custom CSS on people's
				// wikis before SF 2.0.9.
				'class' => "multipleTemplateInstance multipleTemplate"
			),
			$content
		) . "\n";

		wfRunHooks( 'sfMultipleInstanceTemplateHTML', array( &$html, $content, $this ) );
		

		return $html;
	}

	/**
	 * Creates the HTML for the hidden row which will be cloned as new rows on the frontend
	 * Modify the $html value passed to the hook 'sfMultipleInstanceTemplateAdderHTML' to override the HTML output.
	 * @return String Returns the HTML string for the hidden 'template' row to clone for new rows
	 */
	public function adderHTML( $instance ){
		//wrap the content in the inner html
		$content = $this->innerHTML( $instance );

		$html = "\t\t" . Html::rawElement( 'div',
				array(
					'class' => "multipleTemplateStarter",
					'style' => "display: none",
				),
				$content
			) . "\n";
			
		wfRunHooks( 'sfMultipleInstanceTemplateAdderHTML', array( &$html, &$content, $this ) );

		return $html;
	}

	/**
	 * Creates the HTML for the add button to add a new row.
	 * Modify the $html value passed to the hook 'sfMultipleInstanceTemplateAddButtonHTML' to override the HTML output.
	 * @return String Returns the HTML string for the add button
	 */
	public function addButtonHTML(){
		global $sfgTabIndex;

		$attributes = array(
			'tabindex' => $sfgTabIndex,
			'class' => 'multipleTemplateAdder',
		);
		if ( $this->disabled ) 
			$attributes['disabled'] = true;

		$html = Html::input( null, Sanitizer::decodeCharReferences( $this->add_button_text ), 'button', $attributes );

		wfRunHooks( 'sfMultipleInstanceTemplateAddButtonHTML', array( &$html, $this->add_button_text, $attributes, $this ) );

		return $html;
	}

	/**
	 * Crates the HTML which is placed before the template instances are output
	 * Modify the $html value passed to the hook 'sfMultipleInstanceTemplateBeforeHTML' to override the HTML output.
	 * @return String The HTML string to append before any template instance are output
	 */
	public function beforeHTML(){
		$html = "\t" . '<div class="multipleTemplateWrapper">' . "\n"
				 .  "\t" . '<div class="multipleTemplateList">' . "\n";

		wfRunHooks( 'sfMultipleInstanceTemplateBeforeHTML', array( &$html, $this ) );

		return $html;
	}

	/**
	 * Creates the HTML which is placed after the template instances are output. 
	 * Modify the $html value passed to the hook 'sfMultipleInstanceTemplateAfterHTML' to override the HTML output.
	 * @return String The HTML string to append after all template instances have been output
	 */
	public function afterHTML(){
		$button = $this->addButtonHTML();
		$html = '</div>' . $button . '</div>';

		wfRunHooks( 'sfMultipleInstanceTemplateAfterHTML', array( &$html, &$button, $this ) );

		return $html;
	}

}