<?php

namespace lib\Handlers;

use \Bitrix\Iblock\IblockTable;
use \Bitrix\Main\Loader;

class Iblock
{
    final protected const IBLOCK_LOG = 'LOG';

    public function __construct()
    {
        Loader::includeModule('iblock');
    }

    public function addLog(int $block_id)
    {
      
      if(!$this->checkBlockExists($block_id)){
        $id_new =  $this->createBlock($block_id);
        var_dump($id_new);
      }

      global $USER;
      
      $parents = implode(' -> ', $this->parentsList($block_id));
      
      // массив с данными для создания элемента
      $dataArray = [
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => $block_id,
        "IBLOCK_SECTION_ID" => self::IBLOCK_LOG,
        "NAME" => $block_id,
        "PREVIEW_TEXT"   => $parents,
        "DETAIL_TEXT" => $parents,
        "PROPERTY_VALUES"=> [],
      ];
      $el = new \CIBlockElement;
      $el->Add($dataArray);
      return;
    }

    public function updateLog(int $block_id)
    {
      if(!$this->checkBlockExists($block_id)){
        $this->createBlock($block_id);
      }
      
      $parents = implode(' -> ', $this->parentsList($block_id));

    }

    static public function OnAfterIBlockElementUpdateHandler(&$arFields)
    {
      $block_id = (int)$arFields['IBLOCK_ID'];

      if((new self)->isBlockLogType($block_id)) return;
      
      (new self)->updateLog($block_id);
    }
    // new element added
    static public function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        $block_id = (int)$arFields['IBLOCK_ID'];

        if((new self)->isBlockLogType($block_id)) return;
        
        (new self)->addLog($block_id);

    }

    private function isBlockLogType(int $block_id)
    {
      $res = \CIBlock::GetByID($block_id);

      if($block = $res->GetNext()){

        if($block['CODE'] === self::IBLOCK_LOG){
          return true;
        }
      }

      return false;
    }

    private function checkBlockExists(int $block_id): bool
    {
        return \CIBlockSection::GetCount([
          "IBLOCK_ID" => $block_id,
          "SECTION_ID" => self::IBLOCK_LOG,
        ]);
    }
    
    private function createBlock(int $block_name)
    {
        $arNewSection = array(
            "IBLOCK_SECTION_ID" => self::IBLOCK_LOG,
            "IBLOCK_ID" => $block_name,
            "NAME" => $block_name,
        );
       
        $bs = new \CIBlockSection();
        $ID = $bs->Add($arNewSection);
        $res = ($ID>0);
        return $ID;
        if(!$res)
          echo $bs->LAST_ERROR;
        return $ID;
    }

    private function parentsList(int $block_id)
    {
      
      $section = $this->getElementSectionsID($block_id);
      $nav = \CIBlockSection::GetNavChain(
        false,
        $section['IBLOCK_ID'],
        [],
      );
      $parents_list = [];
      while($v = $nav->GetNext()) {
          if($v['ID']) $parents_list[] = $v['NAME'];
      }
      array_unshift($parents_list, $section['NAME']);
      return $parents_list;
    }

    private function getElementSectionsID(int $block_id)
    {
      $arSection = \Bitrix\Iblock\SectionTable::getList([
        'filter' => ['IBLOCK_ID' => $block_id],
        'select' => ['IBLOCK_ID', 'NAME']
      ])->fetch();
      return $arSection;
    }
    
}
