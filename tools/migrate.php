<?php

/**
 * @file migrate.php
 *
 * Copyright (c) 2000-2008 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class migrate
 * @ingroup tools
 *
 * @brief CLI tool for migrating OJS 1.x data to OJS 2.
 */

//$Id$

require(dirname(__FILE__) . '/includes/cliTool.inc.php');

import('site.ImportOJS1');

class migrate extends CommandLineTool {

	/** @var $conferencePath string */
	var $conferencePath;

	/** @var $importPath string */
	var $importPath;

	/** @var $options array */
	var $options;

	/**
	 * Constructor.
	 * @param $argv array command-line arguments
	 */
	function migrate($argv = array()) {
		parent::CommandLineTool($argv);

		if (!isset($this->argv[0]) || !isset($this->argv[1])) {
			$this->usage();
			exit(1);
		}

		$this->conferencePath = $this->argv[0];
		$this->importPath = $this->argv[1];
		$this->options = array_slice($this->argv, 2);
	}

	/**
	 * Print command usage information.
	 */
	function usage() {
		echo "OJS 1 -> OJS 2 migration tool (requires OJS >= 1.1.5 and OJS >= 2.0.1)\n"
			. "Use this tool to import data from an OJS 1 system into an OJS 2 system\n\n"
			. "Usage: {$this->scriptName} [conference_path] [ojs1_path] [options]\n"
			. "conference_path      Conference path to create (E.g., \"ojs\")\n"
			. "                  If path already exists, all content except conference settings\n"
			. "                  will be imported into the existing conference\n"
			. "ojs1_path         Complete local filesystem path to the OJS 1 installation\n"
			. "                  (E.g., \"/var/www/ojs\")\n"
			. "options           importRegistrations - import registration type and registrant\n"
			. "                  data\n"
			. "                  verbose - print additional debugging information\n"
			. "                  emailUsers - Email created users with login information\n";
	}

	/**
	 * Execute the import command.
	 */
	function execute() {
		$importer = &new ImportOJS1();
		if ($importer->import($this->conferencePath, $this->importPath, $this->options)) {
			printf("Import completed\n"
					. "Users imported:     %u\n"
					. "Issues imported:    %u\n"
					. "Papers imported:  %u\n",
				$importer->userCount,
				$importer->issueCount,
				$importer->paperCount);
		} else {
			printf("Import failed!\nERROR: %s\n", $importer->error());
		}
	}

}

$tool = &new migrate(isset($argv) ? $argv : array());
$tool->execute();
?>
