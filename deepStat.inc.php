<?php

/**
 * @file plugins/blocks/deepStat/deepStat.inc.php
 */



import('lib.pkp.classes.plugins.BlockPlugin');

class deepStat extends BlockPlugin {

	public function getContents($templateMgr, $request = null)
    {
        $templateMgr->assign([
          'madeByText' => 'Made with ❤ by the Public Knowledge Project',
        ]);

        return parent::getContents($templateMgr, $request);
    }



	/**
	 * Install default settings on system install.
	 * @return string
	 */
	function getInstallSitePluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Install default settings on journal creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Get the display name of this plugin.
	 * @return String
	 */
	function getDisplayName() {
		return __('Deep Statistics');
	}

	/**
	 * Get a description of the plugin.
	 */
	function getDescription() {
		return __('Deep Statistics');
	}
}


