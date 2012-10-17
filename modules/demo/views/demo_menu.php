
<li><a href="<?=rpd::url('demo');?>">index</a></li>

<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#">Basic <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="<?php echo rpd::url('basic/hello')?>"><i class="icon-book"></i> Hello World</a></li>
		<li><a href="<?php echo rpd::url('sql/simple_query')?>">Simple SQL</a></li>
		<li><a href="<?php echo rpd::url('sql/query_builder')?>">Query Builder</a></li>
		<li><a href="<?php echo rpd::url('mvc')?>">MVC</a></li>
		<li><a href="<?php echo rpd::url('hmvc')?>">HMVC</a></li>
	</ul>
</li>

<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#">CRUD Widgets <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="<?php echo rpd::url('grid/index')?>">DataGrid</a></li>
		<li><a href="<?php echo rpd::url('filtered_grid/index')?>">DataGrid + DataFilter</a></li>
		<li><a href="<?php echo rpd::url('form/index')?>">DataForm</a></li>
		<li><a href="<?php echo rpd::url('edit/index/show/1')?>">DataEdit</a></li>
		<li><a href="<?php echo rpd::url('edit_grid/article/show/1')?>">DataEdit + DataGrid</a></li>
		<li><a href="<?php echo rpd::url('upload/show')?>">Array Driven DG + Upload</a></li>
	</ul>
</li>

<li class="dropdown">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#">Links <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="http://code.google.com/p/rapyd-framework/w/list">Documentation</a></li>
		<li><a href="http://www.rapyd.com">Rapyd Website</a> </li>
		<li><a href="http://www.rapyd.com/page/support">Donate</a> :(</li>
	</ul>
</li>