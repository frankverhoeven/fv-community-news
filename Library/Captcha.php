<?php


class fvCommunityNewsCaptcha {
	
	private $_text = '';
	
	protected $_textLib = 'ABCDEFGHJKLMNPQRSTWXYZ23456789';
	
	protected $_textLength = 6;
	
	protected $_backgroundColor = 'ffffff';
	
	protected $_lineColor = 'ffffff';
	
	protected $_textColor = '0066c';
	
	protected $_textShadowColor = '686868';
	
	protected $_fontSize = 17;
	
	protected $_fonts = array(
		'comic.ttf',
		'times.ttf',
		'verdana.ttf'
	);
	
	protected $_fontsDir = '/Data/Fonts/';
	
	public function __construct() {
		$this->_fontsDir = FVCN_ROOTDIR . $this->_fontsDir;
	}
	
	
	
	public function createText() {
		$from = str_shuffle($this->_textLib);
		$length = $this->_textLength;
		
		$str = '';
		for ($i=0; $i<$length; $i++) {
			$str .= substr($from, mt_rand(0, strlen($from)-1), 1);
		}
		
		$str = str_shuffle($str);
		
		$this->_text = $str;
		fvCommunityNewsSession::set('fvcn_CaptchaValue', sha1($str));
		
		return $this;
	}
	
	
	public function render() {
		$width = round(($this->_fontSize+($this->_fontSize*.8)) * $this->_textLength);
		$height = round($this->_fontSize + (($this->_fontSize/2.8)*2));
		$image = imagecreatetruecolor($width, $height);
		
		shuffle($this->_fonts);
		
		list($r, $g, $b) = $this->_hexToRgb($this->_backgroundColor);
		$background_color = imagecolorallocate($image, $r, $g, $b);
		list($r, $g, $b) = $this->_hexToRgb($this->_lineColor);
		$line_color = imagecolorallocate($image, $r, $g, $b);
		list($r, $g, $b) = $this->_hexToRgb($this->_textShadowColor);
		$shadow_color = imagecolorallocate($image, $r, $g, $b);
		list($r, $g, $b) = $this->_hexToRgb($this->_textColor);
		$text_color = imagecolorallocate($image, $r, $g, $b);
		
		imagefill($image, 0, 0, $background_color);
		
		$x_left = 6;
		
		for ($i = 0; $i < $this->_textLength; $i++) {
			$angle = mt_rand(-15, 15);
			$x = mt_rand($x_left - 3, $x_left + 3);
			$y = round( mt_rand(3/6*$height, 5/6*$height) );
			$font = $this->_fontsDir . $this->_fonts[ mt_rand(0, count($this->_fonts)-1) ];
			$char = $this->_text{$i};	
			
			imagettftext($image, $this->_fontSize, $angle, $x + mt_rand(1, 3), $y + mt_rand(-3, 3), $shadow_color, $font, $char);
			imagettftext($image, $this->_fontSize, $angle, $x, $y, $text_color, $font, $char);
			
			$x_left += ($this->_fontSize+12);
		}
		
		$x_end = $width;
		$number = mt_rand($this->_fontSize*.1, $this->_fontSize*.5);
		$spread = 0;
		
		for ($i = 0; $i<$number; $i++) {
			$y_start = mt_rand(-$spread, $height + 10);
			$y_end = mt_rand(-$spread, $height + 10);
			
			imageline($image, 0, $y_start, $x_end, $y_end, $line_color);
		}
			
		header('Content-type: image/jpeg');
		imagejpeg($image);
		exit;
	}
	
	/**
	 *		Converts hexadecimal colors to their rgb value.
	 *		@param string $hex The hexadecimal color.
	 *		@return array The rgb values.
	 *		@version 1.1
	 */
	protected function _hexToRgb($hex) {
		if (!ctype_xdigit($hex) || (6 != strlen($hex) && 3 != strlen($hex)))
			return false;
		
		if (3 == strlen($hex)) {
			$h = str_split($hex, 1);
			$hex = $h[0] . $h[0] . $h[1] . $h[1] . $h[2] . $h[2];
		}
		
		foreach (str_split($hex, 2) as $h)
			$dec[] = hexdec($h);
		return $dec;
	}
	
}

