<?php
/**
 * Created by PhpStorm.
 * User: Vadim
 * Date: 06.02.2016
 * Time: 3:36
 */

namespace Tests\AppBundle\Controller;


class FilesContollerTest extends WebTestCase
{
    public function TestGetFiles()
    {
        $client = static::createClient();

        $resp = $client->getResponse();
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEqual(true, is_array($resp->getContent()));
    }

    public function TestGetFileMeta()
    {
        $client = static::createClient();
        $resp = $client->getResponse();
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEqual(true, is_array($resp->getContent()));
    }

    public function TestCreateSuccess()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/files/TestFileNew.txt',
            array(),
            array(),
            array(),
            "testContent");
        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertContains('File created', $crawler->text());
    }

    public function TestCreateFail()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/files/TestFileExist.txt');
        $this->assertEquals(409, $client->getResponse()->getStatusCode());
    }

    public function TestUpdateSuccess()
    {

    }

    public function TestUpdateFail()
    {

    }
}