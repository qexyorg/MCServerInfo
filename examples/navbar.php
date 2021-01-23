<div class="navbar bg-gray-50">
	<div class="container">
		<div class="navbar-wrapper">
			<div class="nav-left">
				<a class="nav-brand" href="/examples/index.php">MCServerInfo</a>
				<a href="#" class="nav-mobile"><i class="fa fa-bars"></i></a>
			</div>

			<div class="nav-center"></div>

			<div class="nav-right">
				<ul class="navbar-menu">
					<li class="nav-li">
						<a class="nav-link <?php if($active == 'sample'){ echo 'active'; } ?>" href="/examples/index.php">Auto</a>
					</li>

					<li class="nav-li">
						<a class="nav-link <?php if($active == 'ping'){ echo 'active'; } ?>" href="/examples/ping.php">Ping</a>
					</li>

					<li class="nav-li">
						<a class="nav-link <?php if($active == 'query'){ echo 'active'; } ?>" href="/examples/query.php">Query</a>
					</li>

					<li class="nav-li">
						<a class="nav-link <?php if($active == 'old_ping'){ echo 'active'; } ?>" href="/examples/ping_old.php">Old ping</a>
					</li>

					<li class="nav-li">
						<a class="nav-link nav-sub" href="#" rel="nofollow">Other <i class="fa fa-caret-down"></i></a>
						<ul class="nav-submenu">
							<li class="nav-li">
								<a class="nav-link <?php if($active == 'cached'){ echo 'active'; } ?>" href="/examples/cached.php">Cached</a>
							</li>

							<li>
								<a class="nav-link" href="https://github.com/qexyorg/MCServerInfo" target="_blank" rel="nofollow">GitHub</a>
							</li>

							<li>
								<a class="nav-link" href="#" rel="nofollow">Documentation</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>