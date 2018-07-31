<?php
/**
 * Created by PhpStorm.
 * User: nitsarof
 * Date: 6/2/18
 * Time: 1:58 PM
 */

namespace App\Includes;


class Constant
{
    public static $SUCCESS = 1000;
    public static $INVALID_NATIONAL_CODE = 1101;
    public static $INVALID_PASSWORD = 1102;
    public static $INVALID_TOKEN = 1103;
    public static $NO_MORE_POSTS = 1104;
    public static $POST_NOT_EXIST = 1105;
    public static $USER_NOT_EXIST = 1106;

    public static $TYPE_MEDIA = "media";
    public static $TYPE_PROGRAM = "program";
    public static $TYPE_HTML = "html";

    public static $CATEGORY_ID_POST = 100;
    public static $CATEGORY_ID_MEDIA = 200;
    public static $CATEGORY_ID_PROGRAM = 300;


}