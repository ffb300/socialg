<?php
/**
 * @version     1.0.0
 * @package     com_citybranding
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU AFFERO GENERAL PUBLIC LICENSE Version 3; see LICENSE
 * @author      Ioannis Tsampoulatidis <tsampoulatidis@gmail.com> - https://github.com/itsam
 */
// no direct access
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$params	= $app->getParams();

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_citybranding', JPATH_ADMINISTRATOR);
$user = JFactory::getUser();
$canEdit = $user->authorise('core.edit', 'com_citybranding.poi.' . $this->item->id);
$canChange = $user->authorise('core.edit.state', 'com_citybranding.poi.' . $this->item->id);
$canEditOwn = $user->authorise('core.edit.own', 'com_citybranding.poi.' . $this->item->id);

if (!$canEdit && $user->authorise('core.edit.own', 'com_citybranding.poi.' . $this->item->id)) {
	$canEdit = $user->id == $this->item->created_by;
}

//Edit Own only if poi status is the initial one
$firstStep = CitybrandingFrontendHelper::getStepByStepId($this->item->stepid);
$canEditOnStatus = true;
//if ($firstStep['ordering'] != 1){
//    $canEditOnStatus = false;
//}

$photos = json_decode($this->item->photo);
$i=0;
foreach ($photos->files as $photo) {
	if(!isset($photo->thumbnailUrl))
		unset($photos->files[$i]);
	$i++;
}
$attachments = json_decode($this->item->photo);
$i=0;
foreach ($attachments->files as $attachment) {
	if(isset($attachment->thumbnailUrl))
		unset($attachments->files[$i]);
	$i++;
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/3.3.2/masonry.pkgd.min.js"></script>
<script src="<?php echo  JURI::root(true) . '/components/com_citybranding/assets/js/imagesloaded.pkgd.min.js'; ?>"></script>

<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {
		js('#gallery').photobox('a', { thumbs:true, loop:false }, callback);
		// using setTimeout to make sure all images were in the DOM, before the history.load() function is looking them up to match the url hash
		setTimeout(window._photobox.history.load, 2000);
		function callback(){
			//console.log('callback for loaded content:', this);
		};

		var grid = js('.grid').masonry({
			// set itemSelector so .grid-sizer is not used in layout
			itemSelector: '.grid-item',
			// use element for option
			columnWidth: '.grid-sizer',
			/*gutter: '.gutter-sizer',*/
			percentPosition: true
		});
		//grid.masonry('layout');
		grid.imagesLoaded().progress( function() {
			grid.masonry('layout');
		});

		var grid2 = js('.grid2').masonry({
			// set itemSelector so .grid-sizer is not used in layout
			itemSelector: '.grid-item',
			// use element for option
			columnWidth: '.grid-sizer',
			gutter: '.gutter-sizer',
			percentPosition: true
		});
		//grid2.masonry('layout');
		grid2.imagesLoaded().progress( function() {
			grid2.masonry('layout');
		});
    });
</script>

<?php if (!$this->item || ($this->item->state != 1 && !$canEditOwn ) ) : ?>
	<div class="alert alert-danger">
		<?php echo JText::_('COM_CITYBRANDING_ITEM_NOT_LOADED'); ?>
	</div>
<?php return; endif; ?>

<?php if($canEdit /*&& $this->item->checked_out == 0*/ && $canEditOnStatus && $this->item->poitype == 1): ?>
	<a class="button special" href="<?php echo JRoute::_('index.php?option=com_citybranding&task=poi.edit&id='.$this->item->id); ?>"><i class="fa fa-pencil"></i> <?php echo JText::_("COM_CITYBRANDING_EDIT_ITEM"); ?> your SES</a>
<?php endif; ?>

<header class="citybranding-poi-title">
	<?php if($this->item->logo != '') : ?>
		<div class="image fit" style="width:120px;float:right;text-align:right;">
			<img src="<?php echo $this->item->logo; ?>" alt="logo" />
			<br />
			<span style="font-size: 0.4em">Logo</span>
		</div>
	<?php endif; ?>
	<h4><?php echo $this->item->title; ?></h4>
	<p><span class="icon cb-location"></span> <?php echo $this->item->address;?><br />
		<span class="cb-cat"><?php echo $this->item->catid_title; ?></span>
	<br />
		<h5>Our S.E.S. supports:</h5>
		<?php $classifications = $this->item->classifications;?>

		<?php $classifications = explode(',',$classifications);?>
		<?php foreach ($classifications as $catid) : ?>
			<span class="cb-tag"><?php echo CitybrandingFrontendHelper::getClassificationNameById($catid); ?></span>
		<?php endforeach; ?>
	</p>
</header>

<?php /*if(!empty($attachments->files)) : ?>
	<div id="attachments">
		<div class="citybranding-poi-subtitle"><?php echo JText::_('COM_CITYBRANDING_POI_ATTACHMENTS'); ?></div>
		<?php foreach ($attachments->files as $attachment) : ?>
			<ul>
			<li><a href="<?php echo $attachment->url; ?>"><?php echo $attachment->name; ?></a></li>
			</ul>
		<?php endforeach ?>
	</div>
<?php endif; */ ?>


<?php
$dom = JURI::root(true) . '/components/com_citybranding/assets/pannellum-2.1.1/src/pannellum.htm';
$pan = '?panorama=' . JURI::root(true) . '/images/panorama/examplepano.jpg';
$arg = '&amp;title='.htmlspecialchars($this->item->title);
$preview = '&amp;preview=' . JURI::root(true) . '/images/panorama/examplepano-preview.jpg';

$src = $dom.$pan.$arg.$preview;
?>

<div id="poi-wrapper">
	<div class="grid">

		<!-- width of .grid-sizer used for columnWidth -->
		<div class="grid-sizer"></div>
		<div class="gutter-sizer"></div>

		<?php if($this->item->id == 4 && false) : //testing ?>
		<div class="grid-item grid-item--width100">
			<iframe title="pannellum panorama viewer 1"
					width="100%"
					height="300px"
					webkitAllowFullScreen
					mozallowfullscreen
					allowFullScreen
					style="border-style:none;margin:0;"
					src="<?php echo $src;?>">
			</iframe>
		</div>
		<?php endif; ?>

		<?php if(!empty($photos->files) && file_exists($photos->imagedir .'/'. $photos->id . '/thumbnail/' . (@$photos->files[0]->name))) : ?>

		<div id='gallery'>
			<?php
			$index = 1;
			$count = count($photos->files);
			?>

			<?php foreach ($photos->files as $photo) : ?>

			<div class="grid-item<?php echo $index == 1 ? ' grid-item--width2': ''; ?><?php echo $count == 1 ? ' grid-item--width100': ''; ?>">
				<a href="<?php echo $photos->imagedir .'/'. $photos->id . '/' . ($photo->name) ;?>" class="image fit">
					<?php if($index == 1) : ?>
						<img src="<?php echo $photos->imagedir .'/'. $photos->id . ($index == 1 ? '/' : '/medium/') . ($photo->name) ;?>" alt="<?php echo JText::_('COM_CITYBRANDING_POIS_PHOTO') . ' '. $index;?>" />
					<?php else :?>
						<img src="<?php echo $photos->imagedir .'/'. $photos->id . '/medium/' . ($photo->name) ;?>" alt="<?php echo JText::_('COM_CITYBRANDING_POIS_PHOTO') . ' '. $index;?>" />
					<?php endif; ?>
				</a>
				<?php $index++;?>
			</div>

			<?php endforeach; ?>
		</div>
		<?php endif; ?>

	</div>
</div>

<p></p>
<p><?php echo $this->item->description; ?></p>


<h4>Related S.E.S.</h4>
<?php
	$relativeBrands = $this->relativeBrands;
?>

<?php if(!empty($relativeBrands)) : ?>

	<div class="grid2">
		<!-- width of .grid-sizer used for columnWidth -->
		<div class="grid-sizer"></div>
		<div class="gutter-sizer"></div>

		<?php foreach ($relativeBrands as $rBrand) : ?>
		<?php $attachments = json_decode($rBrand['photo']); ?>
		<div class="grid-item">
			<div id="citybranding-panel-brand-<?php echo $rBrand['id'];?>" class="citybranding-panel">


				<?php //get photo if any
				$img = null;
				$i = 0;
				if(isset($attachments->files)){
					foreach ($attachments->files as $file) {
						if (isset($file->thumbnailUrl)){
							$img['src']  = $attachments->imagedir .'/'. $attachments->id . '/medium/' . ($attachments->files[$i]->name);
							$img['link'] = JRoute::_('index.php?option=com_citybranding&view=poi&id='.(int) $rBrand['id']);
							break; //on first photo break
						}
						$i++;
					}
				}
				?>
				<?php if (!is_null($img)) : ?>
					<div class="crop-height">
						<a href="<?php echo $img['link'];?>">
							<img class="scale" src="<?php echo $img['src'];?>" alt="POI photo" />
						</a>
					</div>
				<?php endif; ?>

				<div class="cb-category-icon">
					<?php $rBrand['catid'] = explode(',',$rBrand['catid']);?>
					<?php foreach ($rBrand['catid'] as $catid) : ?>
						<span class="cb-cat"><?php echo CitybrandingFrontendHelper::getCategoryNameByCategoryId($catid); ?></span>
					<?php endforeach; ?>
				</div>

				<div class="<?php echo ($rBrand['moderation'] == 1 ? 'poi-unmoderated ' : ''); ?>citybranding-panel-body">
					<span class="lead">

						<?php if ($canEdit && $canEditOnStatus) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_citybranding&task=poi.edit&id='.(int) $rBrand['id']); ?>">
								<i class="icon-edit"></i> <?php echo $this->escape($rBrand['title']); ?></a>
						<?php else : ?>
							<h5 style="color: black;"><a href="<?php echo JRoute::_('index.php?option=com_citybranding&view=poi&id='.(int) $rBrand['id']); ?>">
								<?php echo $this->escape($rBrand['title']); ?>
							</a></h5>
						<?php endif; ?>

<!--							<a href="<?php /*echo JRoute::_('index.php?option=com_citybranding&view=brand&id='.(int) $rBrand['id']);*/?>">
								(<i class="fa fa-tachometer"></i> <?php /*echo round($rBrand['distance']*1609.344) ;*/?> meters)
							</a>
-->
					</span>
<!--					<?php /*foreach ($rBrand['tags'] as $tag) : */?>
						<span class="cb-tag"><?php /*echo $tag;*/?></span>
					--><?php /*endforeach; */?>

				</div>


			</div>
		</div>
		<?php endforeach; ?>

	</div> <!-- grid2 -->

<?php else : ?>
	<div class="alert alert-info"><h5 style="color: black; text-align: center;">No relative S.E.S. yet</h5></div>
<?php endif; ?>

<?php if($this->item->target != '') : ?>
<h4>Target Customers (Market Segment)</h4>
<p><?php echo $this->item->target;?></p>
<?php endif; ?>

<?php if($this->item->jobs != '') : ?>
<h4>Jobs available</h4>
<p><?php echo $this->item->jobs;?></p>
<?php endif; ?>

<?php if($this->item->equipment != '') : ?>
	<h4>Jobs available</h4>
	<p><?php echo $this->item->equipment;?></p>
<?php endif; ?>

<?php if($this->item->partnership != '') : ?>
	<h4>Jobs available</h4>
	<p><?php echo $this->item->partnership;?></p>
<?php endif; ?>

<?php if($this->item->contact != '') : ?>
<h4>Contact Details</h4>
<div class="alert alert-info"><h5 style="color: black; text-align: center;"><?php echo $this->item->contact;?></h5></div>
<?php endif; ?>

<div style="height: 10em;"></div>