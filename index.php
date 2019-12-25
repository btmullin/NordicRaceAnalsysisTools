<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tools for doing analysis of nordic ski race results.">
    <title>Nordic Race Analysis Tools</title>
    
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-" crossorigin="anonymous">
	<link rel="stylesheet" href="css/layouts/side-menu.css">
	<link rel="stylesheet" href="css/nratstyle.css">
	
	<meta property="og:url" content="www.nordicraceanalysis.com/index.php"/>
	<meta property="og:type" content="website"/>
	<meta property="og:title" content="Nordic Race Analysis"/>
	<meta property="og:description" content="Nerdy Tools To Review Your Race Results"/>
	<meta property="og:image" content="http://www.nordicraceanalysis.com/images/compareresultsinstructions.png"/>
</head>


<body id="nrat-home">


<div id="layout">
	<!-- /* Menu here */ -->
	<?php include "nratnav.php"; ?>

	<div id="main">
		<div class="header">
			<h1>Nordic Race Analysis Tools</h1>
			<h2>Nerdy Tools To "Review" Your Race Results</h2>
		</div>

		<div class="container">
			<div class="content">
				<h2 class="content-subhead">Nordic Race Analysis Tools?</h2>
				<div>
				<img style="float:right" src="images/commonresultsexample.png" alt="Example race comparison graph"/>
				<p>
					This is a collection of tools for viewing, reviewing, and analyzing
					race results from predominantly Midwest citizen nordic races.
				</p>
				<p>
					Some of the highlights:
				</p>
				<ul>
					<li><b>Birkie Predictor</b></li>
					<ul>
						<li>Use this to gauge how you might do in this year's Birkie</li>
						<li>This uses the race comparison tool to review your past two years
							of results against the most recent Birkie ('19).</li>
					</ul>
					<li><b>Skier Results Summary</b></li>
					<ul>
						<li>See all of your (or anyone elses) results in a single list</li>
						<li>Plot those race performances over time</li>
						<li>See a distribution of race techniques</li>
					</ul>
					<li><b>Race Performance Comparison</b></li>
					<ul>
						<li>Plot a comparison of results of one race against another</li>
						<li>Predict results in one race based on performance in another</li>
					</ul>
				</ul>
				<div style="clear:right"></div>
				</div>
				
				<h2 class="content-subhead">How To Get Started</h2>
				<p>
					There are two routes to take, if you are interested in a particular
					skier, select either the Birkie Predictor or View Skier Results
					menu items from the left.  From there you can select the skier of
					interest and start exploring the data.
				</p>
				<p>
					The second option is to find the results from a particular
					race to dig into.  Either select View All Events to see the full
					database or View Event Results to select a race from a list.
				</p>
				<p>
					Any of these options will get you started and you can navigate
					around from there.  From an event you can select a racer in the
					results and vice versa.
				</p>

				<div class="pure-g">
					<div class="pure-u-1-3">
					<div class="l-box" style="border:1px solid #eee">
						<a href="images/eventslist.png">
						<img class="pure-img-responsive" src="images/eventslist.png" alt="List of Events">
						</a>
						<p style="font-size:small">View All Events</p>
					</div>
					</div>
					<div class="pure-u-1-3">
					<div class="l-box" style="border:1px solid #eee">
						<a href="images/eventresults.png">
						<img class="pure-img-responsive" src="images/eventresults.png" alt="Event Results">
						</a>
						<p style="font-size:small">View Event Results</p>
					</div>
					</div>
					<div class="pure-u-1-3">
					<div class="l-box" style="border:1px solid #eee">
						<a href="images/racerdetails.png">
						<img class="pure-img-responsive" src="images/racerdetails.png" alt="Racer Details">
						</a>
						<p style="font-size:small">View Skier Details</p>
					</div>
					</div>
				</div>
				
				<p>
					There isn't any magic in those tools though.  The race comparison is where
					either the magic, or BS, happens depending on your point of view. For this
					you select two races and optionally a racer to compare.  The tool then will
					identify racers who did both races and build a mathematical model to compare
					the two races.  If the selected racer did the first race the tool will
					predict their anticipated finish time in the second.  Use this tool to make
					an educated guess of how you would have done at a race you didn't attend, or
					to compare your performance at one race against another (was the predicted time
					or actual time faster).
				</p>
				
				<div class="pure-g">
					<div class="pure-u-1-2">
						<div class="l-box" style="border:1px solid #eee">
							<a href="images/compareresultsinstructions.png">
							<img class="pure-img-responsive" src="images/compareresultsinstructions.png" alt="Compare Results Instructions">
							</a>
						</div>
					</div>
					<div class="pure-u-1-2">
						<div class="l-box" style="border:1px solid #eee">
							<a href="images/compareresultsoutput.png">
							<img class="pure-img-responsive" src="images/compareresultsoutput.png" alt="Compare Results Instructions">
							</a>
						</div>
					</div>
				</div>

				<h2 class="content-subhead">Why?</h2>
				<p>
					Basically what you have here are a set of tools I had built for
					myself over the last few years to do some post race analysis.
					Things like trying to get a handle on if I was getting better,
					was a given race better than another one, how might I have done
					had I showed up to that race I didn't do, etc.
				</p>
				<p>
					It started with an Excel spreadsheet and some VB macros based on
					my understanding of how the City of Lakes Loppet did wave placement
					for folks who hadn't done their race previously.  Over a few years
					I collected a bunch of race results and was able compare my race
					results from year to year beyond just a time which is pretty
					meaningless and placing which is slightly less meaningless.
				</P>
				<p>
					Now I've migrated them to be web based tools.  As an embedded sw
					engineer by training, the web based stuff was a bit of a stretch.
					It certainly should make a true web developer or db admin cringe.
				</p>

				<h2 class="content-subhead">Do I Need These Tools?</h2>
				<p>
					Quite frankly you don't.  But what else are you going to do when
					you aren't racing, training, or waxing your skis?  This is really
					just a pet project of mine that keeps me out of trouble when I'm
					not out doing something more active.
				</p>
				
				<h2 class="content-subhead">Disclaimers</h2>
				<p>
				<ul>
					<li>You can't use these to petition into the Elite Wave of the Birkie</li>
					<li>These results have all be extracted from publicly available sources, but
					should not be considered official, or officially endorsed by any race
					promoter in anyway.</li>
				</ul>
				</p>
			</div>
			
			<!-- /* Sidebar here */ -->
			<?php include "nratsidebar.php"; ?>
		</div>
	</div>
</div>
<script src="ui.js"></script>
</body>
</html>
