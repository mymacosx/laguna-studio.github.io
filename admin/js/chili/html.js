{_name:'html',_case:false,_main:{doctype:{_match:/<!DOCTYPE\b[\w\W]*?>/,_style:"color: #CC6600;"},ie_style:{_match:/(<!--\[[^\]]*\]>)([\w\W]*?)(<!\[[^\]]*\]-->)/,_replace:function(all,open,content,close){return"<span class='ie_style'>"+this.x(open)+"</span>"
+this.x(content,'//style')
+"<span class='ie_style'>"+this.x(close)+"</span>";},_style:"color: DarkSlateGray; font-weight: bold;"},comment:{_match:/<!--[\w\W]*?-->/,_style:"color: #4040c2;"},script:{_match:/(<script\s+[^>]*>)([\w\W]*?)(<\/script\s*>)/,_replace:function(all,open,content,close){return this.x(open,'//tag_start')
+this.x(content,'js')
+this.x(close,'//tag_end');}},style:{_match:/(<style\s+[^>]*>)([\w\W]*?)(<\/style\s*>)/,_replace:function(all,open,content,close){return this.x(open,'//tag_start')
+this.x(content,'css')
+this.x(close,'//tag_end');}},tag_start:{_match:/(<\w+)((?:[?%]>|[\w\W])*?)(\/>|>)/,_replace:function(all,open,content,close){return"<span class='tag_start'>"+this.x(open)+"</span>"
+this.x(content,'/tag_attrs')
+"<span class='tag_start'>"+this.x(close)+"</span>";},_style:"color: navy; font-weight: bold;"},tag_end:{_match:/<\/\w+\s*>|\/>/,_style:"color: navy;"},entity:{_match:/&\w+?;/,_style:"color: blue;"}},tag_attrs:{attr:{_match:/(\W*?)([\w-]+)(\s*=\s*)((?:\'[^\']*(?:\\.[^\']*)*\')|(?:\"[^\"]*(?:\\.[^\"]*)*\"))/,_replace:"$1<span class='attr_name'>$2</span>$3<span class='attr_value'>$4</span>",_style:{attr_name:"color: green;",attr_value:"color: maroon;"}}}}