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
            <input type="range" id="pickNumber" name="pickNumber" min="0" max="100" oninput="this.nextElementSibling.value = this.value">
            <output></output>
            <input value="Submit!" type="button" onclick="send(this.form.pickNumber.value)">
		</form>
    </body>
</html>
