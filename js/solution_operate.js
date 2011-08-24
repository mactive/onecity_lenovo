		function searchCRM()
		{			
			var filters = new Object;
			filters.agency_name = document.getElementById("input_agency_name").value;
			filters.contact_name = document.getElementById("input_contact_name").value;

			//var cus = document.getElementById("contact_id");
		  	//filters.contact_id  = cus.value;
		  	//filters.contact_name  = cus.options[cus.selectedIndex].text;
			//filters.agency_id  = document.getElementById("agency_id").value;		      

		  	Ajax.call("solution_operate.php?act=searchCRM", filters, function(result)
		  	{
				if(result.content)
			   	{
					document.getElementById('so_crm_info').innerHTML = result.content;
			    }
		  	}, "GET", "JSON");
		}
		

		function refresh(){
			listTable.loadList();
		}
		
		/* 将订单提交 并通过价格验证*/
		function check_order(orderId)
		{
			var filters = new Object;
			filters.order_id = orderId;
			filters.act = "check_order";
			filters.location_array= location.search.split("&");
			// confirm("{$lang.public_order_yn}")
			if ( 1){
				Ajax.call("solution_operate.php?act=check_order", filters, function(result)
				{
					if(result.content.result == 'pass'){
						confirm_pass(orderId);
					}else{
						if(result.content.low){
							document.getElementById("public_note").innerHTML = '绿色背景的低于销售底价';
							document.getElementById("public_note").style.background = '#9fb769';

							for(var $i=0;$i<result.content.low.length;$i++)
							{
								var span_id  = "step_price_"+result.content.low[$i];
								document.getElementById(span_id).style.background = '#9fb769';	
							}

						}
						else if(result.content.high){
							document.getElementById("public_note").innerHTML = '粉色背景的都应该高于本店售价';	
							document.getElementById("public_note").style.background = '#fc589d';	
							for(var $i=0;$i<result.content.high.length;$i++)
							{
								var span_id  = "step_price_"+result.content.high[$i];
								document.getElementById(span_id).style.background = '#fc589d';	
							}
						}
						
					}
				}, "GET", "JSON");
			}
		}
		
		
		function confirm_pass(orderId){
			var filters = new Object;
			filters.order_id = orderId;
			filters.act = "order_detail";
			filters.location_array= location.search.split("&");
			
			var old_location = location.hostname;
			Ajax.call("solution_operate.php?act=confirm_pass", filters, function(result)
			{
					if(result.content.added_location){
						var new_location = result.content.added_location;
						window.location.assign(new_location);
					}
				}, "GET", "JSON");
		}
		
		/* 打印合同 */
		function print_pdf(orderId){
			var filters = new Object;
			filters.order_id = orderId;
			filters.act = "print_pdf";
			
			Ajax.call("print_pdf.php", filters, function(result)
			{
					
				}, "GET", "JSON");
		}
		
		/* 比较价格并决定是否写入 */
		function check_and_edit(span_obj,step_id,salebase_price,shop_price)
		{
			var span_id  = "step_price_"+step_id;
			var span_value = document.getElementById(span_id).innerHTML;
			//alert(span_value);
			//if(span_value < salebase_price){
			//	alert("to low!");
			//}

			listTable.check_and_edit(span_obj, 'edit_step_price', step_id,salebase_price,shop_price);
		}
		function add_order(orderId)
		{
			var filters = new Object;
			filters.order_id = orderId;
			filters.act = "add_order";
			filters.location_array= location.search.split("&");
			
			var old_location = location.hostname;
			
			Ajax.call("solution_operate.php?act=new_location", filters, function(result)
			{
				if(result.content.added_location){
					var new_location = result.content.added_location;
					window.location.assign(new_location);
				}
				
			}, "GET", "JSON");
		}
		
		
		/* 将订单放入回收站*/
		function trash_order(orderId)
		{
			var filters = new Object;
			filters.order_id = orderId;
			filters.act = "trash_order";
			filters.location_array= location.search.split("&");
			
			var old_location = location.hostname;
			if ( confirm("{$lang.trash_order_yn}") ){
				Ajax.call("solution_operate.php?act=trash_order", filters, function(result)
				{
					if(result.content.added_location){
						var new_location = result.content.added_location;
						window.location.assign(new_location);
					}
				}, "GET", "JSON");
			}
		}
		
		
		function add_step_to_order(goodsId,part_number)
		{			
			var filters = new Object;
			
			if(goodsId > 0){
				var step_price_goods_id  = "step_price_"+goodsId;
				var step_count_goods_id  = "step_count_"+goodsId;
				
				filters.goods_id = goodsId;
			  	filters.part_number = part_number;
			  	filters.step_price = document.getElementById(step_price_goods_id).value;
			  	filters.step_count = document.getElementById(step_count_goods_id).value;
			}
			
		  	
			filters.goods_id = 0 ;
			var cus = document.getElementById("contact_id");
		  	filters.contact_id  = cus.value;
		  	filters.contact_name  = cus.options[cus.selectedIndex].text;
			filters.agency_id  = document.getElementById("agency_id").value;
		  	
		
			filters.location_array= location.search.split("&");
			
			var old_location = location.href;
		  	Ajax.call("solution_operate.php?act=add_step_to_order", filters, function(result)
		  	{
				if(result.content.added_location){
					var new_location = old_location+result.content.added_location;
					window.location.assign(new_location);
				}
		
		  }, "GET", "JSON");


		}
		
		/*修改用户ID*/
		function edit_contact_id(new_id)
		{
				var new_value = document.getElementById("contact_id").value;
				listTable.select_edit(new_id, 'edit_contact_id', {$order_id});
//				listTable.edit(this, 'edit_order_name', {$order_id})
			
		}
		
		//刷新机构下的用户ID*/
		function refresh_agency_contact(new_id)
		{
				var new_value = document.getElementById("contact_id").value;
				listTable.select_edit(new_id, 'refresh_agency_contact', {$order_id},'3');
			
		}
		
		function keep_height()
		{
		  document.getElementById("operate_list").style.height=document.getElementById("operate_area").offsetHeight + "px";

		}
		
		
