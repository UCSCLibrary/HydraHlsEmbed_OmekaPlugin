<?php
class HydraHlsEmbedPlugin extends Omeka_Plugin_AbstractPlugin
{
    const DEFAULT_VIEWER_WIDTH = 640;
    const DEFAULT_VIEWER_HEIGHT = 480;
    const DEFAULT_BASE_URL = "http://digitalcollections.library.ucsc.edu";
    
    protected $_hooks = array('install',
                              'uninstall',
                              'config_form',
                              'config',
                              'hydra_hls_embed',
    );


    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        'hydra_base_url' => HydraHlsEmbedPlugin::DEFAULT_BASE_URL,
        'hydra_hls_height_public' => HydraHlsEmbedPlugin::DEFAULT_VIEWER_HEIGHT,
        'hydra_hls_width_public' => HydraHlsEmbedPlugin::DEFAULT_VIEWER_WIDTH,
    );

    
    public function hookInstall()
    {
        $this->_installOptions();
        
        $db = get_db();

	// Don't install if an element set named "Hydra HLS" already exists.
  if ($db->getTable('ElementSet')->findByName('Hydra HLS')) {
          throw new Exception('An element set by the name "Hydra HLS" already exists. You must delete that '
                         . 'element set to install this plugin.');
}

		$elementSetMetadata = array(
			'record_type'        => "Item", 
			'name'        => "Hydra HLS", 
			'description' => "Elements needed for streaming video for the HydraHlsEmbed Plugin"
		);
		$elements = array(
			array(
				'name'           => "Hydra ID",
				'description'    => "The ID of the item in the Hydra application serving the video."
			), 
			array(
				'name'           => "Player Width",
				'description'    => "The iframe width of this item. Overrides the default configuration setting."
			),
			array(
				'name'           => "Player Height",
				'description'    => "The iframe height of this item. Overrides the default configuration setting."
			)
			// etc.
		);
	insert_element_set($elementSetMetadata, $elements);
    }
    
    public  function hookUninstall()
    {
        $this->_uninstallOptions();
        if ($elementSet = get_db()->getTable('ElementSet')->findByName("Hydra HLS")) {
            $elementSet->delete();
        }
    }
	
    /**
* Appends a warning message to the uninstall confirmation page.
*/
    public static function admin_append_to_plugin_uninstall_message()
    {
        echo '<p><strong>Warning</strong>: This will permanently delete the Hydra HLS element set and all its associated metadata. You may deactivate this plugin if you do not want to lose data.</p>';
    }
	
    public function hookConfigForm()
    {
        include 'config_form.php';
    }
    
    public function hookConfig()
    {
        if (!is_numeric($_POST['hydra_hls_width_public']) ||
        !is_numeric($_POST['hydra_hls_height_public'])) {
            throw new Omeka_Validator_Exception('The width and height must be numeric.');
        }
        set_option('hydra_hls_width_public', $_POST['hydra_hls_width_public']);
        set_option('hydra_hls_height_public', $_POST['hydra_hls_height_public']);
        set_option('hydra_base_url', $_POST['hydra_base_url']);
    }
    
    public function hookHydraHlsEmbed($args)
    {
        $this->append($args);
?>
	  <script>
	     jQuery(document).ready(function() {jQuery('#exhibit-content').find('h1').after(jQuery('#hydra_hls_player'));});
	//	     jQuery(document).ready(function() {jQuery('.item.show #fields-primary').prepend(jQuery('#hydra_hls_player'));});
	</script>
<?php
    }
  
public function append($args)
{ 
    if(isset($args['hydra_id']))
        $hydraId = $args['hydra_id'];
    else {
        if(get_current_record('Item', false))
	    $hydraId = metadata('item',array('Hydra HLS','Hydra ID'));
        if(isset($args['hydra_id']))
	    $hydraId = $args['hydra_id'];
    }
    if(!$hydraId)
	return;
    $hydraUrl = get_option('hydra_base_url')."/file_set/".$hydraId."/embed";
    $width =  get_option('hydra_hls_width_public');
    $height =  get_option('hydra_hls_height_public');
    if(get_current_record('Item', false)){
	$itemheight = metadata('item',array('Hydra HLS','Player Height'));
        $height = $itemheight ? $itemheight : $height;
	$itemwidth = metadata('item',array('Hydra HLS','Player Width'));
        $width = $itemwidth ? $itemwidth : $width;
    }
    if(isset($args['height'])) 	
	$height = $args['height'];
    if(isset($args['width'])) 	
	$width = $args['width'];
    
?> <div id="hydra_hls_player" style= "margin:0 auto;">
  <iframe style="margin:0 auto;" src="<?php echo $hydraUrl; ?>" width=<?php echo $width ?> height=<?php echo $height ?> frameborder=0 webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
</div>
<?php
} 
 } ?>
