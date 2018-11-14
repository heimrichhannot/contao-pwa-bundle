<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\ContaoPwaBundle\Notification;


use Contao\FilesModel;
use Contao\System;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;

class DefaultNotification extends AbstractNotification
{
	protected $title;
	protected $body;
	protected $icon;
	protected $source;


	/**
	 * DefaultNotification constructor.
	 * @param PwaPushNotificationsModel|null $notificationsModel
	 */
	public function __construct(?PwaPushNotificationsModel $notificationsModel = null)
	{
		if ($notificationsModel)
		{
			 $this->setSource($notificationsModel);
			 $this->setTitle($notificationsModel->title);
			 $this->setBody($notificationsModel->body);
			 if ($notificationsModel->icon)
			 {
				 $this->setIconFromSource($notificationsModel->icon, $notificationsModel->iconSize);
			 }
		}
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param mixed $title
	 */
	public function setTitle($title): void
	{
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param mixed $body
	 */
	public function setBody($body): void
	{
		$this->body = $body;
	}

	/**
	 * @return mixed
	 */
	public function getIcon()
	{
		return $this->icon;
	}

	/**
	 * @param mixed $icon
	 */
	public function setIcon($icon): void
	{
		$this->icon = $icon;
	}

	function getSource(): ?PwaPushNotificationsModel
	{
		return $this->source;
	}

	/**
	 * @param mixed $source
	 */
	public function setSource($source): void
	{
		$this->source = $source;
	}

	/**
	 * Set icon from uuid and serialized icon size (as saved in database)
	 *
	 * @param string $icon Icon Uuid
	 * @param string|null $iconSize Serialized size array
	 *
	 * @todo Refactor this out to a factory class
	 */
	public function setIconFromSource(string $icon, string $iconSize = null)
	{
		if ($iconSize)
		{
			$iconSize = deserialize($iconSize);
		}
		$file = FilesModel::findByUuid($icon);
		$image = System::getContainer()->get('contao.image.image_factory')->create($file->path, $iconSize);
		$this->setIcon($image->getUrl(System::getContainer()->getParameter('kernel.project_dir')));
	}
}