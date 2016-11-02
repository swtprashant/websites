<?php
$db = Zend_Registry::get('db');
$sql="SELECT keyword,value FROM bus_translate where lang='es'";
$result = $db->fetchAll($sql);
$data=array();
foreach($result as $val){
$data[$val['keyword']] = $val['value'];
}
//die('<pre>'.print_r($data,true).'</pre>');
return $data;