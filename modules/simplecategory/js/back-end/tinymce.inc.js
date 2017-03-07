function tinySetup(config)

{

	if(!config)

		config = {};

	//var editor_selector = 'rte';

	//if (typeof config['editor_selector'] !== 'undefined')

		//var editor_selector = config['editor_selector'];

	if (typeof config['editor_selector'] != 'undefined')

		config['selector'] = '.'+config['editor_selector'];



//    safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,xhtmlxtras,preview

	default_config = {

		selector: ".editor" ,

		height: 160,

		plugins : "colorpicker link image paste pagebreak table contextmenu filemanager table code media textcolor deal",

		toolbar1 : "code,|,bold,italic,underline,strikethrough,|,alignleft,aligncenter,alignright,alignfull,formatselect,|,blockquote,colorpicker,pasteword,|,bullist,numlist,|,outdent,indent,|,link,unlink,|,cleanup,|,media,image",

		toolbar2: "",

		external_filemanager_path: ad+"/filemanager/",

		filemanager_title: "File manager" ,

		external_plugins: { "filemanager" : ad+"/filemanager/plugin.min.js"},

		language: iso,
        
        
        
		skin: "prestashop",

		statusbar: false,

		relative_urls : false,

		convert_urls: false,

		extended_valid_elements : "em[class|name|id],deal",
        custom_elements : 'em,deal',
        entity_encoding : "raw",
        //forced_root_block : false,    
        //force_br_newlines : true,    
        //force_p_newlines : false,
        //forced_root_block : 'div',
		menu: {

			edit: {title: 'Edit', items: 'undo redo | cut copy paste | selectall'},

			insert: {title: 'Insert', items: 'media image link | pagebreak'},

			view: {title: 'View', items: 'visualaid'},

			format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},

			table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'},

			tools: {title: 'Tools', items: 'code deal'}

		}

	};


	tinyMCE.PluginManager.add('deal', function(editor, url) {
	    // Add a button that opens a window
		editor.addButton('deal', {
			text: 'Deals',
			icon: true,
			onclick: function() {
				// Open window
				editor.insertContent('{deal}{"limit":"2"}{/deal}');
				/*
				editor.windowManager.open({
					title: 'Deal Shortcode',
					body: [
						{type: 'textbox', name: 'title', label: 'Title'}
					],
					onsubmit: function(e) {
						// Insert content when the window form is submitted
						editor.insertContent('Title: ' + e.data.title);
					}
				});
				*/
			}
		});
	
		// Adds a menu item to the tools menu
		editor.addMenuItem('deal', {
			text: 'Deals',
			context: 'tools',
			onclick: function() {
				editor.insertContent('{deal}{"limit":"2"}{/deal}');
				/*
				// Open window with a specific url
				editor.windowManager.open({
					title: 'TinyMCE site',
					url: 'http://www.tinymce.com',
					width: 400,
					height: 300,
					buttons: [{
						text: 'Close',
						onclick: 'close'
					}]
				});
				*/
			}
		});
	});



	$.each(default_config, function(index, el)

	{

		if (config[index] === undefined )

			config[index] = el;

	});



	tinyMCE.init(config);



};