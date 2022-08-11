<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="/public/favicon.ico" rel="icon" type="image/x-icon">
	<link href="/public/css/bootstrap.min.css" rel="stylesheet">
	<link href="/public/css/mdb.min.css" rel="stylesheet">
	<link href="/public/css/style.css" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
	<title>Уралсиб</title>
	<style>
		table.sort {
			border-spacing: 0.1em;
			margin-bottom: 1em;
			margin-top: 1em;
			font-size: 12px;
		}
		table.sort td {
			border: 1px solid #CCCCCC;
			padding: 0.3em 1em
		}
		table.sort thead td {
			cursor: pointer;
			cursor: hand;
			font-weight: bold;
			text-align: center;
			vertical-align: middle
		}
		table.sort tbody td {
			text-align: left;
		}
		table.sort thead td.curcol {
			background-color: #999999;
			color: #FFFFFF
		}
		thead tr td {
			background: #CCCCCC;
			-moz-user-select: none;
			-webkit-user-select: none;
			-ms-user-select: none;
			-o-user-select: none;
			user-select: none;
		}
		a { color: #000; }
		.new-status { background: #ff9494; }
		.done-status { background: #98e38d; }
		.in-process { background: #87cefa; }
		.unixtime { display: none }
	</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark fixed-top black scrolling-navbar">
		<div class="container">
			<a href="/" class="navbar-brand">
			<img src="/public/images/logo.png" width="30" height="30" alt="logo"> <strong> Уралсиб</strong></a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#basicExampleNav" aria-controls="basicExampleNav" aria-expanded="false" aria-label="Toggle Navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="basicExampleNav">
				<ul class="navbar-nav mr-auto smooth-scroll">
					<li class="nav-item">
						<a href="/stats" class="nav-link waves-effect waves-light">Часовая статистика</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>
	<div style="width:100px; height: 100px;"></div>

	<div class="container">
		<h2 class="mb-3">Общая статистика</h2>
		
		<div class="col-12 mt-3">
			<div class="mt-3">Общее количество уникальных пользователей, которые запустили чат-бот - <b><?= count($users) ?></b></div>
			<div class="mt-3">Общее количество уникальных пользователей, которые запустили Web-app - <b><?= $webapp ?></b></div>
			<div class="mt-3">Общее количество завершенных игр в Web-app - <b><?= $main['play'] ?></b></div>
			<div class="mt-3">Общее количество нажатий на кнопку «Получить приз» - <b><?= $main['prize'] ?></b></div>
		</div>
		
		<h2 class="my-3">Пользователи</h2>
		<div class="col-12 mt-3">
			<table class="sort">
				<thead>
					<tr>
						<td>id</td>
						<td>юзернейм</td>
						<td>лучший результат</td>
						<td>дата и время прохождения</td>
					</tr>
				</thead>
				<tbody id="tbody">
				<? foreach ($users as $user) { ?>
					<tr>
						<td><?= $user['id'] ?></td>
						<td><?= $user['username'] ?></td>
						<td><?= $user['best_result'] ?></td>
						<td><span class="unixtime"><?= (int) $user['time'] ?></span><span><?= $user['time'] !== null ? date('Y.m.d H:i:s', $user['time']) : '' ?></span></td>
					</tr>
				<? } ?>
				</tbody>
			</table>
		</div>
	</div>
	<script type="text/javascript" src="/public/js/jquery-3.4.1.min.js"></script>
	<script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/public/js/mdb.min.js"></script>
	<!-- сортировка таблицы -->
	<script type="text/javascript">
		var sort_case_sensitive = false;

		function _sort(a, b) {
			var a = a[0];
			var b = b[0];
			var _a = (a + '').replace(/,/, '.');
			var _b = (b + '').replace(/,/, '.');
			if (parseFloat(_a) && parseFloat(_b)) return sort_numbers(parseFloat(_a), parseFloat(_b));
			else if (!sort_case_sensitive) return sort_insensitive(a, b);
			else return sort_sensitive(a, b);
		}

		function sort_numbers(a, b) {
			return a - b;
		}

		function sort_insensitive(a, b) {
			var anew = a.toLowerCase();
			var bnew = b.toLowerCase();
			if (anew < bnew) return -1;
			if (anew > bnew) return 1;
			return 0;
		}

		function sort_sensitive(a, b) {
			if (a < b) return -1;
			if (a > b) return 1;
			return 0;
		}

		function getConcatenedTextContent(node) {
			var _result = "";

			if (node == null) {
				return _result;
			}
			var childrens = node.childNodes;
			var i = 0;

			while (i < childrens.length) {
				var child = childrens.item(i);
				switch (child.nodeType) {
					case 1:
					case 5:
						_result += getConcatenedTextContent(child);
						break;
					case 3:
					case 2:
					case 4:
						_result += child.nodeValue;
						break;
					break;
				}
				i++;
			}
			return _result;
		}

		function sort(e) {
			var el = window.event ? window.event.srcElement : e.currentTarget;
			while (el.tagName.toLowerCase() != "td") el = el.parentNode;
			var a = new Array();
			var name = el.lastChild.nodeValue;
			var dad = el.parentNode;
			var table = dad.parentNode.parentNode;
			var up = table.up;
			var node, arrow, curcol;
			for (var i = 0; (node = dad.getElementsByTagName("td").item(i)); i++) {
					if (node.lastChild.nodeValue == name) {
							curcol = i;
							if (node.className == "curcol") {
									arrow = node.firstChild;
									table.up = Number(!up);
							} else {
									node.className = "curcol";
									arrow = node.insertBefore(document.createElement("img"),node.firstChild);
									table.up = 0;
							}
							arrow.alt = "";
					} else {
							if (node.className == "curcol") {
									node.className = "";
									if (node.firstChild) node.removeChild(node.firstChild);
							}
					}
			}
			var tbody = table.getElementsByTagName("tbody").item(0);
			for (var i = 0; (node = tbody.getElementsByTagName("tr").item(i)); i++) {
					a[i] = new Array();
					a[i][0] = getConcatenedTextContent(node.getElementsByTagName("td").item(curcol));
					a[i][1] = getConcatenedTextContent(node.getElementsByTagName("td").item(1));
					a[i][2] = getConcatenedTextContent(node.getElementsByTagName("td").item(0));
					a[i][3] = node;
			}
			a.sort(_sort);
			if (table.up) a.reverse();
			for (var i = 0; i < a.length; i++) {
					tbody.appendChild(a[i][3]);
			}
		}

		function init(e) {
			if (!document.getElementsByTagName) return;

			for (var j = 0; (thead = document.getElementsByTagName("thead").item(j)); j++) {
				var node;
				for (var i = 0; (node = thead.getElementsByTagName("td").item(i)); i++) {
					if (node.addEventListener) node.addEventListener("click", sort, false);
					else if (node.attachEvent) node.attachEvent("onclick", sort);
					node.title = "Нажмите на заголовок, чтобы отсортировать колонку";
				}
				thead.parentNode.up = 0;
				
				if (typeof (initial_sort_id) != "undefined") {
					td_for_event = thead.getElementsByTagName("td").item(initial_sort_id);
					if (document.createEvent) {
						var evt = document.createEvent("MouseEvents");
						evt.initMouseEvent("click", false, false, window, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, td_for_event);
						td_for_event.dispatchEvent(evt);
					} else if (td_for_event.fireEvent) td_for_event.fireEvent("onclick");
					if (typeof (initial_sort_up) != "undefined" && initial_sort_up) {
						if (td_for_event.dispatchEvent) td_for_event.dispatchEvent(evt);
						else if (td_for_event.fireEvent) td_for_event.fireEvent("onclick");
					}
				}
			}
		}

		var root = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;
		if (root) {
			if (root.addEventListener) root.addEventListener("load", init, false);
			else if (root.attachEvent) root.attachEvent("onload", init);
		}
	</script>
</body>
</html>