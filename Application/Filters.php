<?php

class fvCommunityNewsFilters {
	
	public function __construct() {
		// Name(name)
		add_filter('fvcn_Name', 'sanitize_text_field'		   );
		add_filter('fvcn_Name', 'wp_kses_data'		  		   );
		add_filter('fvcn_Name', '_wp_specialchars',			 30);
		
		// Email
		add_filter('fvcn_Email', 'sanitize_email'			   );
		add_filter('fvcn_Email', 'wp_kses_data'				   );
		
		// Title
		add_filter('fvcn_Title', 'sanitize_text_field'		   );
		add_filter('fvcn_Title', 'wp_kses_data'		  		   );
		add_filter('fvcn_Title', '_wp_specialchars',		 30);
		
		// Location
		add_filter('fvcn_Location', 'wp_strip_all_tags'		   );
		add_filter('fvcn_Location', 'esc_url'				   );
		add_filter('fvcn_Location', 'wp_kses_data'			   );
		
		// Description
		add_filter('fvcn_Description', 'wptexturize'           );
		add_filter('fvcn_Description', 'convert_chars'         );
		add_filter('fvcn_Description', 'stripslashes'          );
		add_filter('fvcn_Description', 'make_clickable',      9);
		add_filter('fvcn_Description', 'force_balance_tags', 25);
		add_filter('fvcn_Description', 'convert_smilies',    20);
		add_filter('fvcn_Description', 'wpautop',            30);
		
		// Date
		
		
		// Ip
		
		
		// Approved
		
		
		
		// Name(name)
		add_filter('fvcn_PreName', 'sanitize_text_field'		   );
		add_filter('fvcn_PreName', 'wp_filter_kses'		  		   );
		add_filter('fvcn_PreName', '_wp_specialchars',			 30);
		
		// Email
		add_filter('fvcn_PreEmail', 'trim'						   );
		add_filter('fvcn_PreEmail', 'sanitize_email'			   );
		add_filter('fvcn_PreEmail', 'wp_filter_kses'			   );
		
		// Title
		add_filter('fvcn_PreTitle', 'sanitize_text_field'		   );
		add_filter('fvcn_PreTitle', 'wp_filter_kses'	  		   );
		add_filter('fvcn_PreTitle', '_wp_specialchars',			 30);
		
		// Location
		add_filter('fvcn_PreLocation', 'wp_strip_all_tags'		   );
		add_filter('fvcn_PreLocation', 'esc_url_raw'			   );
		add_filter('fvcn_PreLocation', 'wp_filter_kses'			   );
		
		// Description
		add_filter('fvcn_PreDescription', 'balanceTags',		 50);
		
		// Date
		
		
		// Ip
		
		
		// Approved
		
	}
	
}

