<?php if ($this->rss) : ?>
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> <?php _e('Community News RSS Feed', 'fvcn'); ?>" href="<?php echo $this->rss; ?>" />
<?php endif; ?>

<?php if ($this->style) : ?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->dir; ?>/public/css/form/add-submission.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $this->dir; ?>/public/css/archive.css" />
<?php endif; ?>

<meta name="Community-News-Generator" content="FV Community News - <?php echo $this->version; ?>" />
