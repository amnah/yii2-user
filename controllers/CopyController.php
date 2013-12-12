<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\HttpException;
use yii\console\Controller;

/**
 * CopyController - Copy package to your app/modules folder
 */
class CopyController extends Controller {

    /**
     * @inheritdoc
     */
    public function beforeAction($action) {

        // check if this is being run via console only
        if (!\Yii::$app->request->isConsoleRequest) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        return parent::beforeAction($action);
    }

    /**
     * Index
     */
    public function actionIndex($from = "", $to = "", $namespace = "") {

        // set default values if needed
        $from = trim($from) ?: "@vendor/amnah/yii2-user/amnah/yii2/user";
        $to = trim($to) ?: "@app/modules/user";
        $namespace = trim($namespace) ?: "app\\modules\\user";

        // define confirm message
        $confirmMsg  = "Please confirm:\r\n";
        $confirmMsg .= "    From        [ $from ]\r\n";
        $confirmMsg .= "    To          [ $to ]\r\n";
        $confirmMsg .= "    Namespace   [ $namespace ]\r\n";
        $confirmMsg .= "(yes|no):";

        // confirm copy
        $confirm = $this->prompt($confirmMsg, [
            "required" => true,
            "default" => "no",
        ]);

        // process copy
        if (strncasecmp($confirm, "y", 1) === 0) {
            // handle aliases and copy files
            $fromPath = Yii::getAlias($from);
            $toPath = Yii::getAlias($to);
            $this->copyFiles($fromPath, $toPath, $namespace);
        }
        // display cancellation + usage
        else {
            echo "--- Copy cancelled --- \r\n";
            echo "You can specify the paths using:\r\n";
            echo "    php yii user/copy [from] [to] [namespace]\r\n";
            echo "Example:\r\n";
            echo "    php yii user/copy @vendor/amnah/yii2-user/amnah/yii2/user @app/modules/user app\\\\modules\\\\user";
        }
    }

    /**
     * Copy files from $fromPath to $toPath
     *
     * @param string $fromPath
     * @param string $toPath
     * @param string $namespace
     */
    protected function copyFiles($fromPath, $toPath, $namespace) {

        // trim paths
        $fromPath = rtrim($fromPath, "/\\");
        $toPath = rtrim($toPath, "/\\");

        // get files recursively
        $filePaths = $this->glob_recursive($fromPath . "/*");

        // generate new files
        $results = [];
        foreach ($filePaths as $file) {

            // skip directories
            if (is_dir($file)) {
                continue;
            }

            // calculate new file path and relative file
            $newFilePath = str_replace($fromPath, $toPath, $file);
            $relativeFile = str_replace($fromPath, "", $file);

            // get file content and replace namespace
            $content = file_get_contents($file);
            $content = str_replace("amnah\\yii2\\user", $namespace, $content);

            // save and store result
            if (file_exists($newFilePath)) {
                $results[$relativeFile] = "File already exists ... skipping";
            }
            else {
                $result = $this->save($newFilePath, $content);
                $results[$relativeFile] = ($result === true ? "success" : $result);
            }
        }

        print_r($results);
    }

    /**
     * Recursive glob
     * Does not support flag GLOB_BRACE
     * @link http://php.net/glob#106595
     *
     * @param string $pattern
     * @param int $flags
     * @return array
     */
    protected function glob_recursive($pattern, $flags = 0) {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->glob_recursive($dir . '/' . basename($pattern), $flags));
        }

        return $files;
    }

    /**
     * Saves the code into the file specified by [[path]].
     * Taken/modified from yii\gii\CodeFile
     *
     * @param string $path
     * @param string $content
     * @return string|boolean the error occurred while saving the code file, or true if no error.
     */
    protected function save($path, $content) {

        $newDirMode = 0777;
        $newFileMode = 0666;

        $dir = dirname($path);
        if (!is_dir($dir)) {
            $mask = @umask(0);
            $result = @mkdir($dir, $newDirMode, true);
            @umask($mask);
            if (!$result) {
                return "Unable to create the directory '$dir'.";
            }
        }
        if (@file_put_contents($path, $content) === false) {
            return "Unable to write the file '{$path}'.";
        }
        else {
            $mask = @umask(0);
            @chmod($path, $newFileMode);
            @umask($mask);
        }

        return true;
    }
}
