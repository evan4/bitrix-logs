<?php

namespace lib\Agents;

use Bitrix\Main\SystemException;
use Bitrix\Main\Diag\ExceptionHandlerFormatter;
use Bitrix\Main\Diag\ExceptionHandlerLog;
use Bitrix\Main\Diag;

use Bitrix\Main\Loader;

class Iblock
{
    public static function clearOldLogs()
    {
      global $DB;

      try {
        Loader::includeModule('iblock');
        $iblockId = \lib\Handlers\IBlock::getIblockID('QUARRIES_SEARCH', 'SYSTEM');

        
      } catch (SystemException $exception) {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/mylog.txt';
        $message = (new Diag\LogFormatter())->format("{text}\n{trace}\n{delimiter}\n", [
        'text' => $exception->getMessage(),
        'trace' => Diag\Helper::getBackTrace(20, DEBUG_BACKTRACE_IGNORE_ARGS, 2),
        ]);
        (new Diag\FileLogger($path))->error($message);
      }
    }

    public static function example()
    {
        global $DB;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $iblockId = \Only\Site\Helpers\IBlock::getIblockID('QUARRIES_SEARCH', 'SYSTEM');
            $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('SHORT'));
            $rsLogs = \CIBlockElement::GetList(['TIMESTAMP_X' => 'ASC'], [
                'IBLOCK_ID' => $iblockId,
                '<TIMESTAMP_X' => date($format, strtotime('-1 months')),
            ], false, false, ['ID', 'IBLOCK_ID']);
            while ($arLog = $rsLogs->Fetch()) {
                \CIBlockElement::Delete($arLog['ID']);
            }
        }
        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }
}
