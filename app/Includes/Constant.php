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
    public static $REPETITIVE_NATIONAL_CODE = 1107;
    public static $REPETITIVE_PHONE_NUMBER = 1108;
    public static $INVALID_VERIFICATION_CODE = 1109;
    public static $INVALID_PARENT_CODE = 1110;
    public static $USER_NOT_REGISTERED = 1111;
    public static $INVALID_REQUEST = 1112;
    public static $INVALID_EMAIL = 1113;
    public static $INVALID_FILE = 1114;
    public static $SERVER_ISSUE = 1115;
    public static $SHOULD_UPDATE = 1116;
    public static $COUNT_LIMIT = 1117;



    // for get posts function
    public static $TYPE_MEDIA = "media";
    public static $TYPE_PROGRAM = "program";
    public static $TYPE_HTML = "html";
    public static $TYPE_MESSAGE = "message";

    // for notifications
    public static $CATEGORY_ID_POST = 100;
    public static $CATEGORY_ID_MEDIA = 200;
    public static $CATEGORY_ID_PROGRAM = 300;
    public static $CATEGORY_ID_MESSAGE = 400;


}