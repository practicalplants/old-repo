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
		)/*,
		'account'=>array(
			'title'=> 'Login',
			'url'=>'/sso'
		)*/
	);

  private static $instance;

  //optional singleton
  public function getInstance($opts = array() ){
    if(!self::$instance)
        self::$instance = new self($opts);
    return self::$instance;
  }
	
	public $active_tab = '';
	
	public function __construct($opts = array()){
		if(isset($opts['active_tab'])){
			$this->setActiveTab($opts['active_tab']);
		}
		if(isset($opts['search'])){
			$this->search = $opts['search'];
		}
		$this->logged_in = (isset($_COOKIE['SSO-Session']) && isset($_COOKIE['SSO-Authed'])) ? true : false;
	}
	
	function setActiveTab($id){
		if(isset($this->tabs[$id])){
			$this->active_tab = $id;
		}
	}
	
	function draw(){
		ob_start();
?>
  <nav id="masthead">
    <div class="width-constraint">
      <div id="logo"><a href="/wiki/" id="logo-image"></a><h1><a href="/wiki/"><em>Practical</em> Plants</a></h1></div>

	  	<ul class="tabs">
	  	<?php foreach($this->tabs as $id=>$tab){ ?>
	  	  <li class="<?php if($this->active_tab==$id){?>active<?php } ?>"><a href="<?php echo $tab['url'] ?>"><?php echo $tab['title'] ?></a></li><?php } ?>
	  	</ul>
	  	
	  	<div id="masthead-account">
	  	<?php if($this->logged_in):?>
        <a href="/sso" id="user-loggedin"><i class="icon-user icon-white"></i> Account</a>
	  	  <a href="/sso/logout" class="btn btn-small"><i class="icon-off"></i> Log Out</a>
	  	<?php else: ?>
	  	  <!--<form method="post" enctype="application/x-www-form-urlencoded" action="/sso/authenticate/">
          <input type="text" name="email" id="email" value="" placeholder="email">
          <input type="password" name="password" id="password" value="" placeholder="password">
          <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI'] ?>">
	  	    <button type="submit" name="login" id="login-button" class="btn"><i class="icon-user"></i> Log In <b class="caret"></b></button>
	  	  </form>-->
        <form method="post" action="/sso/authenticate/" id="account-menu-login">
          <input type="hidden" name="openid_identifier" id="openid_identifier" value="">
          <div class="btn-group dropdown" id="login-button-group">
            <button class="btn" id="login-with-button">Login with</button>
            <button class="btn dropdown-toggle" data-toggle="dropdown" id="login-with-dropdown">
              <i class="icon-user"></i>
              <span class="caret"></span>
            </button>
            <ul class="dropdown-menu pull-right">
              
              <li><a href="#" data-auth_type="external" class="login-facebook" data-auth_name="facebook" data-auth_url="https://www.facebook.com"><i class="icon-facebook"></i> Facebook</a></li>
              <li><a href="#" data-auth_type="external" class="login-twitter" data-auth_name="twitter" data-auth_url="https://www.twitter.com"><i class="icon-twitter"></i> Twitter</a></li>
              <li><a href="#" data-auth_type="external" class="login-google" data-auth_name="google" data-auth_url="https://www.google.com/accounts/o8/id"><i class="icon-google"></i> Google</a></li>
              <li><a href="#" data-auth_type="external" class="login-openid" data-auth_name="openid" data-auth_url=""><i class="icon-openid"></i> OpenID</a></li>
              <li><a href="#" data-auth_type="local" data-auth_name="local" class="login-local"><i class="icon-practicalplants"></i> Local Account</a></li>
              <li class="divider"></li>
              <li><a href="/sso/register"><i class="icon-user"></i> Register with Email Address</a></li>
            </ul>
          </div>
        </form>
	  	<?php endif; ?>
	  	</div>
      	
    </div><!--/width-constraint-->
  </nav><?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
	function headTags($skip=array()){
    $resources = array(
        array('bootstrap','stylesheet','/resources/bootstrap/css/bootstrap.min.css'),
        array('global','stylesheet','/resources/css/global.css'),
        array('masthead','stylesheet','/resources/css/masthead.css'),
        array('jquery','script','/resources/js/libs/jquery-1.8.2.min.js'),
        array('bootstrap','script','/resources/bootstrap/js/bootstrap.min.js'),
        array('login-menu','script','/resources/js/login-menu.js')
      );
    foreach($resources as $r){
      if(in_array($r[0], $skip))
        continue;
      if($r[1] === 'stylesheet')
        echo '<link rel="stylesheet" type="text/css" href="'.$r[2].'">'."\n";
      if($r[1] === 'script')
        echo '<script type="text/javascript" src="'.$r[2].'"></script>'."\n";
    }
	}

  function footer($extra_blocks=array()){ ?>
<div id="footer">
  <div class="width-constraint">
    <div class="footer-column" id="footer-about">
      <h3>About Practical Plants</h3>
      <p>Practical Plants is a plant database designed for <a href="/wiki/Permaculture">Permaculture</a> enthusiasts, <a href="/wiki/Agroforestry">Forest Gardeners</a>, Homesteaders, Farmers and anyone interested in <a href="/wiki/Organic">organic horticulture</a>.</p>
    </div>
    <div class="footer-column" id="footer-thanks">
      <h3>Our thanks to...</h3>
      <div class="thanks-to" id="thanks-tinymighty">
        <a href="http://tinymighty.com" class="thanks-logo"></a>
        <h4><a href="http://tinymighty.com">TinyMighty</a></h4>
        <p>Website designed, developed and generously hosted by TinyMighty.</p>
      </div>
      <div class="thanks-to" id="thanks-pfaf">
        <a href="http://pfaf.org" class="thanks-logo"></a>
        <h4><a href="http://pfaf.org">Plants For A Future</a></h4>
        <p>The fantastic database this project was forked from was created by PFAF (<a href="/wiki/PracticalPlants:PFAF">read more</a>).</p>
      </div>
      <div class="thanks-to" id="thanks-mediawiki">
        <a href="http://mediawiki.org" class="thanks-logo"></a>
        <h4><a href="http://mediawiki.org">MediaWiki</a> &amp; <a href="http://semantic-mediawiki.org">SMW</a></h4>
        <p>This website uses technology developed by the smart people behind MediaWiki and Semantic MediaWiki.</p>
      </div>
      <div class="thanks-to" id="thanks-cernunnos">
        <a href="http://cernunnos.es" class="thanks-logo"></a>
        <h4><a href="http://cernunnos.es">Cernunnos</a></h4>
        <p>This project is supported by the guys living at Cernunnos.</p>
      </div>
    </div>
    <div class="footer-column" id="footer-others">
      <div id="footer-docs">
        <h3>Documents</h3>
        <ul>
          <li><a href="/wiki/Privacy_policy">Privacy Policy</a></li>
        </ul>
      </div>
      <?php foreach($extra_blocks as $block): ?>
      <div id="<?php echo $block['id'] ?>">
        <h3><?php echo $block['title'] ?></h3>
        <?php echo $block['content'] ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div><!--/width-constraint-->
</div>
    
<div class="modal hide fade" id="login-modal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Enter your login details</h3>
  </div>
  <div class="modal-body">
 
    <form class="form-horizontal" method="post" action="/sso/authenticate">
      <div class="control-group">
        <label class="control-label" for="inputEmail">Email</label>
        <div class="controls">
          <input type="text" id="inputEmail" name="email" placeholder="Email">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="inputPassword">Password</label>
        <div class="controls">
          <input type="password" id="inputPassword" name="password" placeholder="Password">
        </div>
      </div>
      <div class="control-group">
        <div class="controls">
          <label class="checkbox">
            <input type="checkbox"> Remember me
          </label>
        </div>
      </div>
    </form>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-success" id="login-modal-login"><i class="icon-ok icon-white"></i> Log In</a>
  </div>
</div>
<div class="modal hide fade" id="openid-modal">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Enter your OpenID URL</h3>
  </div>
  <div class="modal-body">
 
    <form class="form-horizontal" method="post" action="/sso/authenticate/external">
      <div class="control-group">
        <label class="control-label" for="openid_identifier">OpenID URL</label>
        <div class="controls">
          <input type="text" id="openid_identifier" name="openid_identifier" placeholder="http://you.youropenidurl.com">
        </div>
      </div>
    </form>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-success" id="login-modal-login"><i class="icon-ok icon-white"></i> Connect with OpenID</a>
  </div>
</div>
    <?php
  }
	
	function output(){
		echo $this->draw();
	}
}
?>