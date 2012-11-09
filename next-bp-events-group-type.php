<?php
// parte de configuracao e grupo do bp //


class Eventos_Extension extends BP_Group_Extension {  

    var $visibility = 'public'; // 'public' will show your extension to non-group members, 'private' means you have to be a member of the group to view your extension.
 
    var $enable_create_step = true; // If your extension does not need a creation step, set this to false
    var $enable_nav_item = true; // If your extension does not need a navigation item, set this to false
    var $enable_edit_item = true; // If your extension does not need an edit screen, set this to false
  
    /**
     * Constructor
     */
    function conferencia_extension() {
        $this->name = 'Eventos';
        $this->slug = 'eventos';
     
        $this->create_step_position = 21;
        $this->nav_item_position = 31;
      
        /* Place this in the constructor */
        $this->enable_nav_item = $this->enable_nav_item();
       
    }
     
    function create_screen() {
        if ( !bp_is_group_creation_step( $this->slug ) )
            return false;
        ?>
     
        <p>The HTML for my creation step goes here.</p>
     
        <?php
        wp_nonce_field( 'groups_create_save_' . $this->slug );      
    }
     
    function create_screen_save() {
        global $bp;
 
        check_admin_referer( 'groups_create_save_' . $this->slug );
 
        /* Save any details submitted here */
        groups_update_groupmeta( $bp->groups->new_group_id, 'my_meta_name', 'value' );       
    }
     
    function edit_screen() {
        if ( !bp_is_group_admin_screen( $this->slug ) )
            return false; ?>
             
        <h2><?php echo esc_attr( $this->name ) ?></h2>
     
        <p>Edit steps here</p>
        <input type="submit" name="save" value="Save" />
     
        <?php
        wp_nonce_field( 'groups_edit_save_' . $this->slug );    
    }
     
    function edit_screen_save() {
        global $bp;
     
        if ( !isset( $_POST['save'] ) )
            return false;
         
        check_admin_referer( 'groups_edit_save_' . $this->slug );
         
        /* Insert your edit screen save code here */
 
        /* To post an error/success message to the screen, use the following */
        if ( !$success )
            bp_core_add_message( __( 'There was an error saving, please try again', 'buddypress' ), 'error' );
        else
            bp_core_add_message( __( 'Settings saved successfully', 'buddypress' ) );
 
        bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . '/admin/' . $this->slug );
    }
     
    function display() {
        /* Use this function to display the actual content of your group extension when the nav item is selected */
    }
     
    function widget_display() { ?>
        <div class=&quot;info-group&quot;>
            <h4><?php echo esc_attr( $this->name ) ?></h4>
            <p>
                You could display a small snippet of information from your group extension here. It will show on the group
                home screen.
            </p>
        </div>
        <?php
    }
    

    /* Add this method the end of your extension class */
    function enable_nav_item() {
        global $bp;
     
     
        return true;
        /* You might want to check some groupmeta for this group, before determining whether to enable the nav item */
        if ( groups_get_groupmeta( $bp->groups->current_group->id, 'settings_complete' ) )
            return true;
        else
            return false;
    }
    
}
//bp_register_group_extension( 'Eventos_Extension' );

