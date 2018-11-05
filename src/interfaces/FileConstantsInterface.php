<?php

namespace aminkt\uploadManager\interfaces;

/**
 * Interface FIleConstantsInterface
 * This interface can use to implement some nessessary constants for File active record but if you want
 * change the default values you can use your own interface.
 *
 * @package aminkt\uploadManager\interfaces
 */
interface FileConstantsInterface {

    const FILE_TYPE_IMAGE = 1;
    const FILE_TYPE_VIDEO = 2;
    const FILE_TYPE_AUDIO = 3;
    const FILE_TYPE_ARCHIVE = 4;
    const FILE_TYPE_DOCUMENT = 5;
    const FILE_TYPE_APPLICATION = 6;
    const FILE_TYPE_UNDEFINED = 7;

    const STATUS_DISABLE = 0;
    const STATUS_ENABLE = 1;
}