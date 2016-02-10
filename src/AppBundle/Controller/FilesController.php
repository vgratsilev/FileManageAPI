<?php
/**
 * Created by PhpStorm.
 * User: Vadim
 * Date: 06.02.2016
 * Time: 2:00
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class FilesController
{
    /**
     * @Route("/files")
     * @Method("GET")
     */
    public function GetFilesList()
    {
        $finder = new Finder();
        $finder->files()->in('../data');
        $array = array();
        foreach ($finder as $file) {
            $array[] = $file->getFilename();
        }
        $response = new Response(json_encode($array));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/files/{fileName}")
     * @Method("GET")
     */
    public function GetFile($fileName)
    {
        $finder = new Finder();
        $finder->files()->name($fileName)->in('../data');

        if ($finder->count() == 0) {
            return new Response("File " . $fileName . " not found", 404);
        }

        $iterator = $finder->getIterator();
        $iterator->rewind();
        $file = $iterator->current();

        $oFile = new File(realpath($file));
        $response = new Response(null);
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', $oFile->getMimeType());
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $oFile->getBasename() . '";');
        $response->headers->set('Content-length', $oFile->getSize());
        $response->setContent(file_get_contents($oFile));
        return $response;
    }

    /**
     * @Route("/files/{fileName}/meta")
     * @Method("GET")
     */
    public function GetFileMeta($fileName)
    {
        $finder = new Finder();
        $finder->files()->name($fileName)->in('../data');
        if ($finder->count() == 0) {
            //throw new FileNotFoundException($fileName);
            return new Response("File " . $fileName . " not found", Response::HTTP_NOT_FOUND);
        }
        $iterator = $finder->getIterator();
        $iterator->rewind();
        $file = $iterator->current();
        $array = [
            "name" => $file->getFileName(),
            "extension" => $file->getExtension(),
            "size" => filesize($file),
            "type" => $file->getType()
        ];
        $response = new Response(json_encode($array));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/files/{fileName}")
     * @Method("POST")
     */
    public function CreateFile($fileName)
    {
        $request = Request::createFromGlobals();
        $content = $request->getContent();
        $fs = new Filesystem();
        $isExist = $fs->exists("../data/" . $fileName);
        if ($isExist) {
            return new Response("File already exist", Response::HTTP_CONFLICT);
        } else {
            try {
                $this->EditFile("../data/" . $fileName, $content);
                $response = new Response("File created", Response::HTTP_CREATED, array("Content-type" => "text/html"));
                return $response;
            } catch (FileException $ex) {
                return new Response("File creating error: " . $ex->getMessage(), Response::HTTP_CONFLICT);
            }
        }
    }

    /**
     * @Route("/files/{fileName}")
     * @Method("PUT")
     */
    public function UpdateFile($fileName)
    {
        $request = Request::createFromGlobals();
        $content = $request->getContent();
        $fs = new Filesystem();
        $isExist = $fs->exists("../data/" . $fileName);
        if (!$isExist) {
            return new Response("Can't update file, it not exist", Response::HTTP_NOT_FOUND);
        } else {
            try {
                $append = $request->query->get("Append");
                switch ($append) {
                    case "true":
                        $this->EditFile("../data/" . $fileName, $content, "a");
                        break;
                    case "false":
                    default:
                        $this->EditFile("../data/" . $fileName, $content);
                        break;
                }
                $response = new Response("File updated", Response::HTTP_OK, array("Content-type" => "text/html"));
                return $response;
            } catch (FileException $ex) {
                return new Response("File updating error: " . $ex->getMessage(), Response::HTTP_NOT_MODIFIED);
            }
        }
    }

    private function EditFile($FileName, $content, $mode = "w", $includePath = "false")
    {
        $file = fopen($FileName, $mode, $includePath);
        fwrite($file, $content);
        fclose($file);
    }
}