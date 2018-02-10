<?php

namespace AppBundle\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $pathToProject;
    private $uploadDirectoryFile;

    public function __construct($pathToProject, $uploadDirectoryFile)
    {
        $this->pathToProject = $pathToProject;
        $this->uploadDirectoryFile = $uploadDirectoryFile;
    }

    public function upload(UploadedFile $file, $salt)
    {
        $generatedFileName = time().'_'.$salt.'.'.$file->guessClientExtension();
        $path = $this->pathToProject.'/web'.$this->uploadDirectoryFile;

        $file->move($path, $generatedFileName);

        return $generatedFileName;
    }

    public function getUploadDirectoryPath()
    {
        return $this->pathToProject.'/web'.$this->uploadDirectoryFile;
    }
}