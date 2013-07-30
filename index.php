<?php
$dbname='church';
$conID=mysql_pconnect('localhost', 'root', '');
// Get All Table //
$sql='Show Tables From '.$dbname;
$result=@mysql_query($sql, $conID);
$i=0;
while($row=mysql_fetch_row($result)){
	$table[$i]=$row[0];
	$i++;
}
@mysql_free_result($result);
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Hello World</title>
	<meta name='viewport' content='width=device-width' /> 
	<link rel='stylesheet' href='css/foundation.css' />
	<link rel='stylesheet' href='css/general_foundicons.css'>
	<style>
	h1, h2, h3, h4{font-family: Segoe UI, 微軟正黑體;}
	</style>
	<!--[if lt IE 8]>
		<link rel='stylesheet' href='css/general_foundicons_ie7.css'>
  	<![endif]-->
</head>
<body>
<div id='wrapper'>
	<div class='row'>
		<div class='large-12 columns'>
			<h1>Datebase</h1>
			<hr />
		</div>
	</div>
	<div class='row'>
		<div class='large-2 columns'>
			<div class='docs section-container accordion' data-section='accordion' data-options='one_up: false'>
				<section class='section active'>
					<p class='title'><a href='#'>List</a></p>
					<div class='content'>
						<ul class='side-nav'>
<?php for($i=0; $i < count($table); $i++){ ?>	
			<li><a href='#<?php echo $table[$i] ?>'><?php echo $table[$i] ?></a></li>
<?php } ?>
						</ul>
					</div>
				</section>
				<section class='section'>
					<p class='title'><a href='#'>Menu B</a></p>
					<div class='content'>
						<ul class='side-nav'>
							<li><a class='active' href='#'>Sub 1</a></li>
							<li><a class='' href='#'>Sub 2</a></li>
						</ul>
					</div>
				</section>
			</div>
		</div>
		<div class='large-10 columns'>
			<div id='container'></div>
			<div id='page-wrapper'>
				<div id='page-container'></div>
			</div>
		</div>
	</div>
	</div>
	<script src='js/jquery.js'></script>
	<script src='js/underscore-min.js'></script>
	<script src='js/backbone.js'></script>
	<script>
	(function($){
	var getQuery=function(query){
		var queryStr='';
		for(var i in query){
			if(typeof query[i] == 'undefined') continue;
			if(queryStr !== '') queryStr+='&';
			queryStr+=i+'='+query[i];
		}
		return queryStr;
	};		
	jQuery.fn.serializeObject=function() {
		var arrayData, objectData;
		arrayData=this.serializeArray();
		objectData={};
		$.each(arrayData, function(){
			var value;
			if(this.value != null){
				value=this.value;
			} else{
				value='';
			}
			if(objectData[this.name] != null) {
				if(!objectData[this.name].push) {
					objectData[this.name]=[objectData[this.name]];
				}
				objectData[this.name].push(value);
			} else {
				objectData[this.name]=value;
			}
		});
		return objectData;
	};
<?php for($i=0; $i < count($table); $i++){ ?>	
	var <?php echo $table[$i] ?>={};
	<?php echo $table[$i] ?>.Model=Backbone.Model.extend({
		initialize: function(){
			console.log('Model Initialize');
		},
		defaults: {

		},
		idAttribute: '<?php echo $table[$i] ?>_id',
		urlRoot: 'mysql.php',
		url: function(){
			var base=this.urlRoot || (this.collection && this.collection.url) || '/', query=getQuery(this.query)
			if (this.isNew()) return base;
			return base+'?'+query;
		}
	});
	<?php echo $table[$i] ?>.Collection=Backbone.Collection.extend({
		initialize: function(){
			console.log('Collection Initialize');
		},
		model: <?php echo $table[$i] ?>.Model,
		comparator: function(model){
			return model.get();
		}
	});
<?php } ?>
	var CRUD=null;
	CRUD=Backbone.View.extend({
		el: $('#wrapper'),
		container: $('#container'),
		page: {
			el: $('#page-wrapper'),			
			container: $('#page-container'),
			template: 'template/boss_page.html',
			max: 10,
			current: 1
		},
		history: [],
		events: {
			'click .save': 'save',
			'click .mainView': 'render',
			// Page //
			'click .pageView': 'render',
			// Read //
			'click .readView': 'readView',
			// Create //
			'click .createView': 'createView',
			'click .createSubmit': 'createSubmit',
			// Update //
			'click .updateView': 'updateView',
			'click .updateSubmit': 'updateSubmit',
			'click .deleteSubmit': 'deleteSubmit'
		},
		initialize: function(collection, query){
			console.log('View- Initialize');
			var that=this;
			// Get Query //
			for(var i in query) this[i]=query[i]
			this.collection.url='mysql.php?table='+this.table+'&act=read';
			that.collection.fetch({
				success: function(collection, resp){
					that.render();
				}
			});
		},
		render: function(evt){
			console.log('View- Render');
			this.page.el.css('display','block');
			this.page.current=evt?(evt.currentTarget.getAttribute('data-page')?evt.currentTarget.getAttribute('data-page'):1):1;
			var that=this, pageStart=(that.page.current-1)*that.page.max, pageEnd=pageStart+that.page.max, models=[];
			this.container.html('');
			this.container.load(this.template, function(html){
				that.container.html('');
				for(var i=pageStart; i < pageEnd; i++){
					if(!(that.collection).models[i]) break;
					models.push((that.collection).models[i].toJSON());
				}
				that.container.html(_.template(html, {models: models}));
			});
			this.pageView();
		},
		checkField: function(data){
			for(var i in data){
				var obj=$('#'+i), check=null;
				check=obj.attr('data-check');
				if(!check) continue;
				checkArr=check.split(' ');
				for(var j in checkArr){
					if(checkArr[j] == 'null'){
						if(!obj.val()){
							obj.addClass('error')
							return false;
						}
						else{
							if(obj.hasClass('error')) obj.removeClass('error')
						}
					}

				}	
			}
			return true;
		},
		pageView: function(){
			// Page //
			var that=this;
			var pageCounter=Math.ceil(((this.collection).models.length)/this.page.max);
			this.page.container.load(this.page.template, function(html){
				that.page.container.html('');
				for(var i=1; i <= pageCounter; i++){
					that.page.container.html(that.page.container.html()+_.template(html, {page: i}));
				}
			});
			return false;
		},
		createView: function(evt){
			console.log('View- Create');
			this.page.el.css('display','none');
			evt.preventDefault();
			this.container.load(this.templateCreate);
			return false;
		},
		createSubmit: function(evt){
			console.log('View- Create- Submit');
			evt.preventDefault();
			var query={'table': this.table, 'act': 'create'}, data=this.$el.find('form').serializeObject(), model=this.model;
			var check=this.checkField(data);
			if(check){
				if(typeof data[this.idAttribute] == 'undefined'){
					var d=new Date();
					data[this.idAttribute]='temp'+d.getTime();
				}
				// Request Client //
				this.collection.add(data);
				// Save History //
				this.history.push({'model':model, 'query':query, 'data':data});	
				this.render();
			}
			else{
				return false;
			}
		},
		readView: function(evt){
			console.log('View- Read');
			this.page.el.css('display','none'); //Hide Page
			evt.preventDefault();
			var that=this, id=evt.currentTarget.getAttribute('data-id'), where={}, model=null;
			// Get Model By ID //
			where[this.idAttribute]=id;
			model=this.collection.where(where)[0];
			// Get Started //
			this.container.load(this.templateRead, function(html){
				that.container.html('');
				that.container.html(_.template(html, model.toJSON())+that.container.html());
			});
			return false;
		},
		updateView: function(evt){
			console.log('View- Update');
			this.page.el.css('display', 'none'); //Hide Page
			evt.preventDefault();
			var that=this, id=evt.currentTarget.getAttribute('data-id'), where={}, model=null;
			// Get Model By ID //
			where[this.idAttribute]=id;
			model=this.collection.where(where)[0];
			// Get Started //
			this.container.load(this.templateUpdate, function(html){
				console.log(html);
				that.container.html('');
				that.container.html(_.template(html, model.toJSON())+that.container.html());
			});
			return false;
		},
		updateSubmit: function(evt){
			console.log('View- Update- Submit');
			evt.preventDefault();
			var data=this.$el.find('form').serializeObject();
			var query={'table': this.table, 'act':'update', 'where': this.idAttribute+'='+data[this.idAttribute]}, where={}, model=null;
			// Get Model By ID //
			where[this.idAttribute]=data[this.idAttribute];
			model=this.collection.where(where)[0];
			// Request Client //
			model.set(data);
			// Save History //			
			this.history.push({model:model, 'query':query, 'data':data});	
			this.render();
		},	
		deleteSubmit: function(evt){
			console.log('View- Delete');
			evt.preventDefault();
			var id=evt.currentTarget.getAttribute('data-id');
			var query={'table': this.table, 'act': 'delete', 'where': this.idAttribute+'='+id}, where={}, model=null;
			// Get ID //
			where[this.idAttribute]=id;
			model=this.collection.where(where)[0];
			// Request Client //
			model.destroy();
			// Save History //
			this.history.push({'model':model, 'query':query, 'data':where});		
			this.render();
		},
		request: function(model, query, data){
			console.log('View- Request');
			model.query=query;
			model.save(data);
		},
		save: function(){
			console.log('View- Save');
			for(var i in this.history){
				console.log(getQuery(this.history[i].data));
				this.request(this.history[i].model, this.history[i].query, this.history[i].data);
			}
			this.render();
		}
	});
	router=Backbone.Router.extend({
		routes: {
<?php for($i=0; $i < count($table); $i++){ ?>	
			'<?php echo $table[$i] ?>':'<?php echo $table[$i] ?>Router',
<?php } ?>
			'':'defaultRouter'
		},
<?php for($i=0; $i < count($table); $i++){ ?>
		'<?php echo $table[$i] ?>Router': function(){
			var c=new <?php echo $table[$i] ?>.Collection();
			var m=new <?php echo $table[$i] ?>.Model();
			var v=new CRUD({collection: c, model: m},
			{
				table: '<?php echo $table[$i] ?>', 
				idAttribute: '<?php echo $table[$i] ?>_id',
				template: 'template/template.php?act=main&table=<?php echo $table[$i] ?>',
				templateRead: 'template/template.php?act=read&table=<?php echo $table[$i] ?>',
				templateUpdate: 'template/template.php?act=update&table=<?php echo $table[$i] ?>',
				templateCreate: 'template/template.php?act=create&table=<?php echo $table[$i] ?>'
			});
		},
<?php } ?>
		'defaultRouter': function(){
			var c=new boss.Collection();
			var m=new boss.Model();
			var v=new CRUD({collection: c, model: m},
			{
				table: 'boss', 
				idAttribute: 'boss_id',
				template: 'template/template.php?act=main&table=boss',
				templateRead: 'template/template.php?act=read&table=boss',
				templateUpdate: 'template/template.php?act=update&table=boss',
				templateCreate: 'template/template.php?act=create&table=boss'
			});
		}
	})
	var appRouter=new router();
	Backbone.history.start();
	})(jQuery);
	</script> 
	<script src='js/foundation.min.js'></script>
	<script>
		$(document).foundation();
	</script>
</body>
</html>
 