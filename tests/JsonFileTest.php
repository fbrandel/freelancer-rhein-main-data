<?php

class JsonFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider jsonFileProvider
     */
    public function testValidJson($file)
    {
        $json = file_get_contents($file);
        $decoded = json_decode($json);

        $this->assertNotNull($decoded, 'JSON file is not valid');
    }

    /**
     * @dataProvider jsonFileProvider
     */
    public function testHasName($file)
    {
        $json = file_get_contents($file);
        $decoded = json_decode($json);

        $this->assertTrue(isset($decoded->name), 'Name is not set');
        $this->assertNotEmpty($decoded->name, 'Name is empty');
    }

    /**
     * @dataProvider jsonFileProvider
     */
    public function testValidGravatarAddress($file)
    {
        $json = file_get_contents($file);
        $decoded = json_decode($json);

        if (!empty($decoded->profiles->gravatar)) {
            $this->assertContains('@', $decoded->profiles->gravatar, 'Gravatar profile must contain an email address');
        }
    }

    /**
     * @dataProvider jsonFileProvider
     */
    public function testValidProfileUrls($file)
    {
        $json = file_get_contents($file);
        $decoded = json_decode($json);

        foreach ($decoded->profiles as $profile => $url) {
            if ($profile == "gravatar") {
                continue;
            }
            if (!empty($url)) {
                $this->assertTrue((bool) preg_match('/^http(s?):\/\//', $url), "URL $url is not valid");
            }
        }
    }

    public function jsonFileProvider() 
    {
        $data = array();
        $iterator = new DirectoryIterator(__DIR__ . '/../');

        foreach ($iterator as $fileinfo) {
            $fileExtension = pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION);
            if (!$fileinfo->isDot() && $fileExtension == 'json') {
                $data[] = array($fileinfo->getRealPath());
            }
        }

        return $data;
    }
}
?>