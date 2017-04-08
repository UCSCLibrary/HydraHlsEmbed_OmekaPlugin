<h3>Hydra HLS Playback</h3>
<p> This plugin is not configured to display anything by default. The plugin uses its own hook called 'hydra_hls_embed', so you need to add a fire_plugin_hook for 'hydra_hls_embed' in your theme to make it display on a page.</p>
<label style="font-weight:bold;" for="avalon_width_public">Default iframe width, in pixels:</label>
<p><?php echo get_view()->formText('hydra_hls_width_public', 
                              get_option('avalon_width_public'), 
                              array('size' => 5));?></p>
<label style="font-weight:bold;" for="hydra_hls_height_public">Default iframe height, in pixels:</label>
<p><?php echo get_view()->formText('hydra_hls_height_public', 
                              get_option('hydra_hls_height_public'), 
                              array('size' => 5));?></p>

<label style="font-weight:bold;" for="hydra_base_url">Base URL of your Hydra installation:</label>
<p><?php echo get_view()->formText('hydra_base_url', 
                              get_option('hydra_base_url'), 
                              array('size' => 60));?></p>
