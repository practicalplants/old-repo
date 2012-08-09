<?php 
class PracticalPlants_Masthead{
	public $tabs = array(
		'wiki'=>array(
			'title'=>'Wiki',
			'url'=>'/wiki'
		),
		'community'=>array(
			'title'=>'Community',
			'url'=>'/community'
		),
		'blog'=>array(
			'title'=>'Blog',
			'url'=>'/blog'
		),
		'account'=>array(
			'title'=> 'Login',
			'url'=>'/sso'
		)
	);
	
	public $active_tab = 'wiki';
	
	public function __construct($opts = array()){
		if(isset($opts['active_tab'])){
			$this->setActiveTab($opts['active_tab']);
		}
		if(isset($opts['search'])){
			$this->search = $opts['search'];
		}
		$this->tabs['account']['title'] = (isset($_COOKIE['SSO-Authed']) ? 'Account' : 'Login / Register');
	}
	
	function setActiveTab($id){
		if(isset($this->tabs[$id])){
			$this->active_tab = $id;
		}
	}
	
	function draw(){
		ob_start();
?><nav id="masthead"><div id="logo"><a href="/wiki/" id="logo-image"></a><h1><a href="/wiki/"><em>Practical</em> Plants</a></h1></div>

	<ul class="tabs">
	<?php foreach($this->tabs as $id=>$tab){ ?>
	<li class="<?php if($this->active_tab==$id){?>active<?php } ?>"><a href="<?php echo $tab['url'] ?>"><?php echo $tab['title'] ?></a></li><?php } ?>
	</ul>
	<?php if(isset($this->search)){ ?><div id="masthead-search">
		<?php echo $this->search; ?>
	</div> <?php } ?>
</nav><?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	function headTags(){
		return '<link rel="stylesheet" href="/resources/css/masthead.css" />';
	}
	
	function output(){
		echo $this->draw();
	}
}
?>