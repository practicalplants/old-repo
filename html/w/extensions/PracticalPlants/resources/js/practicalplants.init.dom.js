(function($){

/* Move elements on dom load */
$(function(){ 

  	
	resizeArticleImage();
	
	/* 
	The following checks for the presence of elements within .article-content 
	which the MoveToSkin extension should have already moved out of the .article-content 
	element and into their new positions in the skin.
	These provide a backup in the rare event things aren't moved properly (eg. there is currently
	a bug which prevents MoveToSkin working properly on Special:FormEdit pages)
	*/
	setTOC();
	setArticleSummary();
	setArticleImage();
	setIconBar();
	setArticleCommonName();
	setArticleState();	
	glueFooter();
	initScrollSpy();
	
	$(window).resize(windowResize);
});

var domOps = {
	summary:{
		selector: '#article-summary',
		call: setArticleSummary,
		done: false
	},
	image:{
		selector: '#article-image',
		call: setArticleSummary,
		done: false
	},
	state:{
		selector: '#article-state',
		call: setArticleSummary,
		done: false
	}
};
function checkDom(){
	//console.log("checking");
	var allDone=true;
	for(var k in domOps){
		if(!domOps.hasOwnProperty(k)){
			//console.log('Not even my property, yo');
			return;
		}
		if(domOps[k].done===false && $(domOps[k].selector).length > 0){
			//console.log('Element detected: '+domOps[k].selector+' performing dom op');
			domOps[k].done = true;
			domOps[k].call.call();
		}else{
			allDone=false;
		}
	}
	if(allDone===true){
		clearTimeout(loop);
	}else{
		loop = setTimeout(checkDom,50);
	}
}

function windowResize(){
  resizeArticleImage();
  glueFooter();
}

function setArticleSummary(){
	var summary = $('.article-content #article-summary');
	if(summary.length > 0){
		$('#page-header #article-title').after(summary);
	}
}

function setArticleImage(){

	var image = $('.article-content #article-image')
	    , image_el = $('header#page-header #article-image-container')
	    , header = $('#page-header');
	if(image.length){
		header.addClass('with-image')
		      .find('#article-title').before(image_el);
		
		image_el.append(image);
		
		resizeArticleImage();
	}
	
}

function resizeArticleImage(){
  /*var ic = $('#article-image-container');
  var height = $('#page-header').height();
  console.log('Setting image height to',height);
  
  //if( ic.find('img').width() / ic.find('img').height() > 1 )
  o_height = height+2;
  o_width = 'auto';
  width = 'auto';
  ic.css( {'height':o_height, 'width':o_width} );
  ic.find('#article-image,img').css( {'height':height, 'width':width} );*/
}

function setTOC(){
  var $toc = $('.article-content #toc');
  if($toc.length > 0){
    $('#sidebar #toc-container').prepend($toc);
  }
}  
function setIconBar(){
  var $ib = $('.article-content #plant-iconbar');
  if($ib.length > 0)
    $('#page-header').append($ib).addClass('with-iconbar');
}
function setArticleState(){
	//console.log("setting article state to top!");
	var state=$('.article-content #article-state');
	if(state.length>0){
		$('#page-header').before(state);
	}
}

function setArticleCommonName(){
	var common = $('.article-content #common-name');
	if(common.length > 0){
		$('#page-header #article-title').append(common);
	}
}	
function initScrollSpy(){
    //$('body').scrollspy();
}
function glueFooter(){
	var footer = $('#footer');
	if(footer.length > 0 && footer.offset().top+footer.outerHeight() < window.height){
		footer.css({
		'position':'absolute',
		'bottom':window.height,
		'width':window.width
		});
	}
}
	
})(jQuery||$);