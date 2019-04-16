<?php $__env->startSection('home_page_header'); ?>
	<?php echo $__env->make('shopping.common.home_page_header', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
  
<?php $__env->stopSection(); ?>
<?php echo $__env->make('shopping.layout.home_layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>