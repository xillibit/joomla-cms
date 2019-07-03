<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.Atum
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       4.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var JDocumentHtml $this */

$app   = Factory::getApplication();
$lang  = $app->getLanguage();
$input = $app->input;
$wa    = $this->getWebAssetManager();

// Detecting Active Variables
$option     = $input->get('option', '');
$view       = $input->get('view', '');
$layout     = $input->get('layout', 'default');
$task       = $input->get('task', 'display');
$itemid     = $input->get('Itemid', '');
$cpanel     = $option === 'com_cpanel';
$hiddenMenu = $app->input->get('hidemainmenu');
$joomlaLogo = $this->baseurl . '/templates/' . $this->template . '/images/logo.svg';

// Template params
$siteLogo  = $this->params->get('siteLogo')
	? JUri::root() . $this->params->get('siteLogo')
	: $this->baseurl . '/templates/' . $this->template . '/images/logo-joomla-blue.svg';
$smallLogo = $this->params->get('smallLogo')
	? JUri::root() . $this->params->get('smallLogo')
	: $this->baseurl . '/templates/' . $this->template . '/images/logo-blue.svg';

$logoAlt = htmlspecialchars($this->params->get('altSiteLogo', ''), ENT_COMPAT, 'UTF-8');
$logoSmallAlt = htmlspecialchars($this->params->get('altSmallLogo', ''), ENT_COMPAT, 'UTF-8');

// Enable assets
$wa->enableAsset('template.atum.' . ($this->direction === 'rtl' ? 'rtl' : 'ltr'));

// Load specific language related CSS
HTMLHelper::_('stylesheet', 'administrator/language/' . $lang->getTag() . '/' . $lang->getTag() . '.css', ['version' => 'auto']);

// Load customer stylesheet if available
HTMLHelper::_('stylesheet', 'custom.css', array('version' => 'auto', 'relative' => true));

// Load specific template related JS
// TODO: Adapt refactored build tools pt.2 @see https://issues.joomla.org/tracker/joomla-cms/23786
HTMLHelper::_('script', 'media/templates/' . $this->template . '/js/template.min.js', ['version' => 'auto']);
HTMLHelper::_('script', 'media/vendor/focus-visible/js/focus-visible.min.js', ['version' => 'auto']);

// Set some meta data
$this->setMetaData('viewport', 'width=device-width, initial-scale=1');
// @TODO sync with _variables.scss
$this->setMetaData('theme-color', '#1c3d5c');
$this->addScriptDeclaration('cssVars();');

// Opacity must be set before displaying the DOM, so don't move to a CSS file
$css = '
	.container-main > * {
		opacity: 0;
	}
	.sidebar-wrapper > * {
		opacity: 0;
	}
';


$this->addStyleDeclaration($css);

HTMLHelper::getServiceRegistry()->register('atum', 'Joomla\\Template\\Atum\\Administrator\\Service\\HTML\\Atum');

HTMLHelper::_('atum.rootcolors', $this->params);
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<jdoc:include type="metas"/>
	<jdoc:include type="styles"/>
	<jdoc:include type="scripts"/>
</head>

<body class="admin <?php echo $option . ' view-' . $view . ' layout-' . $layout . ($task ? ' task-' . $task : ''); ?>">

<noscript>
	<div class="alert alert-danger" role="alert">
		<?php echo Text::_('JGLOBAL_WARNJAVASCRIPT'); ?>
	</div>
</noscript>

<?php // Header ?>
<header id="header" class="header">
	<div class="d-flex">
		<div class="header-title d-flex">
			<div class="d-flex">
				<?php // No home link in edit mode (so users can not jump out) and control panel (for a11y reasons) ?>
				<?php if ($hiddenMenu || $cpanel) : ?>
					<div class="logo">
					<img src="<?php echo $siteLogo; ?>" alt="<?php echo $logoAlt; ?>">
					<img class="logo-small" src="<?php echo $smallLogo; ?>" alt="<?php echo $logoSmallAlt; ?>">
					</div>
				<?php else : ?>
					<a class="logo" href="<?php echo Route::_('index.php'); ?>"
						aria-label="<?php echo Text::_('TPL_BACK_TO_CONTROL_PANEL'); ?>">
						<img src="<?php echo $siteLogo; ?>" alt="">
						<img class="logo-small" src="<?php echo $smallLogo; ?>" alt="">
					</a>
				<?php endif; ?>
			</div>
			<jdoc:include type="modules" name="title"/>
		</div>
		<div class="header-items d-flex">
			<jdoc:include type="modules" name="status" style="header-item"/>
		</div>
	</div>
</header>

<?php // Wrapper ?>
<div id="wrapper" class="d-flex wrapper<?php echo $hiddenMenu ? '0' : ''; ?>">



    <?php // Sidebar ?>
	<?php if (!$hiddenMenu) : ?>

        <button class="navbar-toggler toggler-burger collapsed" type="button" data-toggle="collapse" data-target="#sidebar-wrapper" aria-controls="sidebar-wrapper" aria-expanded="false" aria-label="Toggle navigation">
                 <span class="navbar-toggler-icon">
                  </span>
        </button>

		<div id="sidebar-wrapper" class="sidebar-wrapper sidebar-menu" <?php echo $hiddenMenu ? 'data-hidden="' . $hiddenMenu . '"' : ''; ?>>
            <div id="sidebarmenu">
                <div class="sidebar-toggle">
                  <a id="menu-collapse" href="#">
                       <span id="menu-collapse-icon" class="fa-fw fa fa-toggle-off" aria-hidden="true"></span>
                       <span class="sidebar-item-title"><?php echo Text::_('TPL_ATUM_TOGGLE_SIDEBAR'); ?></span>
                  </a>
                </div>
                <jdoc:include type="modules" name="menu" style="none"/>
            </div>
		</div>
	<?php endif; ?>

	<?php // container-fluid ?>
	<div class="container-fluid container-main">
		<?php if (!$cpanel) : ?>
			<?php // Subheader ?>
			<a class="btn btn-subhead d-md-none d-lg-none d-xl-none" data-toggle="collapse"
				data-target=".subhead"><?php echo Text::_('TPL_ATUM_TOOLBAR'); ?>
				<span class="icon-wrench"></span></a>
			<div id="subhead" class="subhead">
				<div id="container-collapse" class="container-collapse"></div>
				<div class="row">
					<div class="col-md-12">
						<jdoc:include type="modules" name="toolbar" style="no"/>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<section id="content" class="content">
			<?php // Begin Content ?>
			<jdoc:include type="modules" name="top" style="xhtml"/>
			<div class="row">
				<div class="col-md-12">
					<main>
						<jdoc:include type="component"/>
					</main>
				</div>
				<?php if ($this->countModules('bottom')) : ?>
					<jdoc:include type="modules" name="bottom" style="xhtml"/>
				<?php endif; ?>
			</div>
			<?php // End Content ?>
		</section>

		<div class="notify-alerts">
			<jdoc:include type="message"/>
		</div>
	</div>
</div>
<jdoc:include type="modules" name="debug" style="none"/>
</body>
</html>
