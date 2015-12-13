<?php
/**
 * This file contains class::TrainingView
 * @package Runalyze\DataObjects\Training\View
 */

use Runalyze\Configuration;
use Runalyze\View\Activity\Context;
use Runalyze\View\Activity\Linker;

/**
 * Display a training
 * 
 * @author Hannes Christiansen
 * @package Runalyze\DataObjects\Training\View
 */
class TrainingView {
	/**
	 * Sections
	 * @var TrainingViewSection[]
	 */
	protected $Sections = array();

	/**
	 * Toolbar links
	 * @var array
	 */
	protected $ToolbarLinks = array();

	/**
	 * @var \Runalyze\View\Activity\Context
	 */
	protected $Context;

	/**
	 * @param \Runalyze\View\Activity\Context $context
	 */
	public function __construct(Context $context) {
		$this->Context = $context;

		$this->initToolbarLinks();
		$this->initSections();
	}

	/**
	 * Init toolbar links
	 */
	private function initToolbarLinks() {
		$Linker = new Linker($this->Context->activity());

		if ($this->Context->activity()->isPublic()) {
			$this->ToolbarLinks[] = '<a href="'.$Linker->publicUrl().'" target="_blank">'.Icon::$ATTACH.' '.__('Public link').'</a>';
		}

		if (!Request::isOnSharedPage()) {
			$ExporterList = new ExporterList($this->Context);
			$List = $ExporterList->getList();
			if($this->Context->activity()->isPublic()) {
			    $this->ToolbarLinks[] = '<ul><li class="with-submenu"><span class="link"><i class="fa fa-fw fa-share-alt"></i> Share</span><ul class="submenu">';
			    foreach($List[ExporterType::Social] as $t) {
				$Exporter = new $t($this->Context);
				$this->ToolbarLinks[] = '<li><a href="'.$Exporter->getUrl().'" target="_blank" title="'.$Exporter->getInfoText().'"><i class="fa fa-fw '.$Exporter->getIconClass().'"></i>'.$Exporter->getName().'</a></li>';
			    }
			    foreach($List[ExporterType::Code] as $t) {
				$Exporter = new $t($this->Context);
				$this->ToolbarLinks[] = Ajax::window('<li><a href="'.ExporterWindow::$URL.'?id='.$this->Context->activity()->id().'&type='.$t::TYPE.'"><i class="fa fa-fw fa-code"></i>'.$t::TYPE.'</a></li>');
			    }
			    $this->ToolbarLinks[] = '</ul></li></ul>';
			}
			
			$this->ToolbarLinks[] = '<ul><li class="with-submenu"><span class="link"><i class="fa fa-fw fa-download"></i> Export</span><ul class="submenu">';
			foreach($List[ExporterType::File] as $ExportFile) {
			    if($ExportFile::NEEDS_ROUTE == $this->Context->hasRoute() OR $ExportFile::NEEDS_ROUTE == false)
			    $this->ToolbarLinks[] = '<li><a href="'.ExporterWindow::$URL.'?id='.$this->Context->activity()->id().'&type='.strtoupper($ExportFile::EXTENSION).'" title=""><i class="'.$Exporter->getIconClass.'"></i>'.$ExportFile::EXTENSION.'</a></li>';
			}
			$this->ToolbarLinks[] = '</ul></li></ul>';
			$this->ToolbarLinks[] = Ajax::window('<a href="'.$Linker->editUrl().'">'.Icon::$EDIT.' '.__('Edit').'</a> ','small');
		}

		$this->ToolbarLinks[] = Ajax::tooltip($Linker->weekLink(), '<em>'.__('Show week').'</em><br>'.$this->Context->dataview()->weekday().', '.$this->Context->dataview()->dateAndDaytime());
	}

	/**
	 * Init social share Links
	 */
	
	

	/**
	 * Init sections
	 */
	protected function initSections() {

		if (Configuration::ActivityView()->mapFirst() && Configuration::ActivityView()->plotMode()->showCollection()) {

			$this->Sections[] = new SectionComposite($this->Context);
			$this->Sections[] = new SectionLaps($this->Context);

		} else {

			$this->Sections[] = new SectionOverview($this->Context);

			if (Configuration::ActivityView()->mapFirst()) {
				$this->Sections[] = new SectionRouteOnlyMap($this->Context);
			} else
				$this->Sections[] = new SectionLaps($this->Context);
                                $this->Sections[] = new SectionSwimLane($this->Context);

			if (Configuration::ActivityView()->plotMode()->showSeperated()) {
				$this->Sections[] = new SectionHeartrate($this->Context);
				$this->Sections[] = new SectionPace($this->Context);

				if (Configuration::ActivityView()->mapFirst()) {
					$this->Sections[] = new SectionRouteOnlyElevation($this->Context);
				} else
					$this->Sections[] = new SectionRoute($this->Context);

			} else {
				$this->Sections[] = new SectionComposite($this->Context);

				if (Configuration::ActivityView()->plotMode()->showPaceAndHR()) {

					if (Configuration::ActivityView()->mapFirst()) {
						$this->Sections[] = new SectionRouteOnlyElevation($this->Context);
					} else
						$this->Sections[] = new SectionRoute($this->Context);

				} else {
					if (!Configuration::ActivityView()->mapFirst())
						$this->Sections[] = new SectionRouteOnlyMap($this->Context);
				}
			}

			if (Configuration::ActivityView()->mapFirst()) {
				$this->Sections[] = new SectionLaps($this->Context);
			}

		}

		if (
			(
				$this->Context->trackdata()->has(\Runalyze\Model\Trackdata\Entity::CADENCE) &&
				$this->Context->sport()->id() == Configuration::General()->runningSport()
			) ||
			$this->Context->trackdata()->has(\Runalyze\Model\Trackdata\Entity::GROUNDCONTACT) ||
			$this->Context->trackdata()->has(\Runalyze\Model\Trackdata\Entity::VERTICAL_OSCILLATION)
		) {
			$this->Sections[] = new SectionRunningDynamics($this->Context);
			$this->Sections[] = new SectionMiscellaneous($this->Context, false);
		} else {
			$this->Sections[] = new SectionMiscellaneous($this->Context, true);
		}

		if ($this->Context->hasHRV()) {
			$this->Sections[] = new SectionHRV($this->Context);
		}
	}

	/**
	 * Display
	 */
	public function display() {
		$this->displayHeader();
		$this->displaySections();
	}

	/**
	 * Display header
	 */
	protected function displayHeader() {
		echo '<div class="panel-heading">';

		if (!Request::isOnSharedPage()) {
			$this->displayHeaderMenu();
		} else {
			$this->displaySharedMenu();
		}

		echo '<h1>'.$this->Context->dataview()->titleWithComment().'</h1>';

		if (!Request::isOnSharedPage()) {
			$this->displayReloadLink();
		}

		echo '</div>';
	}

	/**
	 * Display header menu
	 */
	protected function displayHeaderMenu() {
		echo '<div class="panel-menu"><ul>';

		foreach ($this->ToolbarLinks as $Link) {
			echo '<li>'.$Link.'</li>';
		}

		echo '</ul></div>';
	}

	/**
	 * Display shared menu
	 */
	protected function displaySharedMenu() {
		$User = AccountHandler::getDataForId(SharedLinker::getUserId());

		$this->ToolbarLinks = array();
		$this->ToolbarLinks[] = SharedLinker::getStandardLinkTo( $this->Context->activity()->id(), Icon::$ATTACH );
		$this->ToolbarLinks[] = '<a href="shared/'.$User['username'].'/" target="_blank">'.Icon::$TABLE.'</a>';

		$this->displayHeaderMenu();
	}

	/**
	 * Display reload link
	 */
	protected function displayReloadLink() {
		echo '<div class="hover-icons"><span class="link" onclick="Runalyze.Statistics.reload();">'.Icon::$REFRESH_SMALL.'</span></div>';
	}

	/**
	 * Display sections
	 */
	protected function displaySections() {
		foreach ($this->Sections as &$Section)
			$Section->display();

		$this->initPlots();
	}

	/**
	 * Init plots
	 */
	protected function initPlots() {
		echo Ajax::wrapJSforDocumentReady( 'RunalyzePlot.resizeTrainingCharts();' );
	}
}
