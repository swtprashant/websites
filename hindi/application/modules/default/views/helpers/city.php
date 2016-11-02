<?php
class Zend_View_Helper_City extends Zend_View_Helper_Abstract
{
        public function City($id='')
        {
            $ci=new Admin_Model_DbTable_BusCity();
            $data=$ci->getCity();
            foreach($data as $val){
             if($id==$val['city_id']){
          echo "<option value='".$val['city_id']."' selected>".$val['city_name']."</option>";
          }else{
              
               echo "<option value='".$val['city_id']."'>".$val['city_name']."</option>";
          }
            }
        }
    
}