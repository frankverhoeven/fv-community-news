<?php header('Content-Type: text/xml; charset=' . get_option('blog_charset'), true); ?>

<fvcnAjaxResponse>
	
	<?php if ($this->valid) : ?>
		
		<status>approved</status>
		<message><?php echo $this->message; ?></message>
		
	<?php else : ?>
		
		<status>error</status>
		<message><?php echo $this->message; ?></message>
		<errorfields>
			
			<?php foreach ($this->errors as $name=>$error) : ?>
				
				<field>
					<name><?php echo $name; ?></name>
					<error><?php echo $error; ?></error>
				</field>
				
			<?php endforeach; ?>
			
		</errorfields>
		
	<?php endif; ?>
	
</fvcnAjaxResponse>
