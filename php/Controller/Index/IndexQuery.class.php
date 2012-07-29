<?php
class DemoQuery extends Control{
	// 首页
	public function index(){
		$Form	= M("Form");
		// 普通的列表查询
		$list	=	$Form->limit(5)->order('id desc')->select();
		$this->assign('list',$list);
		// 带条件查询
		$condition['id']	=	array('gt',0);
		$condition['status']	=	1;
		$vo	=	$Form->where($condition)->field('id,title')->find();
		$this->assign('vo',$vo);
		// 组合查询
		$map['id']=array('NOT IN','1,6,9');
		$map['title']	= array(array('like','devephp%'),array('like','yearnfar%'),'or');
		$list	=	$Form->where($map)->order('id desc')->limit('0,5')->select();
		$this->assign('list2',$list);
		// 动态查询
		$vo	=	$Form->getByEmail('yearnfar@gmail.com');
		$this->assign('vo2',$vo);
		$this->display();
	}

}
?>
