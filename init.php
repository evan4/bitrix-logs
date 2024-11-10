<?
use \Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(
	null, // не указываем имя модуля
	[
		// ключ - имя класса, значение - путь относительно корня сайта к файлу с классом
			'\lib\\Handlers\\Iblock' => '/local/php_interface/lib/Handlers/Iblock.php',
	]
);


// регистрируем обработчик
AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("lib\Handlers\Iblock", "OnBeforeIBlockElementAddHandler"));
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("\lib\Handlers\Iblock", "OnAfterIBlockElementUpdateHandler"));

\CAgent::AddAgent(
  "lib\Agents\Iblock::clearOldLogs();", // имя функции
  "",                                  // идентификатор модуля
  "N",                                // period, агент не критичен к кол-ву запусков
  3600,                              // интервал запуска - 1 сутки
);
