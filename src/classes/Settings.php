<?php
class Settings
{
    private $db;
    private $crud;
    private $input;
    private $validator;
    private $_data;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->crud = new Crud();
        $this->input = new Input();
        $this->validator = new Validate();
    }

    function fetchSettings()
    {
        $settings  = $this->crud->getAll('options');
        if ($settings) {
            return $this->_data = $settings;
        }
        return false;
    }

    function fetchSetting($settingID)
    {
        $settings  = $this->crud->getSpecific('options', 'id', '=', $settingID);
        if ($settings) {
            return $this->_data = $settings;
        }
        return false;
    }

    function addSetting($setting)
    {
        if ($this->crud->add('options', $setting)) {
            return true;
        }
        return false;
    }

    function updateSetting($setting, $where = 'id', $settingID)
    {
        if ($this->crud->update('options', $setting, array($where => $settingID))) {
            return true;
        }

        return false;
    }

    function deleteSetting($settingID)
    {
        $result = $this->crud->delete('options', array('id' => $settingID));

        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            return true;
        } else {
            return false;
        }
    }

    function data()
    {
        return $this->_data;
    }
}


class SettingsObject
{
    public $companySettings;
    public $smtpSettings;
    public $companyLogo;
    public $mailResponses;

    public function __construct($settings)
    {
        foreach ($settings as $setting) {
            $optionName = $setting->option_name;
            $this->$optionName = json_decode($setting->option_value);
        }
    }
}