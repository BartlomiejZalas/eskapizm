<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class OvicBlockBanner extends Module
{
	public function __construct()
	{
		$this->name = 'ovicblockbanner';
		$this->tab = 'front_office_features';
		$this->version = '1.3.1';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();	

		$this->displayName = $this->l('Fashion - Banner block');
		$this->description = $this->l('Displays a banner(second) at the right of the store.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		return 
			parent::install() && 
			$this->registerHook('displayRightColumn') &&  
			$this->registerHook('actionObjectLanguageAddAfter') &&
			$this->installFixtures() &&
			$this->disableDevice(Context::DEVICE_MOBILE);
	}
	
	public function hookActionObjectLanguageAddAfter($params)
	{
		return $this->installFixture((int)$params['object']->id, Configuration::get('OVIC_BLOCKBANNER_IMG', (int)Configuration::get('PS_LANG_DEFAULT')));
	}
	
	protected function installFixtures()
	{
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang)
			$this->installFixture((int)$lang['id_lang'], 'nbdf.jpg');

		return true;
	}
	
	protected function installFixture($id_lang, $image = null)
	{
		$values['OVIC_BLOCKBANNER_IMG'][(int)$id_lang] = $image;
		$values['OVIC_BLOCKBANNER_LINK'][(int)$id_lang] = '';
		$values['OVIC_BLOCKBANNER_DESC'][(int)$id_lang] = '';
		Configuration::updateValue('OVIC_BLOCKBANNER_IMG', $values['OVIC_BLOCKBANNER_IMG']);
		Configuration::updateValue('OVIC_BLOCKBANNER_LINK', $values['OVIC_BLOCKBANNER_LINK']);
		Configuration::updateValue('OVIC_BLOCKBANNER_DESC', $values['OVIC_BLOCKBANNER_DESC']);
	}

	public function uninstall()
	{
		Configuration::deleteByName('OVIC_BLOCKBANNER_IMG');
		Configuration::deleteByName('OVIC_BLOCKBANNER_LINK');
		Configuration::deleteByName('OVIC_BLOCKBANNER_DESC');
		return parent::uninstall();
	}

	public function hookdisplayRightColumn($params)
	{
		if (!$this->isCached('ovicblockbanner.tpl', $this->getCacheId()))
		{
			$imgname = Configuration::get('OVIC_BLOCKBANNER_IMG', $this->context->language->id);

			if ($imgname && file_exists(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$imgname))
				$this->smarty->assign('banner_img', $this->_path.'img/'.$imgname);

			$this->smarty->assign(array(
				'banner_link' => Configuration::get('OVIC_BLOCKBANNER_LINK', $this->context->language->id),
				'banner_desc' => Configuration::get('OVIC_BLOCKBANNER_DESC', $this->context->language->id)
			));
		}

		return $this->display(__FILE__, 'ovicblockbanner.tpl', $this->getCacheId());
	}

	public function hookDisplayBanner($params)
	{
		return $this->hookdisplayRightColumn($params);
	}
	public function postProcess()
	{
		if (Tools::isSubmit('submitStoreConf'))
		{
			$languages = Language::getLanguages(false);
			$values = array();
			$update_images_values = false;

			foreach ($languages as $lang)
			{
				if (isset($_FILES['OVIC_BLOCKBANNER_IMG_'.$lang['id_lang']])
					&& isset($_FILES['OVIC_BLOCKBANNER_IMG_'.$lang['id_lang']]['tmp_name'])
					&& !empty($_FILES['OVIC_BLOCKBANNER_IMG_'.$lang['id_lang']]['tmp_name']))
				{
					if ($error = ImageManager::validateUpload($_FILES['OVIC_BLOCKBANNER_IMG_'.$lang['id_lang']], 4000000))
						return $this->displayError($this->l('Invalid image'));
					else
					{
						$ext = substr($_FILES['OVIC_BLOCKBANNER_IMG_'.$lang['id_lang']]['name'], strrpos($_FILES['OVIC_BLOCKBANNER_IMG_'.$lang['id_lang']]['name'], '.') + 1);
						$file_name = md5($_FILES['OVIC_BLOCKBANNER_IMG_'.$lang['id_lang']]['name']).'.'.$ext;

						if (!move_uploaded_file($_FILES['OVIC_BLOCKBANNER_IMG_'.$lang['id_lang']]['tmp_name'], dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$file_name))
							return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
						else
						{
							if (Configuration::hasContext('OVIC_BLOCKBANNER_IMG', $lang['id_lang'], Shop::getContext())
								&& Configuration::get('OVIC_BLOCKBANNER_IMG', $lang['id_lang']) != $file_name)
								@unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.Configuration::get('OVIC_BLOCKBANNER_IMG', $lang['id_lang']));

							$values['OVIC_BLOCKBANNER_IMG'][$lang['id_lang']] = $file_name;
						}
					}

					$update_images_values = true;
				}

				$values['OVIC_BLOCKBANNER_LINK'][$lang['id_lang']] = Tools::getValue('OVIC_BLOCKBANNER_LINK_'.$lang['id_lang']);
				$values['OVIC_BLOCKBANNER_DESC'][$lang['id_lang']] = Tools::getValue('OVIC_BLOCKBANNER_DESC_'.$lang['id_lang']);
			}

			if ($update_images_values)
				Configuration::updateValue('OVIC_BLOCKBANNER_IMG', $values['OVIC_BLOCKBANNER_IMG']);

			Configuration::updateValue('OVIC_BLOCKBANNER_LINK', $values['OVIC_BLOCKBANNER_LINK']);
			Configuration::updateValue('OVIC_BLOCKBANNER_DESC', $values['OVIC_BLOCKBANNER_DESC']);

			$this->_clearCache('ovicblockbanner.tpl');
			return $this->displayConfirmation($this->l('The settings have been updated.'));
		}
		return '';
	}

	public function getContent()
	{
		return $this->postProcess().$this->renderForm();
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'file_lang',
						'label' => $this->l('Block image'),
						'name' => 'OVIC_BLOCKBANNER_IMG',
						'desc' => $this->l('You can either upload the image file, or enter its absolute link in the "Image link" option below.'),
						'lang' => true,
					),
					array(
						'type' => 'text',
						'lang' => true,
						'label' => $this->l('Image link'),
						'name' => 'OVIC_BLOCKBANNER_LINK',
						'desc' => $this->l('You can either enter the image\'s absolute link, or upload the image file in the "Block image" option above.')
					),			
					array(
						'type' => 'text',
						'lang' => true,
						'label' => $this->l('Image description'),
						'name' => 'OVIC_BLOCKBANNER_DESC',
						'desc' => $this->l('Please enter a short but meaningful description for the banner.')
					)
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->module = $this;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitStoreConf';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'uri' => $this->getPathUri(),
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		$languages = Language::getLanguages(false);
		$fields = array();

		foreach ($languages as $lang)
		{
			$fields['OVIC_BLOCKBANNER_IMG'][$lang['id_lang']] = Tools::getValue('OVIC_BLOCKBANNER_IMG_'.$lang['id_lang'], Configuration::get('OVIC_BLOCKBANNER_IMG', $lang['id_lang']));
			$fields['OVIC_BLOCKBANNER_LINK'][$lang['id_lang']] = Tools::getValue('OVIC_BLOCKBANNER_LINK_'.$lang['id_lang'], Configuration::get('OVIC_BLOCKBANNER_LINK', $lang['id_lang']));
			$fields['OVIC_BLOCKBANNER_DESC'][$lang['id_lang']] = Tools::getValue('OVIC_BLOCKBANNER_DESC_'.$lang['id_lang'], Configuration::get('OVIC_BLOCKBANNER_DESC', $lang['id_lang']));
		}

		return $fields;
	}
}
