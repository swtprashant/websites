<?php
class Zend_View_Helper_Country extends Zend_View_Helper_Abstract
{
    public function Country($id=''){
        $ct=new Admin_Model_DbTable_BusCountry();
        $data=$ct->getCountry();
        foreach($data as $val){
            
             if($id==$val['country_id']){
          echo "<option value='".$val['country_id']."' selected>".$val['country_name']."</option>";
          }else{
              
               echo "<option value='".$val['country_id']."'>".$val['country_name']."</option>";
          }
            
        }
        
    }
}
