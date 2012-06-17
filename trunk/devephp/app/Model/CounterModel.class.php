<?php
class CounterModel extends Model{

	function wgetCount($filename,$num){
		$where['Name'] = $filename;
		$result = $this->where($where)->field('Num')->find();
		if($result){
			$this->where($where)->setField('Num',$result['Num']+1);
		}else{
			$data['Name'] = $filename;
			$data['Num']  = 1;
			$options['table'] = $this->tableName;
			$this->db->insert($data,$options);
		}
	}

}
?>
