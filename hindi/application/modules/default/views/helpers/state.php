<?php
class Zend_View_Helper_State extends Zend_View_Helper_Abstract
{

    public function state($id='')
    {
      $st =new Admin_Model_DbTable_BusState();
      
      $data=$st->getState();
      
      foreach($data as $val){
          
          if($id==$val['state_id']){
          echo "<option value='".$val['state_id']."' selected>".$val['state_name']."</option>";
          }else{
              
               echo "<option value='".$val['state_id']."'>".$val['state_name']."</option>";
          }
      }
      
    }
}