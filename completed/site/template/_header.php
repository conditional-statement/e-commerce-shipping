<?php
$objCatalogue = new Catalogue();
$cats = $objCatalogue->getCategories();

$objBusiness = new Business();
$business = $objBusiness->getBusiness();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $this->_meta_title; ?></title>
<meta name="description" content="<?php echo $this->_meta_description; ?>" />
<meta name="keywords" content="<?php echo $this->_meta_keywords; ?>" />
<meta http-equiv="imagetoolbar" content="no" />
<link href="/css/core.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="header">
	<div id="header_in">
		<h5><a href="/"><?php echo $business['name']; ?></a></h5>
		<?php
			if (Login::isLogged(Login::$_login_front)) {
				echo '<div id="logged_as">Logged in as: <strong>';
				echo Login::getFullNameFront(Session::getSession(Login::$_login_front));
				echo '</strong> | <a href="';
				echo $this->objUrl->href('orders');
				echo '">My orders</a>';
				echo ' | <a href="';
				echo $this->objUrl->href('logout');
				echo '">Logout</a></div>';				
			} else {
				echo '<div id="logged_as"><a href="';
				echo $this->objUrl->href('login');
				echo '">Login</a></div>';
			}
		?>
	</div>
</div>
<div id="outer">
	<div id="wrapper">
		<div id="left">
			<?php 
				if ($this->objUrl->cpage != 'summary') {
					require_once('basket_left.php');
				}
			?>
			<?php if (!empty($cats)) { ?>
			<h2>Categories</h2>
			<ul id="navigation">
				<?php 
					foreach($cats as $cat) {
						echo '<li><a href="';
						echo $this->objUrl->href('catalogue', array('category', $cat['identity']));
						echo '"';
						echo $this->objNavigation->active('catalogue', array('category' => $cat['identity']));
						echo '>';
						echo Helper::encodeHtml($cat['name']);
						echo '</a></li>';
					}
				?>
				</ul>
			<?php } ?>					
		</div>
		<div id="right">
		
		
		
		