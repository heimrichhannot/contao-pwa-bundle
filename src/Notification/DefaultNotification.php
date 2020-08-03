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
use Contao\Model;
use Contao\System;
use HeimrichHannot\ContaoPwaBundle\Model\PwaPushNotificationsModel;

class DefaultNotification extends AbstractNotification
{
	protected $title;
	protected $body;
	protected $icon;
    /**
     * @var PwaPushNotificationsModel|Model|null
     */
	protected $model;


	/**
	 * DefaultNotification constructor.
	 * @param PwaPushNotificationsModel|null $notificationsModel
	 */
	public function __construct(?PwaPushNotificationsModel $notificationsModel = null)
	{
		if ($notificationsModel)
		{
			 $this->setModel($notificationsModel);
			 $this->setTitle($notificationsModel->title);
			 $this->setBody($notificationsModel->body);
			 if ($notificationsModel->icon)
			 {
				 $this->setIconFromModel($notificationsModel->icon, $notificationsModel->iconSize);
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

	function getModel(): ?PwaPushNotificationsModel
	{
		return $this->model;
	}

	/**
	 * @param mixed $model
	 */
	public function setModel($model): void
	{
		$this->model = $model;
	}

	/**
	 * Set icon from uuid and serialized icon size (as saved in database)
	 *
	 * @param string $icon Icon Uuid
	 * @param string|null $iconSize Serialized size array
	 *
	 * @todo Refactor this out to a factory class
	 */
	public function setIconFromModel(string $icon, string $iconSize = null)
	{
		if ($iconSize)
		{
			$iconSize = deserialize($iconSize);
		}
                       
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');
        $imageFactory = System::getContainer()->get('contao.image.image_factory');
        if (($objModel = FilesModel::findByUuid($icon)) != null && is_file($rootDir . '/' . $objModel->path)) {
            $image = $imageFactory->create($rootDir . '/' . $objModel->path, $iconSize);
            $this->setIcon($image->getUrl($rootDir));
        }
    }
}