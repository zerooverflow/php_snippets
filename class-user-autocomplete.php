<?php 

/**
 * Classe per Admin Wordpress
 */

class UserAutoComplete {
    
    public function __construct(){
        add_action( 'admin_enqueue_scripts', array(&$this, 'add_scripts'));
        add_action( 'wp_ajax_user_search', array(&$this, 'user_search') ); 
    }
    

    public function user_search(){
    	$term = strtolower( $_REQUEST['term'] );
    	$suggestions = array();
    	
    	$user_query = new WP_User_Query( array(
    			'search'         => '*'. $_REQUEST['term'] .'*',
    			'search_columns' => array( 'user_login', 'user_email', 'user_nicename' )
    	) );
    	
    	if ( ! empty( $user_query->get_results() ) ) {
    		foreach ( $user_query->get_results() as $user ) {
    			
    			$suggestion = array();
    			$suggestion['label'] = $user->display_name;
    			$suggestion['email'] = $user->user_email;
    			$suggestion['value'] = $user->ID;
    			$suggestions[] = $suggestion;
    		}
    	} 
    	
    	wp_reset_query();
    	
    	
    	$response = json_encode( $suggestions );
    	echo $response;
    	exit();
    	
    }
    
    public function add_scripts(){
    	wp_enqueue_script( 'jquery' );
    	wp_enqueue_script( 'jquery-ui-autocomplete' );
	}
	
	public function print_form_autocomplete(){
		?>
			<form id="user-filter" method="get">
				<label>Filtra per nome <input id="user-search" type="text" style="width:300px" value=""/></label>
				<input type="hidden" id="user-id" name="user_id" />
				<input type="submit" class="button-primary" value="Cerca"/>
			</form>

			<script>
			jQuery(document).ready(function($) {

				var url = ajaxurl + "?action=user_search";

				$( "#user-search" ).autocomplete({
					source: url,
					delay: 500,
					minLength: 3,
					focus: function( event, ui ) {
						$( "#user-search" ).val( ui.item.label );
						return false;
						},
					select: function( event, ui ) {
						console.log ('selezionato ', ui);
						$( "#user-search" ).val( ui.item.label );
						$('#user-id').val(ui.item.value);
						return false;
					}
				})
				.autocomplete( "instance" )._renderItem = function( ul, item ) {
						return $( "<li>" )
						.append( "<div>" + item.label + " - <small style='color:#999'>" + item.email + "</small></div>" )
						.appendTo( ul );
					};
			
			
			});
			</script>
		<?php
	}


    
}

