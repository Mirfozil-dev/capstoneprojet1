<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<title>Document</title>
</head>
<body>
	<nav class="navbar navbar-expand-lg bg-light">
	  <div class="container">
	    <a class="navbar-brand text-dark" href="index.php">File manager app</a>
	    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
	      <span class="navbar-toggler-icon"></span>
	    </button>
	    <div class="collapse navbar-collapse" id="navbarSupportedContent">
	      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
	        <li class="nav-item">
	          <a class="nav-link active text-dark" aria-current="page" href="index.php">Statistics</a>
	        </li>
	        <li class="nav-item">
	          <a class="nav-link text-dark" href="fileManager.php">File manager</a>
	        </li>
	      </ul>
	    </div>
	  </div>
	</nav>
	<div class="container">
		<h3 class="my-5">File manager statistics</h3>
		<div class="row">
			<div class="col-2 d-flex align-items-center flex-column">
				<p>Total files count</p>
				<h1 id="filesCount"></h5>
			</div>
			<div class="col-2">
				<div style="width: 200px; height: 200px;">
					<canvas id="pieChart"></canvas>
				</div>
			</div>
			<div class="col-4">
				<div style="width: 400px; height: 200px;">
					<canvas id="lineChart"></canvas>
				</div>
			</div>
			<div class="col-4">
				<div style="width: 400px; height: 200px;">
					<canvas id="barChart"></canvas>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script>
		let free_space = 0;
		let total_space = 0;
		let filesCountByDate = {}
		let filesSizeByDate = {}
		let filesCount = 0

		function format_gb(bytes) {
			return (bytes / (1024 * 1024 * 1024)).toFixed(2);
		}
		function format_mb(bytes) {
			return (bytes / (1024 * 1024)).toFixed(2);
		}

		const displayFilesCount = () => {
			for (let i = 0; i <= filesCount; i++) {
				setTimeout(() => {
					document.querySelector('#filesCount').innerText = i
				}, i * 50)
			}
		} 

		const loadData = async () => {
			await fetch('stats.php', {
				headers : { 
					'Content-Type': 'application/json',
					'Accept': 'application/json'
				}
			}).then(r => r.json()).then(d => {
				console.log(d)
				free_space = format_gb(d.free_space)
				total_space = format_gb(d.total_space)
				filesCountByDate = d.files_count_by_date
				filesSizeByDate = d.total_uploaded_file_size
				filesCount = d.files_count
			})
		}

		const setup = async () => {
			await loadData();

			displayFilesCount()

			const pieChart = new Chart(
				document.getElementById('pieChart').getContext("2d"),
				{
					type: 'pie',
					data: {
						labels: [
							'Total space GB',
							'Free space GB',
						],
						datasets: [{
							data: [total_space, free_space],
							backgroundColor: [
								'rgb(255, 99, 132)',
								'rgb(54, 162, 235)',
							],
						}]
					},
				}
			);

			const lineChart = new Chart(
				document.getElementById('lineChart').getContext("2d"),
				{
					type: 'line',
					data: {
						labels: Object.keys(filesCountByDate),
						datasets: [{
							label: 'Number of files uploaded this month by day',
							data: Object.values(filesCountByDate),
							backgroundColor: [
								'rgb(255, 99, 132)',
								'rgb(54, 162, 235)',
							],
						}]
					},
				}
			);
			const barChart = new Chart(
				document.getElementById('barChart').getContext("2d"),
				{
					type: 'bar',
					data: {
						labels: Object.keys(filesSizeByDate),
						datasets: [{
							label: 'Size of files uploaded this month by day in MB',
							data: Object.values(filesSizeByDate).map(size => format_mb(size)),
							backgroundColor: [
								'rgb(255, 99, 132)',
								'rgb(54, 162, 235)',
							],
						}]
					},
				}
			);
		}

		setup();
	</script>
</body>
</html>