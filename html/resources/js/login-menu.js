(function($){

  var local_url = '/sso/authenticate/'
    , external_url = '/sso/authenticate/external/'
    , $login
    , $loginModal
    , $openidModal
    , default_method = 'facebook'
    , current_method;

  var cookie_name = 'sso_method', cookie_path='/';

  function log(){
    if(console){
      console.log.apply(console,arguments);
    }
  }

  function init(){
    $login = $('form#account-menu-login');
    //set the method to the users last chosen method
    current_method = readCookie() || default_method;
    //console.log("Current method is "+readCookie());
    $('#login-with-dropdown i').attr( 'class', $login.find('[data-auth_name="'+current_method+'"] i').attr('class') );

    
    $openidModal = $('#openid-modal').modal({'show':false});
    $openidModal.find('.btn-success')
                .click(function(){
                  $openidModal.find('form').submit();
                });

    $loginModal = $('#login-modal').modal({'show':false});
    $loginModal .find('.btn-success')
                .click( function(){
                  $loginModal.find('form').submit() 
                });


    if($login){
      $login.find('a[data-auth_type=external]').click(function(ev){
        ev.preventDefault();

        var $this = $(this);
        $('#login-with-dropdown i').attr( 'class', $this.find('i').attr('class') );
        
        log('Clicked ',$this.attr('data-auth_name') );
        externalLogin( $this.attr('data-auth_name') );
        
        return false;
        
      });

      $login.find('a[data-auth_type=local]').click(function(ev){
        ev.preventDefault();

        var $this = $(this);
        //open login modal
        $('#login-with-dropdown i').attr( 'class', $this.find('i').attr('class') );
        localLogin();
        
        return false;
        
      });

      //when the login button is clicked, simulate a click on the current method link
      $login.find('#login-with-button').click(function(ev){
        ev.preventDefault();
        var $this = $(this);

        $login.find('[data-auth_name="'+current_method+'"]').click();
        return false;
      });
    }

    function externalLogin(provider){
      setCookie(provider);
      auth_url = $login.find('[data-auth_name="'+provider+'"]').attr('data-auth_url');
      if(auth_url){
          $login.find('#loginmenu_openid_identifier').val(auth_url);
          $login.attr('action',external_url).submit();
        }else{
          $openidModal.modal('show');
        }
      return false;
      
    }

    function localLogin(){
      setCookie('local');
      $loginModal.modal('show');
      //$loginModal.find('input').first().focus();
    }

    
    function setCookie(value) {
      var date = new Date();
      date.setTime(date.getTime() + (this.cookie_expires * 24 * 60 * 60 * 1000));
      var expires = "; expires=" + date.toGMTString();
      document.cookie = cookie_name + "=" + value + expires + "; path=" + cookie_path;
    }

    function readCookie() {
      var nameEQ = cookie_name + "=";
      var ca = document.cookie.split(';');
      for ( var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ')
          c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0)
          return c.substring(nameEQ.length, c.length);
      }
      return null;
    }
    /*function Modal(id, title, content, buttons){

      this.$modal = $('<div class="modal hide fade" id="'+id+'"></div>');
      this.$header = $('<div class="modal-header"></div>')
                      .append('<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>')
                      .appendTo(this.$modal);
      this.$title = $('<h3></h3>').text(title).appendTo(this.$header);
      this.$body = $('<div class="modal-body"></div>')
                      .append(content)
                      .appendTo(this.$modal);
      this.$footer = $('<div class="modal-footer"></div>')
                      .append(buttons)
                      .appendTo(this.$modal);
      this.modal = this.$modal.modal().data('modal');
    }
    Modal.prototype.show = function() {
      this.modal.show();
    };
    Modal.prototype.hide = function() {
      this.modal.hide();
    };
    Modal.prototype.get = function() {
      return this.$modal;
    };
    Modal.prototype.title = function(title) {
      if(undefined!==title){
        $title.text(title);
      }else{
       return $title.text();
      }
    };
    Modal.prototype.content = function(content) {
      if(undefined!==content){
        $body.empty();
        $body.append(content);
      }else{
        $body.children();
      }
    };*/

  }

  $(init);
})(jQuery);