<?php
class viewPage extends page
{
    public function __construct($webdriver)
    {
        parent::__construct($webdriver);
        $xpath = array(
            'projectName' => "//*[@id='mainContent']/div[1]/div[1]/div[1]/div[2]/div[1]/span[2]",
            'type' => "//*[@id='mainContent']/div[1]/div[1]/div[1]/div[2]/div[1]/span[3]",
            'acl'  => "//*[@id='mainContent']/div[1]/div[1]/div[1]/div[2]/div[1]/span[5]",
        );
        $this->dom->xpath = array_merge($this->dom->xpath, $xpath);
    }
}
