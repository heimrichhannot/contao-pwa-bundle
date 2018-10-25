<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\DataContainer;


use Contao\System;
use HeimrichHannot\ContaoPwaBundle\Generator\ManifestGenerator;

class PageContainer
{
	/**
	 * @var ManifestGenerator
	 */
	private $manifestGenerator;


	/**
	 * PageContainer constructor.
	 */
	public function __construct(ManifestGenerator $manifestGenerator)
	{
		$this->manifestGenerator = $manifestGenerator;
	}

	public function oncreateVersionCallback($table, $pid, $version, $row)
	{
		$this->manifestGenerator->generatePageManifest($row);

	}
}