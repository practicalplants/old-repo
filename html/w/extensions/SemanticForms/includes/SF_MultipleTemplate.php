<?php
class SFMultipleTemplate{

	public $template_name,
		   $disabled,
		   $section,
		   $add_button_text,
		   $instance = 0;

	public static $instances = array();

	public function getInstance($template_name){
		if(!isset(self::$instances[$template_name]))
			self::$instances[$template_name] = new self($template_name);
		return self::$instances[$template_name];
	}

	private function __construct($template_name){
		$this->template_name = $template_name;
		$this->add_button_text = wfMsg( 'sf_formedit_addanother' );
	}

	public function setDisabled($bool=true){
		$this->disabled = $bool;
	}
	public function setSection($section){
		// Add the character "a" onto the instance number of this input
		// in the form, to differentiate the inputs the form starts out
		// with from any inputs added by the Javascript.
		$this->section = str_replace( '[num]', '['.$this->instance.'a]', $section );
	}

	public function increment(){
		$this->instance++;
	}
		/**
	 * Creates the HTML for the inner table for every instance of a
	 * multiple-instance template in the form.
	 */
	public function innerHTML( ) {
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
			<td>$this->section</td>
			<td class="removeButton">$removeButton</td>
			<td class="instanceRearranger">
			<img src="$sfgScriptPath/skins/rearranger.png" $rearranger />
			</td>
			</tr>
			</table>
END;
		
		wfRunHooks( 'sfMultipleInstanceTemplateInnerHTML', array( &$html, &$this->section, $this ) );

		return $html;
	}

	/**
	 * Creates the HTML for a single instance of a multiple-instance template;
	 * plus the end tags for the full multiple-instance HTML.
	 */
	public function itemHTML() {

		//wrap the content in the inner html
		
		$content = $this->innerHTML();

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

	public function adderHTML(){
		//wrap the content in the inner html
		$content = $this->innerHTML();

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

	function addButtonHTML(){
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

	function beforeHTML(){
		$html = "\t" . '<div class="multipleTemplateWrapper">' . "\n"
				.  "\t" . '<div class="multipleTemplateList">' . "\n";

		wfRunHooks( 'sfMultipleInstanceTemplateBeforeHTML', array( &$html, $this ) );

		return $html;
	}

	function afterHTML(){
		$button = $this->addButtonHTML();

		$html = '</div>'
				. $button
				.'</div>';

		wfRunHooks( 'sfMultipleInstanceTemplateAfterHTML', array( &$html, $this ) );

		return $html;
	}

}