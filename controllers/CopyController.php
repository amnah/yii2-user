<?php

namespace amnah\yii2\user\controllers;

use Yii;
use yii\web\HttpException;
use yii\console\Controller;

/**
 * Copy user module to your app/modules folder
 */
class CopyController extends Controller
{
    /**
     * @var string From path
     */
    public $from = "@vendor/amnah/yii2-user";

    /**
     * @var string To path
     */
    public $to = "@app/modules/user";

    /**
     * @var string New namespace of module
     */
    public $namespace = "app\\modules\\user";

    /**
     * @inheritdoc
     */
    public function init()
    {
        // allow console requests only
        if (!Yii::$app->request->isConsoleRequest) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function options($actionId)
    {
        return ['from', 'to', 'namespace'];
    }

    /**
     * Index
     */
    public function actionIndex()
    {
        // define confirm message
        $confirmMsg = "\r\n";
        $confirmMsg .= "Please confirm:\r\n";
        $confirmMsg .= "\r\n";
        $confirmMsg .= "    From        [ $this->from ]\r\n";
        $confirmMsg .= "    To          [ $this->to ]\r\n";
        $confirmMsg .= "    Namespace   [ $this->namespace ]\r\n";
        $confirmMsg .= "\r\n";
        $confirmMsg .= "(yes|no)";

        // confirm copy
        $confirm = $this->prompt($confirmMsg, [
            "required" => true,
            "default"  => "no",
        ]);

        // process copy
        if (strncasecmp($confirm, "y", 1) === 0) {
            // handle aliases and copy files
            $fromPath = Yii::getAlias($this->from);
            $toPath   = Yii::getAlias($this->to);
            $this->copyFiles($fromPath, $toPath, $this->namespace);
        } // display cancellation + usage
        else {
            echo "\r\n";
            echo "--- Copy cancelled! --- \r\n";
            echo "\r\n";
            echo "You can specify the paths using:\r\n\r\n";
            echo "    php yii user/copy --from=@vendor/amnah/yii2-user";
            echo " --to=@app/modules/user --namespace=app\\\\modules\\\\user";
            echo "\r\n";
        }
    }

    /**
     * Copy files from $fromPath to $toPath
     *
     * @param string $fromPath
     * @param string $toPath
     * @param string $namespace
     */
    protected function copyFiles($fromPath, $toPath, $namespace)
    {
        // trim paths
        $fromPath = rtrim($fromPath, "/\\");
        $toPath   = rtrim($toPath, "/\\");

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
            $newFilePath  = str_replace($fromPath, $toPath, $file);
            $relativeFile = str_replace($fromPath, "", $file);

            // get file content and replace namespace
            $content = file_get_contents($file);
            $content = str_replace("amnah\\yii2\\user", $namespace, $content);

            // save and store result
            if (file_exists($newFilePath)) {
                $results[$relativeFile] = "File already exists ... skipping";
            } else {
                $result                 = $this->save($newFilePath, $content);
                $results[$relativeFile] = ($result === true ? "success" : $result);
            }
        }

        print_r($results);
    }

    /**
     * Recursive glob
     * Does not support flag GLOB_BRACE
     *
     * @link http://php.net/glob#106595
     *
     * @param string $pattern
     * @param int    $flags
     * @return array
     */
    protected function glob_recursive($pattern, $flags = 0)
    {
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
    protected function save($path, $content)
    {
        $newDirMode  = 0755;
        $newFileMode = 0644;

        $dir = dirname($path);
        if (!is_dir($dir)) {
            $mask   = @umask(0);
            $result = @mkdir($dir, $newDirMode, true);
            @umask($mask);
            if (!$result) {
                return "Unable to create the directory '$dir'.";
            }
        }
        if (@file_put_contents($path, $content) === false) {
            return "Unable to write the file '{$path}'.";
        } else {
            $mask = @umask(0);
            @chmod($path, $newFileMode);
            @umask($mask);
        }

        return true;
    }
}