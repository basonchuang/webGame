<html>
    <head>
        <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    </head>
    <body>
        <form onsubmit="return false;" action="./function/config.php?method=studentPickNum" method="POST">
			<input type="hidden" name="studentId" value="<?php echo $_POST['studentId'] ?>">
            <input type="hidden" name="studentName" value="<?php echo $_POST['studentName'] ?>">
            <input type="hidden" name="Sid" value="<?php echo $_POST['Sid'] ?>">
            <input type="hidden" name="Rid" value="1">
            <label>Point:</label>
            <input type="range" name="pickNumber" min="0" max="100" oninput="this.nextElementSibling.value = this.value">
            <output></output>
            <input value="Submit!" type="button" onclick="send(this.form.studentId.value,this.form.studentName.value,this.form.pickNumber.value)">
			<table id='list'></table>
		</form>
    </body>
</html>

<script type="text/javascript">
	if (window.WebSocket){
		console.log("This browser supports WebSocket!");
	} else {
		console.log("This browser does not support WebSocket.");
	}
		var ws = new WebSocket("ws://127.0.0.1:8080");
		ws.onopen = function(){
			console.log('连接成功');
			var data="系统消息：建立连接成功";
			list(data);
			ws.send('11');
		}
		ws.onmessage = function(e){
			var obj=eval("("+e.data+")");
			var data=obj.name+"消息:" + obj.mess;
			list(data);
		}
		ws.onerror = function(){
			var data="出错了，请退出重试";
			list(data);
		}
		function send()
		{
			var msg=document.getElementById("msg").value;
			ws.send(msg);
			// var data="客户端消息："+msg;
			// list(data);
			// document.getElementById("msg").value='';
		}
		function list(data)
		{
			var p=document.createElement("p");
			p.innerHTML=data;
			var box=document.getElementById("list");
			box.appendChild(p);
		}
</script>